<div>
    <x-dashboard.page-header :title="__('article_manager.title')" :description="__('article_manager.description')" />

    <x-dashboard.flash />

    <x-dashboard.card>
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="grid flex-1 gap-4 sm:grid-cols-3">
                <x-dashboard.field name="search" :label="__('article_manager.search')" type="search" wire:model.live.debounce.300ms="search" />
                <x-dashboard.select name="type" :label="__('article_manager.fields.type')" wire:model.live="type">
                    <option value="">{{ __('article_manager.all_types') }}</option>
                    @foreach (\App\Enums\ArticleType::cases() as $case)
                        <option value="{{ $case->value }}">{{ __('article_manager.types.'.$case->value) }}</option>
                    @endforeach
                </x-dashboard.select>
                <x-dashboard.select name="status" :label="__('article_manager.fields.status')" wire:model.live="status">
                    <option value="">{{ __('article_manager.all_statuses') }}</option>
                    @foreach (\App\Enums\PublicationStatus::cases() as $case)
                        <option value="{{ $case->value }}">{{ __('article_manager.status.'.$case->value) }}</option>
                    @endforeach
                </x-dashboard.select>
            </div>
            <x-dashboard.button :href="route('app.articles.create')">{{ __('article_manager.create') }}</x-dashboard.button>
        </div>

        <div class="mt-6">
            <x-dashboard.table>
                <thead>
                    <tr>
                        <th scope="col">{{ __('article_manager.fields.title') }}</th>
                        <th scope="col">{{ __('article_manager.fields.type') }}</th>
                        <th scope="col">{{ __('article_manager.fields.languages') }}</th>
                        <th scope="col"><span class="sr-only">{{ __('article_manager.actions') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($articles as $article)
                        @php
                            $translations = $article->translations->keyBy('locale');
                            $title = $article->displayTitle() ?? __('article_manager.untitled');
                        @endphp
                        <tr wire:key="article-{{ $article->id }}" class="align-top">
                            <td class="font-semibold">{{ $title }}</td>
                            <td><x-dashboard.badge tone="role">{{ __('article_manager.types.'.$article->type->value) }}</x-dashboard.badge></td>
                            <td>
                                <ul class="flex flex-wrap gap-1.5">
                                    @foreach (config('locales.landing') as $code)
                                        @php($translation = $translations->get($code))
                                        <li>
                                            <x-dashboard.badge :tone="$translation?->status === \App\Enums\PublicationStatus::Published ? 'active' : 'muted'">
                                                <span class="uppercase" aria-hidden="true">{{ $code }}</span>
                                                <span class="sr-only">{{ config("locales.locales.{$code}.name") }}:</span>
                                                <span class="sr-only">{{ __('article_manager.status.'.($translation?->status->value ?? 'none')) }}</span>
                                            </x-dashboard.badge>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-end whitespace-nowrap">
                                <a href="{{ route('app.articles.edit', $article) }}" class="row-action">{{ __('article_manager.edit') }}<span class="sr-only"> {{ $title }}</span></a>
                                <button type="button" wire:click="delete({{ $article->id }})" data-confirm="{{ __('article_manager.confirm_delete') }}" data-confirm-label="{{ __('article_manager.delete') }}" class="row-action row-action--danger">{{ __('article_manager.delete') }}<span class="sr-only"> {{ $title }}</span></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-muted">{{ __('article_manager.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-dashboard.table>
        </div>

        <div class="mt-6">{{ $articles->links() }}</div>
    </x-dashboard.card>
</div>
