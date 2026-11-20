<?php

declare(strict_types=1);

namespace Tests\Feature\Landing;

use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Services\Articles\ArticleBody;
use App\Services\Articles\LandingArticles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ArticlePagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        app(LandingArticles::class)->flush();
    }

    /**
     * Four articles and two news items, the newest first.
     *
     * @return list<ArticleTranslation>
     */
    private function publishSix(): array
    {
        $translations = [];

        foreach (range(0, 5) as $number) {
            $article = $number % 3 === 2 ? Article::factory()->news()->create() : Article::factory()->create();
            $translations[] = ArticleTranslation::factory()->published('en')->create([
                'article_id' => $article->id,
                'title' => "Item number {$number}",
                'published_at' => now()->subDays($number + 20),
            ]);
        }

        app(LandingArticles::class)->flush();

        return $translations;
    }

    public function test_the_list_shows_the_newest_large_the_rest_as_cards_and_counts_the_filters(): void
    {
        $items = $this->publishSix();
        $html = (string) $this->get('/en/articles')->assertOk()->getContent();

        $this->assertStringContainsString('data-art-featured', $html);
        $this->assertStringContainsString('Item number 0', $html);
        $this->assertSame(5, substr_count($html, 'class="group flex h-full flex-col'));

        foreach (array_slice($items, 1) as $item) {
            $this->assertStringContainsString('href="'.route('landing.articles.show', ['locale' => 'en', 'article' => $item->slug]).'"', $html);
        }

        $this->assertMatchesRegularExpression('/'.preg_quote(__('pages.articles.filters.article'), '/').'\s*<span[^>]*>\s*4\s*</', $html);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function publishFor(string $locale, array $attributes, bool $news = false): ArticleTranslation
    {
        $article = $news ? Article::factory()->news()->create() : Article::factory()->create();

        return ArticleTranslation::factory()->published($locale)->create([
            'article_id' => $article->id,
            'published_at' => now()->subDays(30),
            ...$attributes,
        ]);
    }

    public function test_the_search_finds_items_by_title_or_summary_in_any_case_and_needs_every_word(): void
    {
        $this->publishFor('en', ['title' => 'Hard Chrome Plating Guide', 'excerpt' => 'How thick a layer should be']);
        $this->publishFor('en', ['title' => 'Annual company update', 'excerpt' => 'We now offer CHROME services']);
        $this->publishFor('en', ['title' => 'Nickel basics', 'excerpt' => 'Nothing relevant here']);
        app(LandingArticles::class)->flush();

        $this->get('/en/articles?q=chrome')->assertOk()
            ->assertSee('Hard Chrome Plating Guide')
            ->assertSee('Annual company update')
            ->assertDontSee('Nickel basics');

        $this->get('/en/articles?q=chrome+guide')->assertOk()
            ->assertSee('Hard Chrome Plating Guide')
            ->assertDontSee('Annual company update');
    }

    public function test_the_search_combines_with_the_type_filter_and_counts_the_filters_for_the_search(): void
    {
        $this->publishFor('en', ['title' => 'Zinc article']);
        $this->publishFor('en', ['title' => 'Zinc news'], news: true);
        $this->publishFor('en', ['title' => 'Copper article']);
        app(LandingArticles::class)->flush();

        $html = (string) $this->get('/en/articles?q=zinc&type=news')->assertOk()
            ->assertSee('Zinc news')
            ->assertDontSee('Zinc article')
            ->assertDontSee('Copper article')
            ->getContent();

        $this->assertMatchesRegularExpression('/'.preg_quote(__('pages.articles.filters.all'), '/').'\s*<span[^>]*>\s*2\s*</', $html);
        $this->assertStringContainsString('name="type" value="news"', $html);
        $this->assertStringContainsString('data-search-summary', $html);
    }

    public function test_a_search_without_a_match_shows_a_message_and_not_a_404(): void
    {
        $this->publishFor('en', ['title' => 'Zinc article']);
        app(LandingArticles::class)->flush();

        $this->get('/en/articles?q=nothing-like-this')->assertOk()
            ->assertSee(__('pages.articles.search.empty'))
            ->assertDontSee('Zinc article');
    }

    public function test_the_search_looks_up_percent_and_underscore_as_typed(): void
    {
        $this->publishFor('en', ['title' => 'Save 50% on coating']);
        $this->publishFor('en', ['title' => 'Save 500 on coating']);
        $this->publishFor('en', ['title' => 'Coat_ing tips']);
        $this->publishFor('en', ['title' => 'Coatxing tips']);
        app(LandingArticles::class)->flush();

        $this->get('/en/articles?q='.urlencode('50%'))->assertOk()
            ->assertSee('Save 50% on coating')
            ->assertDontSee('Save 500 on coating');

        $this->get('/en/articles?q=coat_ing')->assertOk()
            ->assertSee('Coat_ing tips')
            ->assertDontSee('Coatxing tips');
    }

    public function test_the_search_stays_in_the_language_of_the_page(): void
    {
        $this->publishFor('en', ['title' => 'Plating in English']);
        $this->publishFor('id', ['title' => 'Plating dalam bahasa Indonesia']);
        app(LandingArticles::class)->flush();

        $this->get('/id/articles?q=plating')->assertOk()
            ->assertSee('Plating dalam bahasa Indonesia')
            ->assertDontSee('Plating in English');
    }

    public function test_a_search_result_is_not_indexed_and_points_its_canonical_at_the_plain_list(): void
    {
        $this->publishFor('en', ['title' => 'Zinc article']);
        app(LandingArticles::class)->flush();

        $html = (string) $this->get('/en/articles?q=zinc')->assertOk()->getContent();
        $this->assertStringContainsString('<meta name="robots" content="noindex, follow">', $html);
        $this->assertStringContainsString('<link rel="canonical" href="'.route('landing.articles.index', ['locale' => 'en']).'">', $html);

        $this->assertStringNotContainsString('name="robots"', (string) $this->get('/en/articles')->getContent());
    }

    public function test_the_pager_and_the_filters_keep_the_search(): void
    {
        foreach (range(1, 15) as $number) {
            $this->publishFor('en', ['title' => "Zinc story {$number}", 'published_at' => now()->subDays(30 + $number)]);
        }
        app(LandingArticles::class)->flush();

        $html = (string) $this->get('/en/articles?q=zinc')->assertOk()->getContent();
        $this->assertStringContainsString('href="'.e(route('landing.articles.index', ['locale' => 'en', 'q' => 'zinc', 'page' => 2])).'"', $html);
        $this->assertStringContainsString('href="'.e(route('landing.articles.index', ['locale' => 'en', 'q' => 'zinc', 'type' => 'news'])).'"', $html);

        $this->get('/en/articles?q=zinc&page=2')->assertOk();
        $this->get('/en/articles?q=zinc&page=3')->assertNotFound();
    }

    public function test_a_blank_or_malformed_search_is_ignored_and_a_long_one_is_cut(): void
    {
        $this->publishFor('en', ['title' => 'Zinc article']);
        app(LandingArticles::class)->flush();

        $this->get('/en/articles?q=%20%20')->assertOk()->assertSee('Zinc article')->assertDontSee('data-search-summary', false);
        $this->get('/en/articles?q[]=zinc')->assertOk()->assertSee('Zinc article');

        $html = (string) $this->get('/en/articles?q='.str_repeat('a', 300))->assertOk()->getContent();
        $this->assertStringContainsString('value="'.str_repeat('a', 100).'"', $html);
        $this->assertStringNotContainsString(str_repeat('a', 101), $html);
    }

    public function test_the_search_costs_the_same_number_of_queries_however_many_items_match(): void
    {
        foreach (range(1, 2) as $number) {
            $this->publishFor('en', ['title' => "Zinc story {$number}"]);
        }
        app(LandingArticles::class)->flush();
        $count = function (): int {
            DB::flushQueryLog();
            DB::enableQueryLog();
            $this->get('/en/articles?q=zinc')->assertOk();

            return count(DB::getQueryLog());
        };
        $count(); // The first request also fills the caches of the menu, the chat, and the footer.
        app(LandingArticles::class)->flush();
        $few = $count();

        foreach (range(3, 10) as $number) {
            $this->publishFor('en', ['title' => "Zinc story {$number}"]);
        }
        app(LandingArticles::class)->flush();

        $this->assertSame($few, $count());
    }

    public function test_searches_are_limited_per_minute_but_the_plain_list_and_the_article_page_are_not(): void
    {
        $item = $this->publishFor('en', ['title' => 'Zinc article']);
        app(LandingArticles::class)->flush();

        foreach (range(1, 60) as $ignored) {
            $this->get('/en/articles?q=zinc')->assertOk();
        }

        $this->get('/en/articles?q=zinc')->assertStatus(429);
        $this->get('/en/articles?q=other')->assertStatus(429);
        $this->get('/en/articles')->assertOk();
        $this->get('/en/articles?type=news')->assertOk();
        $this->get('/en/articles/'.$item->slug)->assertOk();
    }

    public function test_the_search_texts_exist_in_every_language(): void
    {
        foreach (config('locales.landing') as $code) {
            foreach (['title', 'label', 'placeholder', 'submit', 'clear', 'results', 'empty'] as $key) {
                $this->assertNotSame("pages.articles.search.{$key}", __("pages.articles.search.{$key}", ['count' => 2, 'query' => 'x'], $code));
            }
        }
    }

    public function test_the_filter_keeps_only_that_type(): void
    {
        $this->publishSix();

        $this->get('/en/articles?type=news')->assertOk()
            ->assertSee('Item number 2')
            ->assertDontSee('Item number 0');
    }

    public function test_an_article_opens_in_a_narrow_reading_column_with_progress_share_and_related(): void
    {
        $items = $this->publishSix();
        $html = (string) $this->get('/en/articles/'.$items[0]->slug)->assertOk()->getContent();

        $this->assertStringContainsString('<h1', $html);
        $this->assertStringContainsString('Item number 0', $html);
        $this->assertStringContainsString('max-w-[44.5rem]', $html);
        $this->assertStringContainsString('class="article-body reading"', $html);
        $this->assertStringContainsString('data-read-progress', $html);
        // Nothing in the content is animated, the picture included, and the picture sits in the same narrow column.
        $this->assertStringNotContainsString('data-read-head', $html);
        $this->assertStringNotContainsString('data-read-image', $html);
        $this->assertStringNotContainsString('max-w-[60rem]', $html);
        $this->assertSame(2, substr_count($html, 'max-w-[44.5rem]'));
        $this->assertStringContainsString('data-copy-link="'.url('/en/articles/'.$items[0]->slug).'"', $html);
        $this->assertStringContainsString('https://wa.me/?text=', $html);
        $this->assertStringContainsString('data-article-view', $html);
        $this->assertSame(3, substr_count($html, 'class="group flex h-full flex-col'));
        $this->assertStringContainsString('<meta property="og:image"', $html);

        foreach (config('locales.landing') as $code) {
            $this->assertStringContainsString('hreflang="'.config("locales.locales.{$code}.html_lang").'"', $html);
        }
    }

    public function test_without_a_published_article_the_pages_give_not_found(): void
    {
        $this->get('/en/articles')->assertNotFound();
        $this->get('/en/articles/does-not-exist')->assertNotFound();
        $this->get('/en/articles/zinc-plating-basics')->assertNotFound();
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
            foreach (['latest', 'share.title', 'share.copy'] as $key) {
                $this->assertNotSame("articles.{$key}", __("articles.{$key}", [], $code));
            }

            $this->assertNotSame('articles.minutes', __('articles.minutes', ['count' => 3], $code));
        }
    }

    public function test_a_title_with_a_very_long_word_wraps_instead_of_overflowing(): void
    {
        $long = str_repeat('Tedst', 24);
        ArticleTranslation::factory()->published()->create([
            'article_id' => Article::factory()->create()->id,
            'slug' => 'long-word',
            'title' => $long,
            'excerpt' => $long,
        ]);
        app(LandingArticles::class)->flush();

        $this->get('/en/articles/long-word')->assertOk()
            ->assertSee('<h1 class="mt-5 text-balance [overflow-wrap:anywhere]', false)
            ->assertSee('text-ink/65 [overflow-wrap:anywhere]', false);

        $list = (string) $this->get('/en/articles')->assertOk()->getContent();
        $this->assertStringContainsString('text-balance pb-[0.1em] [overflow-wrap:anywhere]', $list);

        $this->assertMatchesRegularExpression('/\.article-body\s*\{\s*overflow-wrap: anywhere;/', (string) file_get_contents(resource_path('css/article-body.css')));
    }

    public function test_the_home_section_cuts_a_long_title_with_an_ellipsis_after_two_lines(): void
    {
        ArticleTranslation::factory()->published()->create([
            'article_id' => Article::factory()->create()->id,
            'slug' => 'long-title',
            'title' => str_repeat('A very long article title ', 9),
        ]);
        app(LandingArticles::class)->flush();

        $this->get('/en')->assertOk()
            ->assertSee('<h3 class="mt-4 line-clamp-2 ', false)
            ->assertSee('[overflow-wrap:anywhere]', false);
    }

    public function test_the_latest_article_cuts_a_long_title_after_three_lines_without_a_block_class_that_would_undo_it(): void
    {
        ArticleTranslation::factory()->published()->create([
            'article_id' => Article::factory()->create()->id,
            'slug' => 'a-very-long-latest-title',
            'title' => str_repeat('A very long latest title ', 9),
        ]);
        app(LandingArticles::class)->flush();

        $html = (string) $this->get('/en/articles')->assertOk()->getContent();
        $start = (int) strpos($html, 'data-art-featured');
        $featured = substr($html, $start, (int) strpos($html, '</a>', $start) - $start);

        // The big title carries the clamp, and no `block` class sits beside it to turn the clamp off.
        $this->assertMatchesRegularExpression('/<span class="[^"]*\bline-clamp-3\b[^"]*\[overflow-wrap:anywhere\][^"]*font-black/', $featured);
        $this->assertDoesNotMatchRegularExpression('/<span class="[^"]*\bline-clamp-3\b[^"]*\bblock\b/', $featured);
        $this->assertDoesNotMatchRegularExpression('/<span class="[^"]*\bblock\b[^"]*\bline-clamp-3\b/', $featured);
    }

    public function test_the_card_cuts_a_long_title_after_two_lines_and_the_summary_after_three_without_a_block_class_that_would_undo_it(): void
    {
        foreach (['one', 'two'] as $slug) {
            ArticleTranslation::factory()->published()->create([
                'article_id' => Article::factory()->create()->id,
                'slug' => $slug,
                'title' => str_repeat('A very long article title ', 9),
                'excerpt' => str_repeat('A long summary sentence. ', 20),
            ]);
        }
        app(LandingArticles::class)->flush();

        $html = (string) $this->get('/en/articles')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<span class="mt-3 line-clamp-2 [^"]*font-black[^"]*\[overflow-wrap:anywhere\]/', $html);
        $this->assertMatchesRegularExpression('/<span class="mt-3 line-clamp-3 [^"]*\[overflow-wrap:anywhere\]/', $html);
        $this->assertDoesNotMatchRegularExpression('/line-clamp-\d [^"]*\bblock\b/', $html);
    }

    public function test_an_item_is_new_for_two_weeks_from_the_day_it_was_published(): void
    {
        Carbon::setTestNow('2026-11-19 15:30:00');

        $this->assertTrue(LandingArticles::isNew('2026-11-19'));
        $this->assertTrue(LandingArticles::isNew('2026-11-12'));
        $this->assertTrue(LandingArticles::isNew('2026-11-05'));
        $this->assertFalse(LandingArticles::isNew('2026-11-04'));
        $this->assertFalse(LandingArticles::isNew('2026-10-01'));
        $this->assertFalse(LandingArticles::isNew('2026-11-20'));
        $this->assertFalse(LandingArticles::isNew(''));
        $this->assertSame(14, LandingArticles::NEW_DAYS);
    }

    private function publish(string $locale, int $daysAgo, string $title = 'An article'): ArticleTranslation
    {

        $translation = ArticleTranslation::factory()->published($locale)->create([
            'article_id' => Article::factory()->create()->id,
            'title' => $title,
            'published_at' => now()->subDays($daysAgo),
        ]);
        app(LandingArticles::class)->flush();

        return $translation;
    }

    public function test_a_recent_item_puts_a_red_new_badge_on_the_menu_and_on_its_list_entries(): void
    {
        $this->publish('en', 3, 'Fresh one');
        $this->publish('en', 10, 'Recent one');
        $this->publish('en', 40, 'Old one');

        $html = (string) $this->get('/en/articles')->assertOk()->getContent();

        // One on the menu (and one in the phone menu), one on the big latest entry, one on the recent card; none on the old card.
        $this->assertSame(1, substr_count($html, 'class="ms-2 inline-flex items-center rounded-full bg-brand-600'));
        $this->assertSame(1, substr_count($html, 'class="ms-3 inline-block'));
        // Beside the menu text, in the flow (it widens the item), not floating over it.
        $this->assertStringNotContainsString('absolute -end-5', $html);
        $this->assertSame(4, substr_count($html, 'data-new-badge'));
        $this->assertStringContainsString('bg-brand-600 px-3 py-1', $html);
        $this->assertStringContainsString(__('articles.new', [], 'en'), $html);

        // The badge sits on the Articles & News item only.
        $this->assertMatchesRegularExpression('/href="[^"]*\/en\/articles"[^>]*>\s*<span class="uline">Articles &amp; News<\/span><span data-new-badge/', $html);
        $this->assertDoesNotMatchRegularExpression('/<span class="uline">(About Us|Processes|Services|Contact)<\/span><span data-new-badge/', $html);
    }

    public function test_the_card_of_an_older_item_has_no_badge(): void
    {
        $this->publish('en', 2, 'Newest');
        $this->publish('en', 30, 'Month old');

        $html = (string) $this->get('/en/articles')->assertOk()->getContent();
        $grid = substr($html, (int) strpos($html, 'data-art-grid'));

        $this->assertStringContainsString('Month old', $grid);
        $this->assertStringNotContainsString('data-new-badge', $grid);
    }

    public function test_without_anything_recent_there_is_no_badge_anywhere(): void
    {
        $this->publish('en', 30, 'Old');

        foreach (['/en', '/en/articles', '/en/about'] as $url) {
            $this->assertStringNotContainsString('data-new-badge', (string) $this->get($url)->assertOk()->getContent(), $url);
        }
    }

    public function test_the_badge_goes_away_by_itself_without_the_cache_being_cleared(): void
    {
        $this->publish('en', 10, 'Ten days old');

        $this->assertStringContainsString('data-new-badge', (string) $this->get('/en/articles')->getContent());

        // Nothing is edited, so the cached list stays as it is; only the day changes.
        Carbon::setTestNow(now()->addDays(5));
        $this->assertStringNotContainsString('data-new-badge', (string) $this->get('/en/articles')->getContent());
        $this->assertFalse(app(LandingArticles::class)->hasNew('en'));
    }

    public function test_the_badge_follows_the_language_of_the_page(): void
    {
        $this->publish('id', 2, 'Artikel baru');

        $id = (string) $this->get('/id/articles')->assertOk()->getContent();
        $this->assertStringContainsString('data-new-badge', $id);
        $this->assertStringContainsString('>'.__('articles.new', [], 'id').'<', $id);

        // English has no published item, so nothing is new there.
        $this->assertStringNotContainsString('data-new-badge', (string) $this->get('/en/articles')->getContent());
    }

    public function test_the_menu_badge_reads_only_the_cached_list_so_it_costs_no_extra_query_per_page(): void
    {
        $this->publish('en', 2);
        app(LandingArticles::class)->hasNew('en');

        DB::enableQueryLog();
        app(LandingArticles::class)->hasNew('en');

        $this->assertCount(0, DB::getQueryLog());
    }

    public function test_the_new_label_exists_in_every_landing_language(): void
    {
        foreach (config('locales.landing') as $code) {
            $this->assertNotSame('articles.new', __('articles.new', [], $code), $code);
        }

        $this->assertSame('New', __('articles.new', [], 'en'));
    }
}
