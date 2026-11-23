<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Landing;

use App\Models\ArticleTranslation;
use App\Services\Articles\LandingArticles;
use App\Services\Landing\LandingContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesCatalog;
use Tests\TestCase;

class LandingContentTest extends TestCase
{
    use CreatesCatalog;
    use RefreshDatabase;

    private function content(): LandingContent
    {
        return app(LandingContent::class);
    }

    public function test_it_finds_a_category_by_slug(): void
    {
        $this->createCatalog();

        $this->assertSame('Zinc Plating Chemicals', $this->content()->category('zinc-plating')['name'] ?? null);
        $this->assertNull($this->content()->category('unknown'));
    }

    public function test_the_locations_come_from_the_configuration(): void
    {
        $this->assertSame(config('landing.subsidiaries'), $this->content()->locations());
    }

    public function test_menu_lists_articles_only_for_a_language_with_a_published_article(): void
    {
        $this->assertNotContains('landing.articles.index', array_column($this->content()->menu(), 'route'));

        ArticleTranslation::factory()->published('en')->create();
        app(LandingArticles::class)->flush();

        app()->setLocale('en');
        $this->assertContains('landing.articles.index', array_column($this->content()->menu(), 'route'));

        app()->setLocale('id');
        $this->assertNotContains('landing.articles.index', array_column($this->content()->menu(), 'route'));
    }
}
