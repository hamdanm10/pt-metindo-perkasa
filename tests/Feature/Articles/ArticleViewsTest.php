<?php

declare(strict_types=1);

namespace Tests\Feature\Articles;

use App\Livewire\Articles\Index;
use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Models\User;
use App\Services\Articles\ArticleViews;
use App\Services\Articles\LandingArticles;
use App\Services\Articles\SampleArticles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ArticleViewsTest extends TestCase
{
    use RefreshDatabase;

    private const BROWSER = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/126.0 Safari/537.36';

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        config(['landing.sample_content' => false]);
        app(LandingArticles::class)->flush();
    }

    private function published(string $locale = 'en', int $views = 0, string $slug = 'hello'): ArticleTranslation
    {
        return ArticleTranslation::factory()->published()->create([
            'article_id' => Article::factory()->create()->id,
            'locale' => $locale,
            'slug' => $slug,
            'views_count' => $views,
        ]);
    }

    /** @return TestResponse<Response> */
    private function sendView(string $url, ?string $agent = self::BROWSER, ?string $cookie = null): TestResponse
    {
        $server = $agent === null ? [] : ['HTTP_USER_AGENT' => $agent];
        $test = $cookie === null ? $this : $this->withUnencryptedCookie('viewed_articles', $cookie);

        return $test->post($url, [], $server);
    }

    public function test_a_view_adds_to_the_total_and_to_the_day(): void
    {
        $translation = $this->published();

        $this->sendView('/en/articles/hello/views')->assertNoContent();

        $this->assertSame(1, $translation->refresh()->views_count);
        $this->assertSame(1, (int) DB::table('article_view_days')->where('article_translation_id', $translation->id)->value('views'));
    }

    public function test_views_are_counted_per_language(): void
    {
        $en = $this->published('en', 0, 'hello');
        $id = $this->published('id', 0, 'halo');

        $this->sendView('/id/articles/halo/views')->assertNoContent();

        $this->assertSame(0, $en->refresh()->views_count);
        $this->assertSame(1, $id->refresh()->views_count);
    }

    public function test_the_same_visitor_counts_once_per_24_hours(): void
    {
        $translation = $this->published();

        $first = $this->sendView('/en/articles/hello/views');
        $cookie = $first->getCookie('viewed_articles', false)?->getValue();
        $this->assertNotNull($cookie);

        $this->sendView('/en/articles/hello/views', self::BROWSER, $cookie)->assertNoContent();
        $this->assertSame(1, $translation->refresh()->views_count);

        $this->travel(25)->hours();
        $this->sendView('/en/articles/hello/views', self::BROWSER, $cookie)->assertNoContent();
        $this->assertSame(2, $translation->refresh()->views_count);
    }

    public function test_the_visitors_memory_drops_expired_entries_and_stays_small(): void
    {
        $views = new ArticleViews;
        $now = now()->getTimestamp();

        $kept = $views->remember([1 => $now - 10, 2 => $now + 100], 3);

        $this->assertSame([2, 3], array_keys($kept));

        $many = [];
        for ($i = 1; $i <= 150; $i++) {
            $many[$i] = $now + 100;
        }

        $this->assertCount(100, $views->remember($many, 999));
    }

    public function test_drafts_unknown_addresses_and_samples_are_not_counted(): void
    {
        $draft = ArticleTranslation::factory()->create([
            'article_id' => Article::factory()->create()->id,
            'slug' => 'draft',
            'views_count' => 0,
        ]);

        $this->sendView('/en/articles/draft/views')->assertNoContent();
        $this->sendView('/en/articles/missing/views')->assertNoContent();

        $this->assertSame(0, $draft->refresh()->views_count);
        $this->assertSame(0, DB::table('article_view_days')->count());
    }

    public function test_bots_and_requests_with_an_empty_user_agent_are_not_counted(): void
    {
        $translation = $this->published();

        foreach (['Googlebot/2.1 (+http://www.google.com/bot.html)', 'curl/8.4.0', 'Mozilla/5.0 HeadlessChrome/126', ''] as $agent) {
            $this->sendView('/en/articles/hello/views', $agent)->assertNoContent();
        }

        $this->assertSame(0, $translation->refresh()->views_count);
    }

    public function test_admin_and_staff_are_not_counted_but_a_customer_is(): void
    {
        $translation = $this->published();

        $this->actingAs(User::factory()->admin()->create())->post('/en/articles/hello/views', [], ['HTTP_USER_AGENT' => self::BROWSER]);
        $this->actingAs(User::factory()->staff()->create())->post('/en/articles/hello/views', [], ['HTTP_USER_AGENT' => self::BROWSER]);
        $this->assertSame(0, $translation->refresh()->views_count);

        $this->actingAs(User::factory()->customer()->create())->post('/en/articles/hello/views', [], ['HTTP_USER_AGENT' => self::BROWSER]);
        $this->assertSame(1, $translation->refresh()->views_count);
    }

    public function test_the_endpoint_is_throttled(): void
    {
        $this->published();

        for ($i = 0; $i < 30; $i++) {
            $this->sendView('/en/articles/hello/views', null)->assertNoContent();
        }

        $this->sendView('/en/articles/hello/views', null)->assertStatus(429);
    }

    public function test_the_detail_page_shows_the_count_only_from_ten_views_and_loads_the_counter(): void
    {
        $this->published('en', 1200);

        $this->get('/en/articles/hello')
            ->assertOk()
            ->assertSee('1.2K views')
            ->assertSee('data-article-view', false)
            ->assertSee('data-url="'.route('landing.articles.views.store', ['locale' => 'en', 'article' => 'hello']).'"', false)
            ->assertSee('name="csrf-token"', false);

        ArticleTranslation::query()->update(['views_count' => 9]);

        $this->get('/en/articles/hello')->assertOk()->assertDontSee('9 views');
    }

    public function test_sample_articles_do_not_load_the_counter(): void
    {
        config(['landing.sample_content' => true]);
        app(LandingArticles::class)->flush();
        $slug = app(SampleArticles::class)->summaries()[0]['slug'];

        $this->get("/en/articles/{$slug}")->assertOk()->assertDontSee('data-article-view', false);
    }

    public function test_the_list_cards_show_the_count_without_going_stale_in_the_cache(): void
    {
        $translation = $this->published('en', 25);

        $this->get('/en/articles')->assertOk()->assertSee('25 views');

        ArticleTranslation::query()->whereKey($translation->id)->update(['views_count' => 40]);
        $this->get('/en/articles')->assertOk()->assertSee('40 views')->assertDontSee('25 views');
    }

    public function test_the_label_is_hidden_below_ten_and_follows_the_page_language(): void
    {
        $this->assertNull(ArticleViews::label(9));
        $this->assertSame('1 view', trans_choice('articles.views', 1, ['count' => 1]));

        app()->setLocale('id');
        $this->assertStringContainsString('kali dibaca', (string) ArticleViews::label(15));
    }

    public function test_the_texts_exist_in_every_landing_language(): void
    {
        foreach (config()->array('locales.landing') as $locale) {
            $this->assertArrayHasKey('views', require lang_path("{$locale}/articles.php"));
        }

        $this->assertArrayHasKey('views', (require lang_path('en/article_manager.php'))['fields']);
        $this->assertArrayHasKey('views', (require lang_path('id/article_manager.php'))['fields']);
    }

    public function test_the_dashboard_list_shows_the_total_views_with_a_breakdown_per_language_without_extra_queries(): void
    {
        $admin = User::factory()->admin()->create();
        $article = Article::factory()->create();
        ArticleTranslation::factory()->published('en')->create(['article_id' => $article->id, 'views_count' => 1200]);
        ArticleTranslation::factory()->published('id')->create(['article_id' => $article->id, 'views_count' => 34]);

        Livewire::actingAs($admin)->test(Index::class)->assertSee('1,234')->assertSee('1.2K')->assertSee('34');

        DB::enableQueryLog();
        Livewire::actingAs($admin)->test(Index::class);
        $few = count(DB::getQueryLog());

        Article::factory()->count(6)->create()->each(fn (Article $item) => ArticleTranslation::factory()->published()->create(['article_id' => $item->id]));

        DB::flushQueryLog();
        Livewire::actingAs($admin)->test(Index::class);

        $this->assertSame($few, count(DB::getQueryLog()));
    }
}
