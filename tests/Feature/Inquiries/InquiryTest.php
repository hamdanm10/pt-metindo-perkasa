<?php

declare(strict_types=1);

namespace Tests\Feature\Inquiries;

use App\Livewire\Inquiries\Editor;
use App\Livewire\Inquiries\Index;
use App\Models\Inquiry;
use App\Models\InquiryImage;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use App\Services\Dashboard\DashboardMenu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class InquiryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');
    }

    private function customerId(): string
    {
        return (string) User::factory()->customer()->create()->id;
    }

    private function picture(string $name = 'photo.jpg', int $width = 2400, int $height = 1800): UploadedFile
    {
        return UploadedFile::fake()->image($name, $width, $height);
    }

    private function withImage(Inquiry $inquiry): InquiryImage
    {
        $path = 'inquiries/'.fake()->uuid().'.webp';
        Storage::disk('local')->put($path, 'webp-bytes');

        return $inquiry->images()->create(['path' => $path]);
    }

    public function test_access_by_role(): void
    {
        $inquiry = Inquiry::factory()->create();

        $this->get('/app/inquiries')->assertRedirect(route('app.login'));
        $this->get('/customer/inquiries')->assertRedirect(route('customer.login'));

        foreach (['admin', 'staff'] as $role) {
            $user = User::factory()->{$role}()->create();
            $this->actingAs($user)->get('/app/inquiries')->assertOk();
            $this->actingAs($user)->get("/app/inquiries/{$inquiry->id}")->assertOk();
            $this->actingAs($user)->get('/app/inquiries/create')->assertOk();
            $this->actingAs($user)->get('/customer/inquiries')->assertForbidden();
        }

        $customer = User::factory()->customer()->create();
        $own = Inquiry::factory()->create(['customer_id' => $customer->id]);
        $this->actingAs($customer)->get('/customer/inquiries')->assertOk();
        $this->actingAs($customer)->get("/customer/inquiries/{$own->id}")->assertOk();
        $this->actingAs($customer)->get('/app/inquiries')->assertForbidden();
        $this->actingAs($customer)->get('/app/inquiries/create')->assertForbidden();
        $this->actingAs($customer)->get("/app/inquiries/{$inquiry->id}/edit")->assertForbidden();
    }

    public function test_the_policy_lets_staff_read_all_a_customer_read_their_own_and_only_the_owner_or_admin_change(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $otherStaff = User::factory()->staff()->create();
        $customer = User::factory()->customer()->create();
        $mine = Inquiry::factory()->create(['created_by' => $staff->id]);
        $orphan = Inquiry::factory()->create(['created_by' => null]);

        foreach ([$admin, $staff, $customer] as $user) {
            $this->assertTrue(Gate::forUser($user)->allows('viewAny', Inquiry::class));
        }

        $this->assertTrue(Gate::forUser($admin)->allows('view', $mine));
        $this->assertTrue(Gate::forUser($otherStaff)->allows('view', $mine));
        $this->assertFalse(Gate::forUser($customer)->allows('view', $mine));
        $this->assertTrue(Gate::forUser($customer)->allows('view', Inquiry::factory()->create(['customer_id' => $customer->id])));

        $this->assertTrue(Gate::forUser($admin)->allows('create', Inquiry::class));
        $this->assertTrue(Gate::forUser($staff)->allows('create', Inquiry::class));
        $this->assertFalse(Gate::forUser($customer)->allows('create', Inquiry::class));

        foreach (['update', 'delete'] as $ability) {
            $this->assertTrue(Gate::forUser($admin)->allows($ability, $mine));
            $this->assertTrue(Gate::forUser($admin)->allows($ability, $orphan));
            $this->assertTrue(Gate::forUser($staff)->allows($ability, $mine));
            $this->assertFalse(Gate::forUser($otherStaff)->allows($ability, $mine));
            $this->assertFalse(Gate::forUser($staff)->allows($ability, $orphan));
            $this->assertFalse(Gate::forUser($customer)->allows($ability, $mine));
        }
    }

    public function test_staff_creates_a_record_with_optional_links_and_is_its_author(): void
    {
        $staff = User::factory()->staff()->create();
        $service = Service::factory()->create();
        $product = Product::factory()->create();
        $customer = User::factory()->customer()->create();

        Livewire::actingAs($staff)->test(Editor::class)
            ->set('form.customerId', (string) $customer->id)
            ->set('form.question', ' How long does it take? ')
            ->set('form.answer', 'Three working days.')
            ->set('form.askedAt', '2026-10-01')
            ->set('form.serviceId', (string) $service->id)
            ->set('form.productId', (string) $product->id)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('app.inquiries.index'));

        $inquiry = Inquiry::query()->firstOrFail();
        $this->assertSame('How long does it take?', $inquiry->question);
        $this->assertSame('2026-10-01', $inquiry->asked_at?->toDateString());
        $this->assertSame($staff->id, $inquiry->created_by);
        $this->assertSame($customer->id, $inquiry->customer_id);
        $this->assertSame($service->id, $inquiry->service_id);
        $this->assertSame($product->id, $inquiry->product_id);

        Livewire::actingAs($staff)->test(Editor::class)
            ->set('form.customerId', $this->customerId())
            ->set('form.question', 'Plain question')
            ->set('form.answer', 'Plain answer')
            ->call('save')->assertHasNoErrors();

        $plain = Inquiry::query()->where('question', 'Plain question')->firstOrFail();
        $this->assertNull($plain->service_id);
        $this->assertNull($plain->product_id);
        $this->assertNull($plain->asked_at);
    }

    public function test_the_fields_are_validated(): void
    {
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.askedAt', now()->addDay()->toDateString())
            ->set('form.serviceId', '999999')
            ->set('form.productId', '999999')
            ->call('save');

        $component->assertHasErrors(['form.customerId', 'form.question', 'form.answer', 'form.askedAt', 'form.serviceId', 'form.productId']);
        $this->assertSame(0, Inquiry::query()->count());
    }

    public function test_pictures_are_stored_privately_as_scaled_webp_with_random_names(): void
    {
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.customerId', $this->customerId())
            ->set('form.question', 'Question')
            ->set('form.answer', 'Answer')
            ->set('uploads', [$this->picture('secret-name.jpg'), $this->picture('other.png')]);

        $component->assertHasNoErrors()->call('save')->assertHasNoErrors();

        $inquiry = Inquiry::query()->with('images')->firstOrFail();
        $this->assertCount(2, $inquiry->images);

        foreach ($inquiry->images as $image) {
            $this->assertStringStartsWith('inquiries/', $image->path);
            $this->assertStringEndsWith('.webp', $image->path);
            $this->assertStringNotContainsString('secret-name', $image->path);
            Storage::disk('local')->assertExists($image->path);
            $size = getimagesizefromstring((string) Storage::disk('local')->get($image->path));
            $this->assertLessThanOrEqual(1600, max($size[0] ?? 0, $size[1] ?? 0));
        }

        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_picture_type_and_size_are_checked(): void
    {
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class);

        $component->set('uploads', [UploadedFile::fake()->create('file.pdf', 10, 'application/pdf')])
            ->assertHasErrors(['uploads.0']);
        $component->set('uploads', [UploadedFile::fake()->image('big.jpg', 800, 600)->size(3000)])
            ->assertHasErrors(['uploads.0']);

        $this->assertSame(0, InquiryImage::query()->count());
    }

    public function test_at_most_ten_pictures_per_record(): void
    {
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.customerId', $this->customerId())
            ->set('form.question', 'Question')
            ->set('form.answer', 'Answer')
            ->set('uploads', array_map(fn (int $i): UploadedFile => $this->picture("p{$i}.jpg", 200, 200), range(1, 11)))
            ->assertHasErrors(['form.images']);

        $component->assertCount('form.images', 0)->call('save')->assertHasNoErrors();
        $this->assertSame(1, Inquiry::query()->count());
        $this->assertSame(0, InquiryImage::query()->count());

        $ok = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.customerId', $this->customerId())
            ->set('form.question', 'Question')
            ->set('form.answer', 'Answer')
            ->set('uploads', array_map(fn (int $i): UploadedFile => $this->picture("p{$i}.jpg", 200, 200), range(1, 10)))
            ->call('save')->assertHasNoErrors();

        $this->assertSame(10, InquiryImage::query()->count());
        $ok->assertRedirect();
    }

    public function test_the_limit_counts_the_saved_pictures_and_removing_one_frees_a_slot(): void
    {
        $admin = User::factory()->admin()->create();
        $inquiry = Inquiry::factory()->create(['created_by' => $admin->id]);
        $images = collect(range(1, 10))->map(fn (): InquiryImage => $this->withImage($inquiry));

        $component = Livewire::actingAs($admin)->test(Editor::class, ['inquiry' => $inquiry])
            ->set('uploads', [$this->picture('extra.jpg', 200, 200)])
            ->assertHasErrors(['form.images']);

        $component->call('toggleRemoveImage', $images->firstOrFail()->id)
            ->set('uploads', [$this->picture('extra.jpg', 200, 200)])
            ->assertHasNoErrors()
            ->call('save')->assertHasNoErrors();

        $this->assertSame(10, $inquiry->images()->count());
        $this->assertDatabaseMissing('inquiry_images', ['id' => $images->firstOrFail()->id]);
        Storage::disk('local')->assertMissing($images->firstOrFail()->path);
    }

    public function test_a_waiting_picture_can_be_taken_back_and_removal_can_be_undone(): void
    {
        $admin = User::factory()->admin()->create();
        $inquiry = Inquiry::factory()->create(['created_by' => $admin->id]);
        $saved = $this->withImage($inquiry);

        Livewire::actingAs($admin)->test(Editor::class, ['inquiry' => $inquiry])
            ->set('uploads', [$this->picture('a.jpg', 200, 200), $this->picture('b.jpg', 200, 200)])
            ->assertCount('form.images', 2)
            ->call('removePending', 0)
            ->assertCount('form.images', 1)
            ->call('toggleRemoveImage', $saved->id)
            ->assertSet('form.removedImageIds', [$saved->id])
            ->call('toggleRemoveImage', $saved->id)
            ->assertSet('form.removedImageIds', [])
            ->call('save')->assertHasNoErrors();

        $this->assertSame(2, $inquiry->images()->count());
        Storage::disk('local')->assertExists($saved->path);
    }

    public function test_pictures_of_another_record_cannot_be_removed_through_this_one(): void
    {
        $admin = User::factory()->admin()->create();
        $mine = Inquiry::factory()->create(['created_by' => $admin->id]);
        $other = Inquiry::factory()->create();
        $foreign = $this->withImage($other);

        Livewire::actingAs($admin)->test(Editor::class, ['inquiry' => $mine])
            ->set('form.removedImageIds', [$foreign->id])
            ->call('save')->assertHasNoErrors();

        $this->assertDatabaseHas('inquiry_images', ['id' => $foreign->id]);
        Storage::disk('local')->assertExists($foreign->path);
    }

    public function test_staff_edits_only_their_own_records_and_the_admin_edits_any(): void
    {
        $staff = User::factory()->staff()->create();
        $mine = Inquiry::factory()->create(['created_by' => $staff->id, 'question' => 'Old']);
        $theirs = Inquiry::factory()->create(['created_by' => User::factory()->staff()->create()->id]);

        Livewire::actingAs($staff)->test(Editor::class, ['inquiry' => $theirs])->assertForbidden();
        $this->actingAs($staff)->get("/app/inquiries/{$theirs->id}/edit")->assertForbidden();

        Livewire::actingAs($staff)->test(Editor::class, ['inquiry' => $mine])
            ->set('form.question', 'New')
            ->call('save')->assertHasNoErrors();
        $this->assertSame('New', $mine->refresh()->question);
        $this->assertSame($staff->id, $mine->created_by);

        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class, ['inquiry' => $theirs])
            ->set('form.question', 'By admin')
            ->call('save')->assertHasNoErrors();
        $this->assertSame('By admin', $theirs->refresh()->question);
    }

    public function test_staff_deletes_only_their_own_records_and_the_files_go_with_them(): void
    {
        $staff = User::factory()->staff()->create();
        $mine = Inquiry::factory()->create(['created_by' => $staff->id]);
        $image = $this->withImage($mine);
        $theirs = Inquiry::factory()->create(['created_by' => User::factory()->staff()->create()->id]);

        Livewire::actingAs($staff)->test(Index::class)
            ->call('delete', $theirs->id)->assertForbidden();
        $this->assertDatabaseHas('inquiries', ['id' => $theirs->id]);

        Livewire::actingAs($staff)->test(Index::class)->call('delete', $mine->id);
        $this->assertDatabaseMissing('inquiries', ['id' => $mine->id]);
        $this->assertDatabaseMissing('inquiry_images', ['id' => $image->id]);
        Storage::disk('local')->assertMissing($image->path);

        Livewire::actingAs(User::factory()->admin()->create())->test(Index::class)->call('delete', $theirs->id);
        $this->assertDatabaseMissing('inquiries', ['id' => $theirs->id]);
    }

    public function test_a_customer_only_reads(): void
    {
        $customer = User::factory()->customer()->create();
        $inquiry = Inquiry::factory()->create(['customer_id' => $customer->id]);

        Livewire::actingAs($customer)->test(Index::class)->call('delete', $inquiry->id)->assertForbidden();
        $this->assertDatabaseHas('inquiries', ['id' => $inquiry->id]);

        $this->actingAs($customer)->get('/customer/inquiries')
            ->assertOk()
            ->assertDontSee(route('app.inquiries.create'), false)
            ->assertDontSee('data-confirm="', false)
            ->assertDontSee(route('app.inquiries.edit', $inquiry), false);
    }

    public function test_the_author_is_shown_to_staff_but_not_to_customers(): void
    {
        $author = User::factory()->staff()->create(['name' => 'Sari Wulandari']);
        $customer = User::factory()->customer()->create();
        $inquiry = Inquiry::factory()->create(['created_by' => $author->id, 'customer_id' => $customer->id]);

        $this->actingAs(User::factory()->admin()->create())->get('/app/inquiries')->assertSee('Sari Wulandari');
        $this->actingAs(User::factory()->admin()->create())->get("/app/inquiries/{$inquiry->id}")->assertSee('Sari Wulandari');

        $this->actingAs($customer)->get('/customer/inquiries')->assertDontSee('Sari Wulandari');
        $this->actingAs($customer)->get("/customer/inquiries/{$inquiry->id}")->assertDontSee('Sari Wulandari');
    }

    public function test_the_show_page_has_the_question_answer_links_and_pictures(): void
    {
        $service = Service::factory()->create(['name' => 'Electroplating']);
        $customer = User::factory()->customer()->create();
        $inquiry = Inquiry::factory()->create(['question' => 'Does it rust?', 'answer' => "Line one\nLine two", 'service_id' => $service->id, 'customer_id' => $customer->id]);
        $image = $this->withImage($inquiry);

        $this->actingAs($customer)->get("/customer/inquiries/{$inquiry->id}")
            ->assertOk()
            ->assertSee('Does it rust?')
            ->assertSee('Line one')
            ->assertSee('Electroplating')
            ->assertSee(route('customer.inquiry-images.show', $image), false);
    }

    public function test_pictures_are_served_only_to_signed_in_users_in_their_own_area(): void
    {
        $customer = User::factory()->customer()->create();
        $image = $this->withImage(Inquiry::factory()->create(['customer_id' => $customer->id]));

        $this->get(route('app.inquiry-images.show', $image))->assertRedirect(route('app.login'));
        $this->get(route('customer.inquiry-images.show', $image))->assertRedirect(route('customer.login'));

        $this->actingAs($customer)->get(route('customer.inquiry-images.show', $image))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/webp');
        $this->actingAs($customer)->get(route('app.inquiry-images.show', $image))->assertForbidden();

        $this->actingAs(User::factory()->staff()->create())->get(route('app.inquiry-images.show', $image))->assertOk();
        $this->actingAs(User::factory()->staff()->create())->get(route('customer.inquiry-images.show', $image))->assertForbidden();
        $this->actingAs($customer)->get('/customer/inquiry-images/999999')->assertNotFound();
    }

    public function test_search_filters_and_order(): void
    {
        $service = Service::factory()->create();
        $product = Product::factory()->create();
        Inquiry::factory()->create(['question' => 'Alpha question', 'answer' => 'about paint', 'asked_at' => '2026-01-01']);
        Inquiry::factory()->create(['question' => 'Beta question', 'answer' => 'about zinc', 'asked_at' => '2026-03-01', 'service_id' => $service->id]);
        Inquiry::factory()->create(['question' => 'Gamma question', 'answer' => 'x', 'asked_at' => '2026-02-01', 'product_id' => $product->id]);

        $staff = User::factory()->staff()->create();

        $component = Livewire::actingAs($staff)->test(Index::class);
        $component->assertSeeInOrder(['Beta question', 'Gamma question', 'Alpha question']);
        $component->set('search', 'zinc')->assertSee('Beta question')->assertDontSee('Alpha question')
            ->set('search', '100%')->assertDontSee('Beta question')->assertSee(__('inquiries.empty_filtered'))
            ->set('search', '')->set('service', (string) $service->id)->assertSee('Beta question')->assertDontSee('Gamma question')
            ->set('service', '')->set('product', (string) $product->id)->assertSee('Gamma question')->assertDontSee('Beta question');
    }

    public function test_deleting_a_service_or_product_keeps_the_record(): void
    {
        $service = Service::factory()->create();
        $inquiry = Inquiry::factory()->create(['service_id' => $service->id]);

        $service->delete();

        $this->assertNull($inquiry->refresh()->service_id);
    }

    public function test_the_list_query_count_does_not_grow_with_rows(): void
    {
        $staff = User::factory()->staff()->create();
        $make = function (int $count) use ($staff): void {
            Inquiry::factory()->count($count)->create(['created_by' => $staff->id, 'service_id' => Service::factory()->create()->id, 'product_id' => Product::factory()->create()->id])
                ->each(fn (Inquiry $inquiry) => $this->withImage($inquiry));
        };

        $make(2);
        DB::enableQueryLog();
        Livewire::actingAs($staff)->test(Index::class);
        $few = count(DB::getQueryLog());

        $make(8);
        DB::flushQueryLog();
        Livewire::actingAs($staff)->test(Index::class);

        $this->assertSame($few, count(DB::getQueryLog()));
    }

    public function test_the_form_has_no_placeholders_and_the_date_starts_empty_and_optional(): void
    {
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)->get('/app/inquiries/create')
            ->assertOk();

        $this->assertDoesNotMatchRegularExpression('/<textarea[^>]*placeholder=/', (string) $this->actingAs($staff)->get('/app/inquiries/create')->getContent());

        Livewire::actingAs($staff)->test(Editor::class)
            ->assertSet('form.askedAt', '')
            ->set('form.customerId', $this->customerId())
            ->set('form.question', 'Question')
            ->set('form.answer', 'Answer')
            ->call('save')->assertHasNoErrors();

        $this->assertNull(Inquiry::query()->firstOrFail()->asked_at);
    }

    public function test_a_record_without_a_date_is_listed_last_and_shown_without_one(): void
    {
        $customer = User::factory()->customer()->create();
        $dated = Inquiry::factory()->create(['customer_id' => $customer->id, 'question' => 'Dated question', 'asked_at' => '2026-01-01']);
        $undated = Inquiry::factory()->create(['customer_id' => $customer->id, 'question' => 'Undated question', 'asked_at' => null]);

        $this->actingAs($customer)->get('/customer/inquiries')
            ->assertOk()
            ->assertSeeInOrder(['Dated question', 'Undated question']);
        $this->actingAs($customer)->get("/customer/inquiries/{$undated->id}")->assertOk()->assertDontSee('Asked on');
        $this->actingAs($customer)->get("/customer/inquiries/{$dated->id}")->assertOk()->assertSee('Asked on');

        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class, ['inquiry' => $undated])
            ->assertSet('form.askedAt', '')
            ->call('save')->assertHasNoErrors();
        $this->assertNull($undated->refresh()->asked_at);
    }

    public function test_a_customer_sees_only_their_own_records(): void
    {
        $alice = User::factory()->customer()->create(['name' => 'Alice Customer']);
        $bob = User::factory()->customer()->create(['name' => 'Bob Customer']);
        $mine = Inquiry::factory()->create(['customer_id' => $alice->id, 'question' => 'Alice question']);
        $theirs = Inquiry::factory()->create(['customer_id' => $bob->id, 'question' => 'Bob question']);
        $theirImage = $this->withImage($theirs);

        $this->actingAs($alice)->get('/customer/inquiries')
            ->assertOk()
            ->assertSee('Alice question')
            ->assertDontSee('Bob question');

        $this->actingAs($alice)->get("/customer/inquiries/{$mine->id}")->assertOk();
        $this->actingAs($alice)->get("/customer/inquiries/{$theirs->id}")->assertNotFound();
        $this->actingAs($alice)->get(route('customer.inquiry-images.show', $theirImage))->assertNotFound();

        Livewire::actingAs($alice)->test(Index::class)->assertSee('Alice question')->assertDontSee('Bob question');
        Livewire::actingAs($alice)->test(Index::class)->set('customer', (string) $bob->id)->assertSee('Alice question')->assertDontSee('Bob question');
        $this->assertDatabaseHas('inquiries', ['id' => $theirs->id]);
    }

    public function test_staff_see_every_customer_and_can_filter_by_one(): void
    {
        $alice = User::factory()->customer()->create(['name' => 'Alice Customer']);
        $bob = User::factory()->customer()->create(['name' => 'Bob Customer']);
        Inquiry::factory()->create(['customer_id' => $alice->id, 'question' => 'Alice question']);
        Inquiry::factory()->create(['customer_id' => $bob->id, 'question' => 'Bob question']);

        $component = Livewire::actingAs(User::factory()->staff()->create())->test(Index::class)
            ->assertSee('Alice question')->assertSee('Bob question')->assertSee('Alice Customer');

        $component->set('customer', (string) $bob->id)->assertSee('Bob question')->assertDontSee('Alice question');
    }

    public function test_the_record_must_belong_to_an_existing_customer_account(): void
    {
        $staff = User::factory()->staff()->create();

        foreach (['999999', (string) $staff->id] as $invalid) {
            Livewire::actingAs($staff)->test(Editor::class)
                ->set('form.customerId', $invalid)
                ->set('form.question', 'Question')
                ->set('form.answer', 'Answer')
                ->call('save')->assertHasErrors(['form.customerId']);
        }

        $this->assertSame(0, Inquiry::query()->count());
    }

    public function test_changing_a_record_keeps_or_moves_its_customer(): void
    {
        $admin = User::factory()->admin()->create();
        $inquiry = Inquiry::factory()->create();
        $original = $inquiry->customer_id;
        $other = User::factory()->customer()->create();

        Livewire::actingAs($admin)->test(Editor::class, ['inquiry' => $inquiry])
            ->assertSet('form.customerId', (string) $original)
            ->call('save')->assertHasNoErrors();
        $this->assertSame($original, $inquiry->refresh()->customer_id);

        Livewire::actingAs($admin)->test(Editor::class, ['inquiry' => $inquiry])
            ->set('form.customerId', (string) $other->id)
            ->call('save')->assertHasNoErrors();
        $this->assertSame($other->id, $inquiry->refresh()->customer_id);
    }

    public function test_the_menu_links_the_page_for_every_role(): void
    {
        $menu = new DashboardMenu;

        $this->assertContains('app.inquiries.index', array_column($menu->forUser(User::factory()->admin()->create()), 'route'));
        $this->assertContains('app.inquiries.index', array_column($menu->forUser(User::factory()->staff()->create()), 'route'));
        $this->assertContains('customer.inquiries.index', array_column($menu->forUser(User::factory()->customer()->create()), 'route'));
        $this->assertNotContains('app.inquiries.index', array_column($menu->forUser(User::factory()->customer()->create()), 'route'));
    }

    public function test_the_texts_exist_in_both_dashboard_languages(): void
    {
        $en = require lang_path('en/inquiries.php');
        $id = require lang_path('id/inquiries.php');

        $this->assertSame(array_keys($en), array_keys($id));
        $this->assertSame(array_keys($en['fields']), array_keys($id['fields']));
        $this->assertSame(array_keys($en['messages']), array_keys($id['messages']));
        $this->assertArrayHasKey('inquiries', (require lang_path('en/dashboard.php'))['nav']);
        $this->assertArrayHasKey('inquiries', (require lang_path('id/dashboard.php'))['nav']);
        $this->assertArrayHasKey('support', (require lang_path('id/dashboard.php'))['groups']);
    }
}
