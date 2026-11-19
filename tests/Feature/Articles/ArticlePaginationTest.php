<?php

declare(strict_types=1);

namespace Tests\Feature\Articles;

use App\Enums\ArticleType;
use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Services\Articles\LandingArticles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ArticlePaginationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    /** Publishes items "Item 01" (newest) to "Item NN" (oldest), one per day. */
    private function publishItems(int $count, ?callable $type = null): void
    {
        for ($number = 1; $number <= $count; $number++) {
            ArticleTranslation::factory()->published('en')->create([
                'article_id' => Article::factory()->create(['type' => $type ? $type($number) : ArticleType::Article])->id,
                'title' => sprintf('Item %02d', $number),
                'published_at' => now()->subDays($number),
            ]);
        }

        app(LandingArticles::class)->flush();
    }

    private function html(string $url): string
    {
        return (string) $this->get($url)->assertOk()->getContent();
    }

    public function test_page_one_has_the_newest_entry_large_and_twelve_cards_below_it(): void
    {
        $this->publishItems(30);
        $html = $this->html('/en/articles');

        $this->assertSame(1, substr_count($html, 'data-art-featured class='));
        $this->assertStringContainsString('Item 01', $html);
        $this->assertStringContainsString('Item 13', $html);
        $this->assertStringNotContainsString('Item 14', $html);
        $this->assertSame(12, substr_count(substr($html, (int) strpos($html, 'data-art-grid')), 'class="title-hover"'));
    }

    public function test_later_pages_are_grids_of_twelve_without_the_large_entry(): void
    {
        $this->publishItems(30);

        $second = $this->html('/en/articles?page=2');
        $this->assertStringNotContainsString('data-art-featured', $second);
        $this->assertStringContainsString('Item 14', $second);
        $this->assertStringContainsString('Item 25', $second);
        $this->assertStringNotContainsString('Item 13', $second);
        $this->assertStringNotContainsString('Item 26', $second);
        $this->assertSame(12, substr_count($second, 'class="title-hover"'));

        $third = $this->html('/en/articles?page=3');
        $this->assertStringContainsString('Item 26', $third);
        $this->assertStringContainsString('Item 30', $third);
        $this->assertSame(5, substr_count($third, 'class="title-hover"'));
    }

    public function test_a_page_past_the_last_is_not_found_and_odd_page_numbers_fall_back_to_page_one(): void
    {
        $this->publishItems(30);

        $this->get('/en/articles?page=4')->assertNotFound();
        $this->get('/en/articles?page=999')->assertNotFound();

        foreach (['0', '-3', 'abc', ''] as $odd) {
            $this->assertStringContainsString('data-art-featured', $this->html('/en/articles?page='.$odd), $odd);
        }

        $this->assertStringContainsString('data-art-featured', $this->html('/en/articles?page[]=2'));
    }

    public function test_the_pager_links_every_page_and_marks_the_current_one(): void
    {
        $this->publishItems(30);
        $first = $this->html('/en/articles');

        $this->assertStringContainsString('data-pagination', $first);
        $this->assertStringContainsString('href="'.url('/en/articles?page=2').'"', $first);
        $this->assertStringContainsString('href="'.url('/en/articles?page=3').'"', $first);
        $this->assertStringContainsString('rel="next"', $first);
        $this->assertStringNotContainsString('rel="prev"', $first);
        $this->assertStringNotContainsString('?page=1', $first);
        $this->assertMatchesRegularExpression('/<span aria-current="page"[^>]*>1<\\/span>/', $first);

        $second = $this->html('/en/articles?page=2');
        $this->assertMatchesRegularExpression('/<a href="'.preg_quote(url('/en/articles'), '/').'" rel="prev"/', $second);
        $this->assertStringContainsString('rel="next"', $second);
        $this->assertMatchesRegularExpression('/<span aria-current="page"[^>]*>2<\\/span>/', $second);

        $third = $this->html('/en/articles?page=3');
        $this->assertStringContainsString('rel="prev"', $third);
        $this->assertStringNotContainsString('rel="next"', $third);
        $this->assertStringContainsString('aria-disabled="true"', $third);
    }

    public function test_a_short_list_has_no_pager(): void
    {
        $this->publishItems(13);

        $this->assertStringNotContainsString('data-pagination', $this->html('/en/articles'));
        $this->get('/en/articles?page=2')->assertNotFound();
    }

    public function test_the_type_filter_is_kept_by_the_pager_and_counts_its_own_pages(): void
    {
        // 14 news items (the odd numbers) and 16 articles: the news list has 14 entries, so two pages.
        $this->publishItems(30, fn (int $number): ArticleType => $number % 2 === 1 && $number <= 27 ? ArticleType::News : ArticleType::Article);

        $news = $this->html('/en/articles?type=news');
        $this->assertStringContainsString('href="'.url('/en/articles?type=news&amp;page=2').'"', $news);
        $this->assertStringNotContainsString('page=3', $news);

        $this->assertStringContainsString('data-art-featured', $news);
        $this->get('/en/articles?type=news&page=3')->assertNotFound();

        // The counts on the filter pills stay those of the whole list.
        $counts = app(LandingArticles::class)->counts('en');
        $this->assertSame(30, $counts['all']);
        $this->assertSame($counts['all'], $counts['article'] + $counts['news']);
    }

    public function test_a_filter_with_no_match_still_opens_its_first_page(): void
    {
        $this->publishItems(3);

        $this->get('/en/articles?type=news')->assertOk()->assertSee(__('pages.articles.empty'));
        $this->get('/en/articles?type=news&page=2')->assertNotFound();
    }

    public function test_every_page_points_its_canonical_to_itself_and_links_its_neighbours(): void
    {
        $this->publishItems(40);

        $first = $this->html('/en/articles');
        $this->assertStringContainsString('<link rel="canonical" href="'.url('/en/articles').'">', $first);
        $this->assertStringContainsString('<link rel="next" href="'.url('/en/articles?page=2').'">', $first);
        $this->assertStringNotContainsString('<link rel="prev"', $first);

        $second = $this->html('/en/articles?page=2');
        $this->assertStringContainsString('<link rel="canonical" href="'.url('/en/articles?page=2').'">', $second);
        $this->assertStringContainsString('<link rel="prev" href="'.url('/en/articles').'">', $second);
        $this->assertStringContainsString('<link rel="next" href="'.url('/en/articles?page=3').'">', $second);

        $third = $this->html('/en/articles?page=3');
        $this->assertStringContainsString('<link rel="prev" href="'.url('/en/articles?page=2').'">', $third);

        // A filtered list points at the plain list, and the other languages are not given a page they may not have.
        $this->assertStringContainsString('<link rel="canonical" href="'.url('/en/articles').'">', $this->html('/en/articles?type=article&page=2'));
        $this->assertStringContainsString('hreflang="id" href="'.url('/id/articles').'"', $second);
    }

    public function test_the_list_page_reads_one_page_of_rows_so_the_cost_does_not_grow_with_the_articles(): void
    {
        $this->publishItems(14);
        $this->get('/en/articles');

        DB::flushQueryLog();
        DB::enableQueryLog();
        $this->get('/en/articles');
        $few = count(DB::getQueryLog());

        $this->publishItems(60);
        $this->get('/en/articles');

        DB::flushQueryLog();
        $this->get('/en/articles');
        $many = count(DB::getQueryLog());
        $queries = array_column(DB::getQueryLog(), 'query');

        $this->assertSame($few, $many);

        // Every read of the translations that orders them is bounded by LIMIT; nothing loads the whole list.
        foreach ($queries as $query) {
            if (str_contains($query, 'from "article_translations"') && str_contains($query, 'order by')) {
                $this->assertStringContainsString('limit', $query);
            }
        }
    }

    public function test_the_home_page_and_the_menu_read_only_a_few_cached_entries(): void
    {
        $this->publishItems(60);
        $this->get('/en');

        DB::flushQueryLog();
        DB::enableQueryLog();
        $html = $this->html('/en');
        $queries = array_column(DB::getQueryLog(), 'query');

        // Four cards, however many articles there are.
        $this->assertSame(4, substr_count($html, 'class="title-hover"'));

        foreach ($queries as $query) {
            if (str_contains($query, 'from "article_translations"')) {
                $this->assertStringContainsString('"slug" in', $query);
            }
        }
    }

    public function test_the_page_labels_exist_in_every_landing_language(): void
    {
        foreach (config('locales.landing') as $code) {
            foreach (['label', 'previous', 'next', 'page'] as $key) {
                $this->assertNotSame("articles.pagination.{$key}", __("articles.pagination.{$key}", ['number' => 2], $code), "{$code} {$key}");
            }
        }

        $this->assertSame('Page 2', __('articles.pagination.page', ['number' => 2], 'en'));
    }
}
