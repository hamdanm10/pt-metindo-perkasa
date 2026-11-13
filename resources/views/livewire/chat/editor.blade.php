@php
    $erroredLocales = collect($errors->keys())
        ->map(fn ($key) => preg_match('/^form\.translations\.([a-z]+)\./', $key, $m) ? $m[1] : null)
        ->filter()->unique()->all();
    $filledLocales = $form->activeLocales();
@endphp

<div>
    <x-dashboard.page-header :title="$entry === null ? __('chat_manager.create_title') : __('chat_manager.edit_title')" :description="$entry === null ? __('chat_manager.create_description') : __('chat_manager.edit_description')" />

    <x-dashboard.card class="max-w-3xl">
        @if ($parentLabel !== null)
            <p class="mb-6 rounded-lg bg-lavender px-4 py-3 text-sm"><span class="font-bold text-muted">{{ __('chat_manager.follows') }}</span> <span class="font-semibold">{{ $parentLabel }}</span></p>
        @endif

        <form wire:submit="save" class="space-y-8" novalidate>
            <div>
                <h2 class="text-xl font-bold">{{ __('chat_manager.fields.languages') }}</h2>
                <p class="mt-1 text-sm text-muted">{{ __('chat_manager.hints.languages') }}</p>
                @error('form.translations')
                    <p class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
                @enderror

                <nav aria-label="{{ __('chat_manager.fields.languages') }}" class="mt-4">
                    <ul class="flex flex-wrap gap-2">
                        @foreach ($languages as $code)
                            <li>
                                <button type="button" wire:click="selectLocale('{{ $code }}')"
                                        @if ($activeLocale === $code) aria-current="true" @endif
                                        class="inline-flex min-h-11 items-center gap-2 rounded-lg border px-4 font-semibold {{ $activeLocale === $code ? 'border-brand-700 bg-brand-700 text-white' : 'border-ink/40 text-ink hover:border-brand-700 hover:text-brand-700' }}">
                                    {{ config("locales.locales.{$code}.name") }}
                                    @if (in_array($code, $filledLocales, true))
                                        <span class="size-2 rounded-full bg-current" aria-hidden="true"></span>
                                        <span class="sr-only">{{ __('chat_manager.filled') }}</span>
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
                    @php($lang = config("locales.locales.{$code}.html_lang"))
                    <div wire:key="panel-{{ $code }}" @class(['mt-6 space-y-6', 'hidden' => $activeLocale !== $code])>
                        <x-dashboard.field :name="'form.translations.'.$code.'.question'" :label="__('chat_manager.fields.question')" wire:model="form.translations.{{ $code }}.question" :dir="$dir" lang="{{ $lang }}" autocomplete="off" />

                        <div>
                            <label for="form-translations-{{ $code }}-answer" class="field-label">{{ __('chat_manager.fields.answer') }}</label>
                            <textarea id="form-translations-{{ $code }}-answer" rows="5" dir="{{ $dir }}" lang="{{ $lang }}" wire:model="form.translations.{{ $code }}.answer"
                                      @error('form.translations.'.$code.'.answer') aria-invalid="true" aria-describedby="form-translations-{{ $code }}-answer-error" @enderror
                                      class="field-input"></textarea>
                            @error('form.translations.'.$code.'.answer')
                                <p id="form-translations-{{ $code }}-answer-error" class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <x-dashboard.field :name="'form.translations.'.$code.'.link_label'" :label="__('chat_manager.fields.link_label')" wire:model="form.translations.{{ $code }}.link_label" :dir="$dir" lang="{{ $lang }}" autocomplete="off" />
                            <x-dashboard.field :name="'form.translations.'.$code.'.link_url'" :label="__('chat_manager.fields.link_url')" wire:model="form.translations.{{ $code }}.link_url" dir="ltr" autocomplete="off" :hint="__('chat_manager.hints.link_url')" />
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex flex-wrap gap-3">
                <x-dashboard.button type="submit">{{ __('chat_manager.save') }}</x-dashboard.button>
                <x-dashboard.button variant="secondary" :href="route('app.chat.index')">{{ __('chat_manager.cancel') }}</x-dashboard.button>
            </div>
        </form>
    </x-dashboard.card>
</div>
