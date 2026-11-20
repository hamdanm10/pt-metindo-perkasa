@php
    $languages = config('locales.landing');
    $erroredLocales = collect($errors->keys())
        ->map(fn ($key) => preg_match('/^form\.descriptions\.([a-z]+)$/', $key, $m) ? $m[1] : null)
        ->filter()->unique()->all();
@endphp

<div class="space-y-8">
    <x-dashboard.page-header :title="$processCategory === null ? __('process_categories.create_title') : __('process_categories.edit_title')" :description="$processCategory === null ? __('process_categories.create_description') : __('process_categories.edit_description')" />

    <form wire:submit="save" class="space-y-8" novalidate>
        <x-dashboard.card class="max-w-2xl">
            <div class="space-y-6">
                <x-dashboard.field name="form.name" :label="__('process_categories.fields.name')" :hint="__('process_categories.hints.name')" wire:model="form.name" autocomplete="off" required />

                <div>
                    <label class="inline-flex min-h-11 items-center gap-3 font-semibold">
                        <input type="checkbox" wire:model="form.isActive" class="size-5 rounded border-ink/50 accent-brand-600">
                        {{ __('process_categories.fields.active') }}
                    </label>
                    <p class="text-sm text-muted">{{ __('process_categories.hints.active') }}</p>
                </div>
            </div>
        </x-dashboard.card>

        <x-dashboard.card>
            <h2 class="text-xl font-bold">{{ __('process_categories.fields.languages') }}</h2>
            <p class="mt-1 text-sm text-muted">{{ __('process_categories.hints.languages') }}</p>
            @error('form.descriptions')
                <p class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
            @enderror

            <nav aria-label="{{ __('process_categories.fields.languages') }}" class="mt-4">
                <ul class="flex flex-wrap gap-2">
                    @foreach ($languages as $code)
                        <li>
                            <button type="button" wire:click="selectLocale('{{ $code }}')"
                                    @if ($activeLocale === $code) aria-current="true" @endif
                                    class="inline-flex min-h-11 items-center gap-2 rounded-lg border px-4 font-semibold {{ $activeLocale === $code ? 'border-brand-700 bg-brand-700 text-white' : 'border-ink/40 text-ink hover:border-brand-700 hover:text-brand-700' }}">
                                {{ config("locales.locales.{$code}.name") }}
                                @if (in_array($code, $erroredLocales, true))
                                    <span class="font-bold" aria-hidden="true">!</span>
                                @endif
                            </button>
                        </li>
                    @endforeach
                </ul>
            </nav>

            @foreach ($languages as $code)
                @php
                    $dir = config("locales.locales.{$code}.dir");
                    $lang = config("locales.locales.{$code}.html_lang");
                @endphp
                <div wire:key="panel-{{ $code }}" @class(['mt-6 max-w-2xl', 'hidden' => $activeLocale !== $code])>
                    <label for="form-descriptions-{{ $code }}" class="field-label">{{ __('process_categories.fields.description') }}</label>
                    <textarea id="form-descriptions-{{ $code }}" rows="6" dir="{{ $dir }}" lang="{{ $lang }}" wire:model="form.descriptions.{{ $code }}"
                              @error('form.descriptions.'.$code) aria-invalid="true" @enderror
                              class="field-input"></textarea>
                    @error('form.descriptions.'.$code)
                        <p class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach
        </x-dashboard.card>

        <div class="flex flex-wrap gap-3">
            <x-dashboard.button type="submit">{{ __('process_categories.save') }}</x-dashboard.button>
            <x-dashboard.button variant="secondary" :href="route('app.process-categories.index')">{{ __('process_categories.cancel') }}</x-dashboard.button>
        </div>
    </form>
</div>
