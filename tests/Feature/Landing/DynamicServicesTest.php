<?php

declare(strict_types=1);

namespace Tests\Feature\Landing;

use App\Actions\Services\SaveService;
use App\Models\Service;
use App\Services\Landing\LandingServices;
use App\Services\Services\DefaultServices;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DynamicServicesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Storage::fake('public');
    }

    /**
     * @param  array<string, array{title: string, summary?: string, description?: string, included?: list<string>, fit?: string}>  $texts
     */
    private function service(string $slug, array $texts, int $position = 0, bool $active = true): Service
    {
        $translations = [];

        foreach ($texts as $locale => $text) {
            $translations[$locale] = [
                'title' => $text['title'],
                'summary' => $text['summary'] ?? '',
                'description' => $text['description'] ?? '',
                'included' => $text['included'] ?? [],
                'fit' => $text['fit'] ?? '',
            ];
        }

        return app(SaveService::class)->execute(null, $slug, $slug, $position, $active, $translations);
    }

    public function test_without_services_the_menu_home_and_pages_have_nothing_to_show(): void
    {
        $this->get('/en')->assertOk()
            ->assertDontSee('data-services-track', false)
            ->assertDontSee(route('landing.services.index', ['locale' => 'en']), false);

        $this->get('/en/services')->assertNotFound();
        $this->get('/en/services/anything')->assertNotFound();
    }

    public function test_services_follow_the_admin_order_and_hide_inactive_ones(): void
    {
        $this->service('second', ['en' => ['title' => 'Second one']], position: 2);
        $this->service('first', ['en' => ['title' => 'First one']], position: 1);
        $this->service('hidden', ['en' => ['title' => 'Hidden one']], position: 0, active: false);

        $this->get('/en')->assertSeeInOrder(['First one', 'Second one'])->assertDontSee('Hidden one');
        $this->get('/en/services')->assertOk()->assertSeeInOrder(['First one', 'Second one'])->assertDontSee('Hidden one');
        $this->get('/en/services/hidden')->assertNotFound();
        $this->get('/en/services/first')->assertOk()->assertSee('Second one');
        $this->get('/en')->assertSee(route('landing.services.index', ['locale' => 'en']), false);
    }

    public function test_a_language_without_text_shows_english_and_one_with_text_shows_its_own(): void
    {
        $this->service('plating', [
            'en' => ['title' => 'English title', 'summary' => 'English summary'],
            'id' => ['title' => 'Judul Indonesia', 'summary' => 'Ringkasan Indonesia'],
        ]);

        $this->get('/id/services/plating')->assertOk()->assertSee('Judul Indonesia')->assertSee('Ringkasan Indonesia')->assertDontSee('English title');
        $this->get('/ja/services/plating')->assertOk()->assertSee('English title')->assertSee('English summary');
        $this->get('/ja')->assertSee('English title');
    }

    public function test_a_service_without_english_or_without_an_address_is_not_shown(): void
    {
        $this->service('only-id', ['id' => ['title' => 'Hanya Indonesia']]);
        $noSlug = $this->service('no-slug', ['en' => ['title' => 'No address']]);
        $noSlug->update(['slug' => null]);
        app(LandingServices::class)->flush();

        $this->get('/en/services')->assertNotFound();
        $this->get('/id/services')->assertOk()->assertSee('Hanya Indonesia');
        $this->assertSame([], app(LandingServices::class)->all('en'));
    }

    public function test_the_detail_page_leaves_out_empty_parts(): void
    {
        $this->service('bare', ['en' => ['title' => 'Bare service']]);
        $this->service('full', ['en' => ['title' => 'Full service', 'description' => 'Long text.', 'included' => ['One', 'Two'], 'fit' => 'Everyone']]);

        $this->get('/en/services/bare')->assertOk()
            ->assertDontSee(e(__('pages.services.detail.included', [], 'en')), false)
            ->assertDontSee(__('pages.services.detail.fit', [], 'en'));

        $this->get('/en/services/full')->assertOk()
            ->assertSee('Long text.')->assertSee('One')->assertSee('Two')->assertSee('Everyone')
            ->assertSee(e(__('pages.services.detail.included', [], 'en')), false);
    }

    public function test_the_only_service_has_no_other_services_section(): void
    {
        $this->service('alone', ['en' => ['title' => 'Alone']]);

        $this->get('/en/services/alone')->assertOk()->assertDontSee(__('pages.services.others', [], 'en'));
    }

    public function test_text_from_the_admin_is_escaped(): void
    {
        $this->service('xss', ['en' => ['title' => '<script>alert(1)</script>']]);

        $this->get('/en/services/xss')->assertOk()->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_the_landing_sees_a_saved_change_at_once(): void
    {
        $service = $this->service('live', ['en' => ['title' => 'Before']]);
        $this->get('/en/services/live')->assertSee('Before');

        app(SaveService::class)->execute($service, 'live', 'live', 0, true, [
            'en' => ['title' => 'After', 'summary' => '', 'description' => '', 'included' => [], 'fit' => ''],
        ]);

        $this->get('/en/services/live')->assertSee('After')->assertDontSee('Before');
    }

    public function test_the_uploaded_picture_is_used(): void
    {
        $service = $this->service('pic', ['en' => ['title' => 'Pic']]);
        $service->update(['image_path' => 'services/abc.webp']);
        app(LandingServices::class)->flush();

        $this->get('/en/services')->assertSee(Storage::disk('public')->url('services/abc.webp'), false);
    }

    public function test_the_query_count_does_not_grow_with_the_number_of_services(): void
    {
        $this->service('one', ['en' => ['title' => 'One']]);
        DB::enableQueryLog();
        app(LandingServices::class)->all('en');
        $few = count(DB::getQueryLog());

        app(LandingServices::class)->flush();
        foreach (range(1, 10) as $number) {
            $this->service("more-{$number}", ['en' => ['title' => "More {$number}"]]);
        }
        app(LandingServices::class)->flush();
        DB::flushQueryLog();
        app(LandingServices::class)->all('en');

        $this->assertSame($few, count(DB::getQueryLog()));
    }

    public function test_the_default_services_import_the_old_texts_once(): void
    {
        $this->assertSame(3, app(DefaultServices::class)->import());

        $this->assertSame(0, app(DefaultServices::class)->import());
        $this->assertSame(3, Service::query()->count());

        $service = Service::query()->where('slug', 'chemical-supply')->with('translations')->firstOrFail();
        $this->assertSame(config()->array('locales.landing'), $service->translations->pluck('locale')->sort(fn ($a, $b) => array_search($a, config()->array('locales.landing'), true) <=> array_search($b, config()->array('locales.landing'), true))->values()->all());
        $this->assertSame(__('home.services.items.chemical_supply.title', [], 'ja'), $service->translations->firstWhere('locale', 'ja')?->title);
        $this->assertCount(5, $service->translations->firstWhere('locale', 'en')->included ?? []);
        $this->assertNotNull($service->image_path);
        Storage::disk('public')->assertExists((string) $service->image_path);
    }

    public function test_the_import_command_keeps_the_admins_changes(): void
    {
        Artisan::call('services:import-defaults');
        Service::query()->where('slug', 'chemical-supply')->firstOrFail()->translations()->where('locale', 'en')->update(['title' => 'Changed by admin']);

        Artisan::call('services:import-defaults');

        $this->assertSame(3, Service::query()->count());
        $this->assertStringContainsString('Added 0 service(s).', Artisan::output());
        $this->get('/en/services/chemical-supply')->assertSee('Changed by admin');
    }
}
