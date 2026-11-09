<?php

declare(strict_types=1);

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Services\Articles\LandingArticles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LandingArticlesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Cache::flush();
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function publish(string $locale, string $slug, ?Article $article = null, array $extra = []): ArticleTranslation
    {
        return ArticleTranslation::factory()->published($locale)->create(array_merge([
            'article_id' => ($article ?? Article::factory()->create())->id,
            'slug' => $slug,
            'title' => "Title {$slug}",
        ], $extra));
    }

    public function test_a_language_without_a_published_article_has_no_pages_and_no_menu_item(): void
    {
        $this->publish('id', 'satu');

        $this->get('/id/articles')->assertOk()->assertSee('Title satu');
        $this->get('/en/articles')->assertNotFound();
        $this->get('/en/about')->assertDontSee('href="'.url('/en/articles').'"', false);
        $this->get('/id/about')->assertSee('href="'.url('/id/articles').'"', false);
    }

    public function test_there_is_no_fallback_text_in_another_language(): void
    {
        $this->publish('id', 'satu');

        $this->get('/en/articles/satu')->assertNotFound();
        $this->get('/id/articles/satu')->assertOk()->assertSee('Title satu');
    }

    public function test_drafts_are_not_public(): void
    {
        ArticleTranslation::factory()->create(['locale' => 'en', 'slug' => 'draft']);

        $this->get('/en/articles/draft')->assertNotFound();
        $this->get('/en/articles')->assertNotFound();
    }

    public function test_the_same_slug_can_exist_in_two_languages(): void
    {
        $this->publish('en', 'same');
        $this->publish('id', 'same');

        $this->get('/en/articles/same')->assertOk();
        $this->get('/id/articles/same')->assertOk();
    }

    public function test_type_filter_and_invalid_type(): void
    {
        $this->publish('en', 'news-one', Article::factory()->news()->create());
        $this->publish('en', 'article-one');

        $this->get('/en/articles?type=news')->assertSee('Title news-one')->assertDontSee('Title article-one');
        $this->get('/en/articles?type=article')->assertSee('Title article-one')->assertDontSee('Title news-one');
        $this->get('/en/articles?type=invalid')->assertSee('Title news-one')->assertSee('Title article-one');
    }

    public function test_hreflang_lists_only_published_languages_and_canonical_is_its_own(): void
    {
        $article = Article::factory()->create();
        $this->publish('en', 'quality', $article);
        $this->publish('id', 'kualitas', $article);
        ArticleTranslation::factory()->create(['article_id' => $article->id, 'locale' => 'ja', 'slug' => 'draft-ja']);

        $response = $this->get('/id/articles/kualitas')->assertOk();

        $response->assertSee('<link rel="canonical" href="'.url('/id/articles/kualitas').'">', false);
        $response->assertSee('hreflang="en" href="'.url('/en/articles/quality').'"', false);
        $response->assertSee('hreflang="id" href="'.url('/id/articles/kualitas').'"', false);
        $response->assertDontSee('<link rel="alternate" hreflang="ja"', false);
        $response->assertSee('hreflang="x-default" href="'.url('/en/articles/quality').'"', false);
    }

    public function test_meta_og_image_and_rendered_body(): void
    {
        $this->publish('en', 'meta', null, ['meta_title' => 'Meta Title', 'meta_description' => 'Meta text', 'body_html' => '<p>Body <strong>here</strong></p>']);

        $this->get('/en/articles/meta')
            ->assertSee('<title>Meta Title | PT Metindo Perkasa</title>', false)
            ->assertSee('<meta name="description" content="Meta text">', false)
            ->assertSee('property="og:image"', false)
            ->assertSee('<p>Body <strong>here</strong></p>', false);
    }

    public function test_home_shows_the_three_newest_of_the_language_only(): void
    {
        foreach (['a', 'b', 'c', 'd'] as $i => $slug) {
            $this->publish('en', $slug, null, ['published_at' => now()->subDays(10 - $i)]);
        }
        $this->publish('id', 'lain');

        $this->get('/en')->assertSee('Title d')->assertSee('Title c')->assertSee('Title b')->assertDontSee('Title a')->assertDontSee('Title lain');
    }

    public function test_related_lists_other_articles_of_the_same_language(): void
    {
        $this->publish('en', 'main');
        $this->publish('en', 'other');
        $this->publish('id', 'lain');

        $this->get('/en/articles/main')->assertSee('Title other')->assertDontSee('Title lain');
    }

    public function test_the_list_is_cached_and_cleared_on_change(): void
    {
        $translation = $this->publish('en', 'cached');
        $service = app(LandingArticles::class);

        $this->assertCount(1, $service->list('en'));

        $translation->update(['title' => 'Renamed']);
        $this->assertSame('Title cached', $service->list('en')[0]['title']);

        $service->flush();
        $this->assertSame('Renamed', $service->list('en')[0]['title']);
    }

    public function test_query_count_does_not_grow_with_more_articles(): void
    {
        $this->publish('en', 'one');
        $this->get('/en/articles');
        Cache::flush();

        DB::enableQueryLog();
        $this->get('/en/articles');
        $few = count(DB::getQueryLog());

        foreach (range(1, 8) as $i) {
            $this->publish('en', "more-{$i}");
        }
        Cache::flush();

        DB::flushQueryLog();
        $this->get('/en/articles');

        $this->assertSame($few, count(DB::getQueryLog()));
    }

    public function test_every_landing_language_can_render_its_articles(): void
    {
        foreach (config('locales.landing') as $code) {
            $this->publish($code, "slug-{$code}");

            $this->get("/{$code}/articles")->assertOk()->assertSee("Title slug-{$code}");
            $this->get("/{$code}/articles/slug-{$code}")->assertOk();
        }
    }

    public function test_home_articles_section_follows_the_reference_layout(): void
    {
        $this->publish('en', 'first', null, ['title' => 'First article', 'published_at' => now()->subDays(3)]);
        $this->publish('en', 'second', Article::factory()->news()->create(), ['title' => 'Second article', 'published_at' => now()->subDays(2)]);

        $response = $this->get('/en')->assertOk();

        $response->assertSee('data-anim="grow"', false)
            ->assertSee(__('home.articles.title'))
            ->assertSee(__('home.articles.intro'))
            ->assertSee(__('articles.tags.article'))
            ->assertSee(__('articles.tags.news'))
            ->assertSee(now()->subDays(3)->format('d.m.y'))
            ->assertSee('data-btn', false)
            ->assertSee('class="title-hover"', false)
            ->assertSee('href="'.url('/en/articles').'"', false);

        $this->assertSame(2, substr_count((string) $response->getContent(), '<article>'));
    }

    public function test_home_articles_button_label_exists_in_every_language(): void
    {
        foreach (config('locales.landing') as $code) {
            $this->publish($code, "slug-{$code}");

            $this->get("/{$code}")->assertSeeText(__('home.articles.all', [], $code));
        }
    }
}
