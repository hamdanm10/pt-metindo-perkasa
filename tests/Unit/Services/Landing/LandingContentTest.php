<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Landing;

use App\Models\ArticleTranslation;
use App\Services\Articles\LandingArticles;
use App\Services\Articles\ThumbnailStore;
use App\Services\Landing\LandingContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingContentTest extends TestCase
{
    use RefreshDatabase;

    private function content(): LandingContent
    {
        return new LandingContent(new LandingArticles(new ThumbnailStore));
    }

    public function test_it_finds_a_category_by_slug(): void
    {
        $this->assertSame('Zinc Plating Chemicals', $this->content()->category('zinc-plating')['name'] ?? null);
        $this->assertNull($this->content()->category('unknown'));
    }

    public function test_sample_content_is_empty_when_the_flag_is_off(): void
    {
        config(['landing.sample_content' => false]);

        $this->assertSame([], $this->content()->subsidiaries());
    }

    public function test_menu_lists_articles_only_for_a_language_with_a_published_article(): void
    {
        config(['landing.sample_content' => false]);

        $this->assertNotContains('landing.articles.index', array_column($this->content()->menu(), 'route'));

        ArticleTranslation::factory()->published('en')->create();
        app(LandingArticles::class)->flush();

        app()->setLocale('en');
        $this->assertContains('landing.articles.index', array_column($this->content()->menu(), 'route'));

        app()->setLocale('id');
        $this->assertNotContains('landing.articles.index', array_column($this->content()->menu(), 'route'));
    }
}
