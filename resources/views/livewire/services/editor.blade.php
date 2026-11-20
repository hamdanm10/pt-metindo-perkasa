@php
    $languages = config('locales.landing');
    $erroredLocales = collect($errors->keys())
        ->map(fn ($key) => preg_match('/^form\.translations\.([a-z]+)\./', $key, $m) ? $m[1] : null)
        ->filter()->unique()->all();
    $textareas = [
        'summary' => 3,
        'description' => 6,
        'included' => 6,
        'fit' => 3,
    ];
@endphp

<div class="space-y-8">
    <x-dashboard.page-header :title="$service === null ? __('services.create_title') : __('services.edit_title')" :description="$service === null ? __('services.create_description') : __('services.edit_description')" />

    <form wire:submit="save" class="space-y-8" novalidate>
        <x-dashboard.card class="max-w-2xl">
            <div class="space-y-6">
                <x-dashboard.field name="form.name" :label="__('services.fields.name')" :hint="__('services.hints.name')" wire:model="form.name" autocomplete="off" required />

                <div>
                    <label class="inline-flex min-h-11 items-center gap-3 font-semibold">
                        <input type="checkbox" wire:model="form.isActive" class="size-5 rounded border-ink/50 accent-brand-600">
                        {{ __('services.fields.active') }}
                    </label>
                    <p class="text-sm text-muted">{{ __('services.hints.active') }}</p>
                </div>

                <div>
                    <label for="form-image" class="field-label">{{ __('services.fields.image') }}@unless ($currentImage)<span class="text-brand-600" aria-hidden="true"> *</span>@endunless</label>
                    <input id="form-image" type="file" wire:model="form.image" accept="image/jpeg,image/png,image/webp" aria-required="{{ $currentImage ? 'false' : 'true' }}"
                           @error('form.image') aria-invalid="true" aria-describedby="form-image-error" @else aria-describedby="form-image-hint" @enderror
                           class="mt-3 block w-full rounded-lg border-2 border-dashed border-ink/25 bg-white p-3 text-base file:me-4 file:min-h-10 file:cursor-pointer file:rounded-md file:border-0 file:bg-brand-600 file:px-4 file:font-bold file:text-white hover:file:bg-brand-700">
                    <p id="form-image-hint" class="mt-2 text-sm text-muted">{{ __('services.hints.image') }}</p>
                    @error('form.image')
                        <p id="form-image-error" class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
                    @enderror
                    <div wire:loading wire:target="form.image" class="mt-2 text-sm text-muted">…</div>

                    @if ($form->image && $form->image->isPreviewable() && ! $errors->has('form.image'))
                        <img src="{{ $form->image->temporaryUrl() }}" alt="" class="mt-4 aspect-[4/3] w-full max-w-sm rounded-lg object-cover">
                    @elseif ($currentImage)
                        <figure class="mt-4">
                            <img src="{{ $currentImage }}" alt="" class="aspect-[4/3] w-full max-w-sm rounded-lg object-cover">
                            <figcaption class="mt-1 text-sm text-muted">{{ __('services.image_current') }}</figcaption>
                        </figure>
                    @endif
                </div>
            </div>
        </x-dashboard.card>

        <x-dashboard.card>
            <h2 class="text-xl font-bold">{{ __('services.fields.languages') }}</h2>
            <p class="mt-1 text-sm text-muted">{{ __('services.hints.languages') }}</p>
            @error('form.translations')
                <p class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
            @enderror

            <nav aria-label="{{ __('services.fields.languages') }}" class="mt-4">
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
                <div wire:key="panel-{{ $code }}" @class(['mt-6 max-w-2xl space-y-6', 'hidden' => $activeLocale !== $code])>
                    <x-dashboard.field :name="'form.translations.'.$code.'.title'" :label="__('services.fields.title')" wire:model="form.translations.{{ $code }}.title" :dir="$dir" :lang="$lang" />

                    @foreach ($textareas as $field => $rows)
                        <div>
                            <label for="form-translations-{{ $code }}-{{ $field }}" class="field-label">{{ __('services.fields.'.$field) }}</label>
                            <textarea id="form-translations-{{ $code }}-{{ $field }}" rows="{{ $rows }}" dir="{{ $dir }}" lang="{{ $lang }}" wire:model="form.translations.{{ $code }}.{{ $field }}"
                                      @error('form.translations.'.$code.'.'.$field) aria-invalid="true" @enderror
                                      class="field-input"></textarea>
                            @if (\Illuminate\Support\Facades\Lang::has('services.hints.'.$field))
                                <p class="mt-2 text-sm text-muted">{{ __('services.hints.'.$field) }}</p>
                            @endif
                            @error('form.translations.'.$code.'.'.$field)
                                <p class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach
                </div>
            @endforeach
        </x-dashboard.card>

        <div class="flex flex-wrap gap-3">
            <x-dashboard.button type="submit" wire:loading.attr="disabled" wire:target="save,form.image">{{ __('services.save') }}</x-dashboard.button>
            <x-dashboard.button variant="secondary" :href="route('app.services.index')">{{ __('services.cancel') }}</x-dashboard.button>
        </div>
    </form>
</div>
