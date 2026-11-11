@php
    $languages = config('locales.landing');
    $erroredLocales = collect($errors->keys())
        ->map(fn ($key) => preg_match('/^form\.translations\.([a-z]+)\./', $key, $m) ? $m[1] : null)
        ->filter()->unique()->all();
    $editorLabels = __('article_manager.editor');
@endphp

<div class="space-y-8">
    <x-dashboard.page-header :title="$article === null ? __('article_manager.create_title') : __('article_manager.edit_title')" />

    <x-dashboard.flash />

    <form wire:submit="save" class="space-y-8" novalidate>
        <x-dashboard.card>
            <div class="max-w-2xl space-y-6">
                <x-dashboard.select name="form.type" :label="__('article_manager.fields.type')" wire:model="form.type" required>
                    @foreach (\App\Enums\ArticleType::cases() as $case)
                        <option value="{{ $case->value }}">{{ __('article_manager.types.'.$case->value) }}</option>
                    @endforeach
                </x-dashboard.select>

                <div>
                    <label for="form-thumbnail" class="field-label">{{ __('article_manager.fields.thumbnail') }}</label>
                    <input id="form-thumbnail" type="file" wire:model="form.thumbnail" accept="image/jpeg,image/png,image/webp"
                           @error('form.thumbnail') aria-invalid="true" aria-describedby="form-thumbnail-error" @else aria-describedby="form-thumbnail-hint" @enderror
                           class="mt-3 block w-full rounded-lg border-2 border-dashed border-ink/25 bg-white p-3 text-base file:me-4 file:min-h-10 file:cursor-pointer file:rounded-md file:border-0 file:bg-brand-600 file:px-4 file:font-bold file:text-white hover:file:bg-brand-700">
                    <p id="form-thumbnail-hint" class="mt-2 text-sm text-muted">{{ __('article_manager.hints.thumbnail') }}</p>
                    @error('form.thumbnail')
                        <p id="form-thumbnail-error" class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
                    @enderror
                    <div wire:loading wire:target="form.thumbnail" class="mt-2 text-sm text-muted">…</div>

                    @if ($form->thumbnail && $form->thumbnail->isPreviewable() && ! $errors->has('form.thumbnail'))
                        <img src="{{ $form->thumbnail->temporaryUrl() }}" alt="" class="mt-4 aspect-video w-full max-w-sm rounded-lg object-cover">
                    @elseif ($currentThumbnail)
                        <figure class="mt-4">
                            <img src="{{ app(\App\Services\Articles\ThumbnailStore::class)->url($currentThumbnail) }}" alt="" class="aspect-video w-full max-w-sm rounded-lg object-cover">
                            <figcaption class="mt-1 text-sm text-muted">{{ __('article_manager.thumbnail_current') }}</figcaption>
                        </figure>
                    @endif
                </div>
            </div>
        </x-dashboard.card>

        <x-dashboard.card>
            <h2 class="text-xl font-bold">{{ __('article_manager.fields.languages') }}</h2>
            <p class="mt-1 text-sm text-muted">{{ __('article_manager.hints.languages') }}</p>
            @error('form.translations')
                <p class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
            @enderror

            <nav aria-label="{{ __('article_manager.fields.languages') }}" class="mt-4">
                <ul class="flex flex-wrap gap-2">
                    @foreach ($languages as $code)
                        <li>
                            <button type="button" wire:click="selectLocale('{{ $code }}')"
                                    @if ($activeLocale === $code) aria-current="true" @endif
                                    class="inline-flex min-h-11 items-center gap-2 rounded-lg border px-4 font-semibold {{ $activeLocale === $code ? 'border-brand-700 bg-brand-700 text-white' : 'border-ink/40 text-ink hover:border-brand-700 hover:text-brand-700' }}">
                                {{ config("locales.locales.{$code}.name") }}
                                @if ($form->translations[$code]['published'])
                                    <span class="size-2 rounded-full bg-current" aria-hidden="true"></span>
                                    <span class="sr-only">{{ __('article_manager.status.published') }}</span>
                                @endif
                                @if (in_array($code, $erroredLocales, true))
                                    <span class="font-bold" aria-hidden="true">!</span>
                                @endif
                            </button>
                        </li>
                    @endforeach
                </ul>
            </nav>

            @foreach ($languages as $code)
                @php($dir = config("locales.locales.{$code}.dir"))
                <div wire:key="panel-{{ $code }}" @class(['mt-6 space-y-6', 'hidden' => $activeLocale !== $code])>
                    <x-dashboard.field :name="'form.translations.'.$code.'.title'" :label="__('article_manager.fields.title')" wire:model="form.translations.{{ $code }}.title" :placeholder="__('article_manager.placeholders.title')" :dir="$dir" lang="{{ config('locales.locales.'.$code.'.html_lang') }}" />
                    <x-dashboard.field :name="'form.translations.'.$code.'.slug'" :label="__('article_manager.fields.slug')" :hint="__('article_manager.hints.slug')" wire:model="form.translations.{{ $code }}.slug" :placeholder="__('article_manager.placeholders.slug')" dir="ltr" autocapitalize="off" spellcheck="false" />

                    <div>
                        <label for="form-translations-{{ $code }}-excerpt" class="field-label">{{ __('article_manager.fields.excerpt') }}</label>
                        <textarea id="form-translations-{{ $code }}-excerpt" rows="3" dir="{{ $dir }}" lang="{{ config('locales.locales.'.$code.'.html_lang') }}" wire:model="form.translations.{{ $code }}.excerpt" placeholder="{{ __('article_manager.placeholders.excerpt') }}"
                                  class="field-input"></textarea>
                        @error('form.translations.'.$code.'.excerpt')
                            <p class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <span id="body-label-{{ $code }}" class="field-label">{{ __('article_manager.fields.body') }}</span>
                        <div wire:ignore data-tiptap data-labelled-by="body-label-{{ $code }}" data-input="body-{{ $code }}"
                             data-dir="{{ $dir }}" data-lang="{{ config('locales.locales.'.$code.'.html_lang') }}"
                             data-labels="{{ json_encode($editorLabels) }}"
                             data-content="{{ $form->translations[$code]['body'] }}"
                             class="mt-2"></div>
                        <textarea id="body-{{ $code }}" wire:model="form.translations.{{ $code }}.body" class="sr-only" tabindex="-1" aria-hidden="true"></textarea>
                        @error('form.translations.'.$code.'.body')
                            <p class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <x-dashboard.field :name="'form.translations.'.$code.'.meta_title'" :label="__('article_manager.fields.meta_title')" wire:model="form.translations.{{ $code }}.meta_title" :placeholder="__('article_manager.placeholders.meta_title')" :dir="$dir" />
                    <x-dashboard.field :name="'form.translations.'.$code.'.meta_description'" :label="__('article_manager.fields.meta_description')" :hint="__('article_manager.hints.meta_description')" wire:model="form.translations.{{ $code }}.meta_description" :placeholder="__('article_manager.placeholders.meta_description')" :dir="$dir" />
                    <x-dashboard.field :name="'form.translations.'.$code.'.image_alt'" :label="__('article_manager.fields.image_alt')" wire:model="form.translations.{{ $code }}.image_alt" :placeholder="__('article_manager.placeholders.image_alt')" :dir="$dir" />

                    <div>
                        <label class="inline-flex min-h-11 items-center gap-3 font-semibold">
                            <input type="checkbox" wire:model="form.translations.{{ $code }}.published" class="size-5 rounded border-ink/50 accent-brand-600">
                            {{ __('article_manager.fields.published') }}
                        </label>
                        <p class="text-sm text-muted">{{ __('article_manager.hints.published') }}</p>
                    </div>
                </div>
            @endforeach
        </x-dashboard.card>

        <div class="flex flex-wrap gap-3">
            <x-dashboard.button type="submit" wire:loading.attr="disabled" wire:target="save,form.thumbnail">{{ __('article_manager.save') }}</x-dashboard.button>
            <x-dashboard.button variant="secondary" :href="route('app.articles.index')">{{ __('article_manager.cancel') }}</x-dashboard.button>
        </div>
    </form>
</div>
