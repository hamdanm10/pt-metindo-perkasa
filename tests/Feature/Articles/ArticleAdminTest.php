<?php

declare(strict_types=1);

namespace Tests\Feature\Articles;

use App\Enums\PublicationStatus;
use App\Livewire\Articles\Editor;
use App\Livewire\Articles\Index;
use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

class ArticleAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Storage::fake('local');
    }

    private function body(string $text = 'Hello world'): string
    {
        return (string) json_encode(['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => $text]]]]]);
    }

    /**
     * @param  Testable<Component>  $component
     * @return Testable<Component>
     */
    private function fillEnglish(Testable $component, bool $published = false): Testable
    {
        return $component
            ->set('form.translations.en.title', 'Quality control')
            ->set('form.translations.en.slug', 'quality-control')
            ->set('form.translations.en.body', $this->body())
            ->set('form.translations.en.meta_description', 'About quality control')
            ->set('form.translations.en.published', $published);
    }

    private function image(int $width = 1600, int $height = 900): UploadedFile
    {
        return UploadedFile::fake()->image('cover.jpg', $width, $height);
    }

    public function test_only_admins_open_the_pages(): void
    {
        $article = Article::factory()->create();

        foreach (['/app/articles', '/app/articles/create', "/app/articles/{$article->id}/edit"] as $url) {
            $this->actingAs(User::factory()->admin()->create())->get($url)->assertOk();
            $this->actingAs(User::factory()->staff()->create())->get($url)->assertForbidden();
            $this->actingAs(User::factory()->customer()->create())->get($url)->assertForbidden();
        }

        auth()->logout();
        $this->get('/app/articles')->assertRedirect(route('app.login'));
    }

    public function test_staff_cannot_use_the_livewire_actions(): void
    {
        $staff = User::factory()->staff()->create();

        Livewire::actingAs($staff)->test(Editor::class)->assertForbidden();
        Livewire::actingAs($staff)->test(Index::class)->assertForbidden();
    }

    public function test_menu_shows_the_articles_link_to_admins_only(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get('/app/dashboard')->assertSee(route('app.articles.index'), false);
        $this->actingAs(User::factory()->staff()->create())->get('/app/dashboard')->assertDontSee(route('app.articles.index'), false);
    }

    public function test_a_draft_is_saved_without_a_thumbnail(): void
    {
        $admin = User::factory()->admin()->create();

        $component = Livewire::actingAs($admin)->test(Editor::class);
        $this->fillEnglish($component)->call('save')->assertHasNoErrors();

        $article = Article::query()->with('translations')->firstOrFail();
        $translation = $article->translations->firstOrFail();
        $this->assertNull($article->thumbnail_path);
        $this->assertSame($admin->id, $article->created_by);
        $this->assertSame(PublicationStatus::Draft, $translation->status);
        $this->assertNull($translation->published_at);
        $this->assertSame('<p>Hello world</p>', $translation->body_html);
    }

    public function test_publishing_needs_a_thumbnail(): void
    {
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class);

        $this->fillEnglish($component, published: true)->call('save')->assertHasErrors(['form.thumbnail']);

        $this->assertSame(0, Article::query()->count());
    }

    public function test_publishing_needs_a_complete_language(): void
    {
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.thumbnail', $this->image())
            ->set('form.translations.en.title', 'Only a title')
            ->set('form.translations.en.slug', 'only-a-title')
            ->set('form.translations.en.published', true)
            ->call('save');

        $component->assertHasErrors(['form.translations.en.body', 'form.translations.en.meta_description']);
    }

    public function test_a_language_can_be_published_alone_with_a_stored_webp_thumbnail(): void
    {
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.thumbnail', $this->image());
        $this->fillEnglish($component, published: true)->call('save')->assertHasNoErrors();

        $article = Article::query()->with('translations')->firstOrFail();

        $this->assertCount(1, $article->translations);
        $this->assertNotNull($article->translations->firstOrFail()->published_at);
        $this->assertStringEndsWith('.webp', (string) $article->thumbnail_path);
        Storage::disk('public')->assertExists((string) $article->thumbnail_path);
    }

    public function test_thumbnail_type_size_and_dimensions_are_checked(): void
    {
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class);

        $this->fillEnglish($component)
            ->set('form.thumbnail', UploadedFile::fake()->create('file.pdf', 10, 'application/pdf'))
            ->call('save')->assertHasErrors(['form.thumbnail']);

        $this->fillEnglish($component)
            ->set('form.thumbnail', $this->image(800, 450))
            ->call('save')->assertHasErrors(['form.thumbnail']);

        $this->fillEnglish($component)
            ->set('form.thumbnail', UploadedFile::fake()->image('big.jpg', 1600, 900)->size(3000))
            ->call('save')->assertHasErrors(['form.thumbnail']);
    }

    public function test_at_least_one_language_is_required(): void
    {
        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->call('save')->assertHasErrors(['form.translations']);
    }

    public function test_slug_is_unique_per_language_but_may_repeat_across_languages(): void
    {
        ArticleTranslation::factory()->create(['locale' => 'en', 'slug' => 'taken']);
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class);

        $this->fillEnglish($component)->set('form.translations.en.slug', 'taken')->call('save')
            ->assertHasErrors(['form.translations.en.slug']);

        $component
            ->set('form.translations.en.title', '')->set('form.translations.en.slug', '')
            ->set('form.translations.en.body', '')->set('form.translations.en.meta_description', '')
            ->set('form.translations.id.title', 'Sama')->set('form.translations.id.slug', 'taken')
            ->call('save')->assertHasNoErrors();
    }

    public function test_slug_format_is_checked(): void
    {
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class);

        $this->fillEnglish($component)->set('form.translations.en.slug', 'Not A Slug!')->call('save')
            ->assertHasErrors(['form.translations.en.slug']);
    }

    public function test_type_outside_the_list_is_rejected(): void
    {
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class);

        $this->fillEnglish($component)->set('form.type', 'blog')->call('save')->assertHasErrors(['form.type']);
    }

    public function test_editing_updates_keeps_the_publish_date_and_removes_emptied_languages(): void
    {
        $article = Article::factory()->create();
        $en = ArticleTranslation::factory()->published('en')->create(['article_id' => $article->id, 'slug' => 'first']);
        ArticleTranslation::factory()->create(['article_id' => $article->id, 'locale' => 'id', 'slug' => 'pertama']);
        $date = $en->published_at;

        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class, ['article' => $article])
            ->set('form.translations.en.title', 'Changed')
            ->set('form.translations.id.title', '')->set('form.translations.id.slug', '')
            ->set('form.translations.id.excerpt', '')->set('form.translations.id.body', '')
            ->set('form.translations.id.meta_description', '')->set('form.translations.id.image_alt', '')
            ->call('save');

        $component->assertHasNoErrors();
        $en->refresh();
        $this->assertSame('Changed', $en->title);
        $this->assertEquals($date, $en->published_at);
        $this->assertSame(1, $article->translations()->count());
    }

    public function test_replacing_the_thumbnail_removes_the_old_file(): void
    {
        Storage::disk('public')->put('articles/old.webp', 'x');
        $article = Article::factory()->create(['thumbnail_path' => 'articles/old.webp']);
        ArticleTranslation::factory()->create(['article_id' => $article->id]);

        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class, ['article' => $article])
            ->set('form.thumbnail', $this->image())->call('save')->assertHasNoErrors();

        Storage::disk('public')->assertMissing('articles/old.webp');
        $article->refresh();
        Storage::disk('public')->assertExists((string) $article->thumbnail_path);
    }

    public function test_deleting_removes_the_article_and_its_thumbnail(): void
    {
        Storage::disk('public')->put('articles/gone.webp', 'x');
        $article = Article::factory()->create(['thumbnail_path' => 'articles/gone.webp']);
        ArticleTranslation::factory()->create(['article_id' => $article->id]);

        Livewire::actingAs(User::factory()->admin()->create())->test(Index::class)->call('delete', $article->id);

        $this->assertSame(0, Article::query()->count());
        $this->assertSame(0, ArticleTranslation::query()->count());
        Storage::disk('public')->assertMissing('articles/gone.webp');
    }

    public function test_the_list_filters_by_search_type_and_status(): void
    {
        $alpha = Article::factory()->create();
        ArticleTranslation::factory()->published('en')->create(['article_id' => $alpha->id, 'title' => 'Alpha plating']);
        $beta = Article::factory()->news()->create();
        ArticleTranslation::factory()->create(['article_id' => $beta->id, 'title' => 'Beta coating']);

        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Index::class)
            ->assertSee('Alpha plating')->assertSee('Beta coating');

        $component->set('search', 'ALPHA')->assertSee('Alpha plating')->assertDontSee('Beta coating');
        $component->set('search', '')->set('type', 'news')->assertSee('Beta coating')->assertDontSee('Alpha plating');
        $component->set('type', '')->set('status', 'published')->assertSee('Alpha plating')->assertDontSee('Beta coating');
    }

    public function test_the_list_title_follows_landing_language_order(): void
    {
        $article = Article::factory()->create();
        ArticleTranslation::factory()->create(['article_id' => $article->id, 'locale' => 'ar', 'title' => 'Arabic title']);
        ArticleTranslation::factory()->create(['article_id' => $article->id, 'locale' => 'en', 'title' => 'English title']);

        $this->assertSame('English title', Article::query()->with('translations')->firstOrFail()->displayTitle());
    }

    public function test_the_list_query_count_does_not_grow_with_rows(): void
    {
        $admin = User::factory()->admin()->create();
        Article::factory()->count(2)->create()->each(fn (Article $article) => ArticleTranslation::factory()->create(['article_id' => $article->id]));

        DB::enableQueryLog();
        Livewire::actingAs($admin)->test(Index::class);
        $few = count(DB::getQueryLog());

        Article::factory()->count(10)->create()->each(fn (Article $article) => ArticleTranslation::factory()->create(['article_id' => $article->id]));

        DB::flushQueryLog();
        Livewire::actingAs($admin)->test(Index::class);

        $this->assertSame($few, count(DB::getQueryLog()));
    }

    public function test_dashboard_texts_exist_in_both_dashboard_languages(): void
    {
        $en = require lang_path('en/article_manager.php');
        $id = require lang_path('id/article_manager.php');

        $this->assertSame(array_keys($en), array_keys($id));
        foreach (['fields', 'status', 'hints', 'editor', 'messages', 'types'] as $group) {
            $this->assertSame(array_keys($en[$group]), array_keys($id[$group]));
        }
    }
}
