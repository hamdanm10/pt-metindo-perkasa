<?php

declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Livewire\Services\Index;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PaginationViewTest extends TestCase
{
    use RefreshDatabase;

    private function page(string $locale = 'en'): string
    {
        Service::query()->exists() || Service::factory()->count(20)->create();
        $admin = User::factory()->admin()->create(['locale' => $locale]);
        app()->setLocale($locale);

        return Livewire::actingAs($admin)->test(Index::class)->html();
    }

    public function test_the_pager_uses_the_dashboard_colours_and_none_of_the_default_ones(): void
    {
        $html = $this->page();

        $this->assertStringContainsString('border-brand-700 bg-brand-700 text-white', $html);
        $this->assertStringContainsString('aria-current="page"', $html);

        // The default view brings grey, blue, and dark-mode classes that clash with the dashboard.
        $this->assertDoesNotMatchRegularExpression('/\b(?:text|bg|border|ring)-(?:gray|blue)-\d{2,3}\b/', $html);
        $this->assertStringNotContainsString('dark:', $html);
    }

    public function test_the_pager_texts_are_translated_to_the_dashboard_language(): void
    {
        $this->assertStringContainsString('Showing 1 to 15 of 20 results', preg_replace('/\s+/', ' ', strip_tags($this->page('en'))) ?? '');
        $this->assertStringContainsString('Menampilkan 1 sampai 15 dari 20 hasil', preg_replace('/\s+/', ' ', strip_tags($this->page('id'))) ?? '');
    }

    public function test_the_first_previous_button_is_disabled_and_the_pager_is_hidden_for_one_page(): void
    {
        $html = $this->page();

        $this->assertStringContainsString('aria-disabled="true"', $html);
        $this->assertStringContainsString('wire:click="nextPage(', $html);
        $this->assertStringNotContainsString('wire:click="previousPage(', $html);
    }

    public function test_there_is_no_pager_when_everything_fits_on_one_page(): void
    {
        Service::factory()->count(3)->create();
        $html = Livewire::actingAs(User::factory()->admin()->create())->test(Index::class)->html();

        $this->assertStringNotContainsString('role="navigation"', $html);
    }

    public function test_the_texts_exist_in_both_dashboard_languages(): void
    {
        $en = (require lang_path('en/dashboard.php'))['pagination'];
        $id = (require lang_path('id/dashboard.php'))['pagination'];

        $this->assertSame(array_keys($en), array_keys($id));
    }
}
