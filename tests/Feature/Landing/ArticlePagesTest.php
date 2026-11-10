<?php

declare(strict_types=1);

namespace Tests\Feature\Landing;

use App\Models\ArticleTranslation;
use App\Services\Articles\ArticleBody;
use App\Services\Articles\LandingArticles;
use App\Services\Articles\SampleArticles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticlePagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        config(['landing.sample_content' => true]);
        app(LandingArticles::class)->flush();
    }

    public function test_sample_articles_are_newest_first_and_use_the_supplied_image(): void
    {
        $summaries = app(SampleArticles::class)->summaries();

        $this->assertCount(5, $summaries);
        $this->assertSame(array_column($summaries, 'date'), collect(array_column($summaries, 'date'))->sortDesc()->values()->all());

        foreach ($summaries as $summary) {
            $this->assertTrue($summary['sample']);
            $this->assertStringEndsWith('images/landing/articles/sample-business.webp', (string) $summary['image']);
            $this->assertGreaterThanOrEqual(1, $summary['minutes']);
        }

        $this->assertFileExists(public_path('images/landing/articles/sample-business.webp'));
        $this->assertSame(['article', 'news'], collect($summaries)->pluck('type')->unique()->sort()->values()->all());
    }

    public function test_the_list_shows_the_newest_large_the_rest_as_cards_and_counts_the_filters(): void
    {
        $samples = app(SampleArticles::class)->summaries();
        $html = (string) $this->get('/en/articles')->getContent();

        $this->assertStringContainsString('data-art-featured', $html);
        $this->assertStringContainsString($samples[0]['title'], $html);
        $this->assertSame(4, substr_count($html, 'class="group flex h-full flex-col'));
        $this->assertStringContainsString(__('landing.sample_note'), $html);

        foreach (array_slice($samples, 1) as $sample) {
            $this->assertStringContainsString('href="'.route('landing.articles.show', ['locale' => 'en', 'article' => $sample['slug']]).'"', $html);
        }

        $articles = count(array_filter($samples, fn (array $item): bool => $item['type'] === 'article'));
        $this->assertMatchesRegularExpression('/'.preg_quote(__('pages.articles.filters.article'), '/').'\s*<span[^>]*>\s*'.$articles.'\s*</', $html);
    }

    public function test_the_filter_keeps_only_that_type(): void
    {
        $news = array_values(array_filter(app(SampleArticles::class)->summaries(), fn (array $item): bool => $item['type'] === 'news'));
        $article = array_values(array_filter(app(SampleArticles::class)->summaries(), fn (array $item): bool => $item['type'] === 'article'));

        $this->get('/en/articles?type=news')->assertOk()
            ->assertSee($news[0]['title'])
            ->assertDontSee($article[0]['title']);
    }

    public function test_a_sample_article_opens_in_a_narrow_reading_column_with_progress_share_and_related(): void
    {
        $sample = app(SampleArticles::class)->summaries()[0];
        $html = (string) $this->get('/en/articles/'.$sample['slug'])->assertOk()->getContent();

        $this->assertStringContainsString('<h1', $html);
        $this->assertStringContainsString($sample['title'], $html);
        $this->assertStringContainsString('max-w-[44.5rem]', $html);
        $this->assertStringContainsString('class="article-body reading"', $html);
        $this->assertStringContainsString('data-read-progress', $html);
        // Nothing in the content is animated, the picture included, and the picture sits in the same narrow column.
        $this->assertStringNotContainsString('data-read-head', $html);
        $this->assertStringNotContainsString('data-read-image', $html);
        $this->assertStringNotContainsString('max-w-[60rem]', $html);
        $this->assertSame(2, substr_count($html, 'max-w-[44.5rem]'));
        $this->assertStringContainsString('data-copy-link="'.url('/en/articles/'.$sample['slug']).'"', $html);
        $this->assertStringContainsString('https://wa.me/?text=', $html);
        $this->assertStringContainsString(__('landing.sample_note'), $html);
        $this->assertStringContainsString(__('articles.minutes', ['count' => $sample['minutes']]), $html);
        $this->assertSame(3, substr_count($html, 'class="group flex h-full flex-col'));
        $this->assertStringContainsString('<meta property="og:image"', $html);

        foreach (config('locales.landing') as $code) {
            $this->assertStringContainsString('hreflang="'.config("locales.locales.{$code}.html_lang").'"', $html);
        }
    }

    public function test_an_unknown_slug_and_everything_outside_sample_mode_give_not_found(): void
    {
        $this->get('/en/articles/does-not-exist')->assertNotFound();

        config(['landing.sample_content' => false]);

        $this->get('/en/articles')->assertNotFound();
        $this->get('/en/articles/zinc-plating-basics')->assertNotFound();
    }

    public function test_published_articles_replace_the_samples_in_their_language_only(): void
    {
        ArticleTranslation::factory()->published('en')->create(['slug' => 'real', 'title' => 'A real article']);
        app(LandingArticles::class)->flush();

        $this->get('/en/articles')->assertOk()->assertSee('A real article')->assertDontSee(app(SampleArticles::class)->summaries()[0]['title']);
        $this->get('/en/articles/zinc-plating-basics')->assertNotFound();
        $this->get('/id/articles/zinc-plating-basics')->assertOk();
    }

    public function test_reading_minutes_count_words_and_cjk_characters(): void
    {
        $this->assertSame(1, ArticleBody::readingMinutes('<p>Short.</p>'));
        $this->assertSame(1, ArticleBody::readingMinutes(''));
        $this->assertSame(3, ArticleBody::readingMinutes('<p>'.str_repeat('word ', 450).'</p>'));
        $this->assertSame(3, ArticleBody::readingMinutes('<p>'.str_repeat('字', 1000).'</p>'));
    }

    public function test_the_article_texts_exist_in_every_language(): void
    {
        foreach (config('locales.landing') as $code) {
            $response = $this->get("/{$code}/articles")->assertOk();

            foreach (['sample', 'latest', 'share.title', 'share.copy'] as $key) {
                $this->assertNotSame("articles.{$key}", __("articles.{$key}", [], $code));
            }

            $this->assertNotSame('articles.minutes', __('articles.minutes', ['count' => 3], $code));
            $response->assertSee(__('articles.latest', [], $code), false);
        }
    }
}
