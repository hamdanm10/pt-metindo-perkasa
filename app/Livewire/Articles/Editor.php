<?php

declare(strict_types=1);

namespace App\Livewire\Articles;

use App\Actions\Articles\SaveArticle;
use App\Enums\ArticleType;
use App\Livewire\Concerns\ResolvesActor;
use App\Livewire\Forms\ArticleForm;
use App\Models\Article;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

class Editor extends Component
{
    use ResolvesActor;
    use WithFileUploads;

    #[Locked]
    public ?Article $article = null;

    public ArticleForm $form;

    public string $activeLocale = 'en';

    public function mount(?Article $article = null): void
    {
        if ($article?->exists) {
            $this->authorize('update', $article);
            $article->load('translations');
            $this->article = $article;
        } else {
            $this->authorize('create', Article::class);
        }

        $this->form->initialize($this->article);
        $this->activeLocale = config()->string('locales.default');
    }

    public function updatedFormThumbnail(): void
    {
        $this->form->validateOnly('thumbnail');
    }

    public function selectLocale(string $locale): void
    {
        if (in_array($locale, config()->array('locales.landing'), true)) {
            $this->activeLocale = $locale;
        }
    }

    public function save(SaveArticle $saveArticle): void
    {
        $this->article === null
            ? $this->authorize('create', Article::class)
            : $this->authorize('update', $this->article);

        $this->form->normalize();
        $this->form->validate();

        $saved = $saveArticle->execute(
            $this->article,
            $this->actor(),
            ArticleType::from($this->form->type),
            $this->form->payload(),
            $this->form->thumbnail,
        );

        session()->flash('status', __($this->article === null ? 'article_manager.messages.created' : 'article_manager.messages.updated'));

        $this->redirectRoute('app.articles.edit', $saved);
    }

    public function render(): View
    {
        $title = __($this->article === null ? 'article_manager.create_title' : 'article_manager.edit_title');

        return view('livewire.articles.editor', [
            'currentThumbnail' => $this->article?->thumbnail_path,
        ])->layout('components.dashboard.layout', ['title' => $title]);
    }
}
