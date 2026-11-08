<?php

declare(strict_types=1);

namespace App\Livewire\Articles;

use App\Actions\Articles\DeleteArticle;
use App\Actions\Articles\ListArticles;
use App\Enums\ArticleType;
use App\Enums\PublicationStatus;
use App\Models\Article;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $type = '';

    #[Url(except: '')]
    public string $status = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Article::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function delete(int $id, DeleteArticle $deleteArticle): void
    {
        $article = Article::query()->findOrFail($id);

        $this->authorize('delete', $article);

        $deleteArticle->execute($article);

        session()->flash('status', __('article_manager.messages.deleted'));
    }

    public function render(ListArticles $listArticles): View
    {
        $this->authorize('viewAny', Article::class);

        return view('livewire.articles.index', [
            'articles' => $listArticles->execute(
                $this->search,
                ArticleType::tryFrom($this->type),
                PublicationStatus::tryFrom($this->status),
            ),
        ])->layout('components.dashboard.layout', ['title' => __('article_manager.title')]);
    }
}
