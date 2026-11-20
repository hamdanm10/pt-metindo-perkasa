@php
    $languages = config('locales.landing');
    $erroredLocales = collect($errors->keys())
        ->map(fn ($key) => preg_match('/^form\.descriptions\.([a-z]+)$/', $key, $m) ? $m[1] : null)
        ->filter()->unique()->all();
@endphp

<div class="space-y-8">
    <x-dashboard.page-header :title="$product === null ? __('products.create_title') : __('products.edit_title')" :description="$product === null ? __('products.create_description') : __('products.edit_description')" />

    <form wire:submit="save" class="space-y-8" novalidate>
        <x-dashboard.card class="max-w-2xl">
            <div class="space-y-6">
                <x-dashboard.field name="form.name" :label="__('products.fields.name')" :hint="__('products.hints.name')" wire:model="form.name" autocomplete="off" required />

                <div>
                    <label class="inline-flex min-h-11 items-center gap-3 font-semibold">
                        <input type="checkbox" wire:model="form.isActive" class="size-5 rounded border-ink/50 accent-brand-600">
                        {{ __('products.fields.active') }}
                    </label>
                    <p class="text-sm text-muted">{{ __('products.hints.active') }}</p>
                </div>

                <x-dashboard.field name="form.brochureUrl" :label="__('products.fields.brochure_url')" :hint="__('products.hints.url')" type="url" wire:model="form.brochureUrl" autocomplete="off" />
                <x-dashboard.field name="form.videoUrl" :label="__('products.fields.video_url')" :hint="__('products.hints.url')" type="url" wire:model="form.videoUrl" autocomplete="off" />
                <x-dashboard.field name="form.price" :label="__('products.fields.price')" :hint="__('products.hints.price')" inputmode="numeric" wire:model="form.price" autocomplete="off" />

                <div>
                    <label for="form-tds" class="field-label">{{ __('products.fields.tds') }}</label>
                    <input id="form-tds" type="file" wire:model="form.tds" accept="application/pdf"
                           @error('form.tds') aria-invalid="true" aria-describedby="form-tds-error" @else aria-describedby="form-tds-hint" @enderror
                           class="mt-3 block w-full rounded-lg border-2 border-dashed border-ink/25 bg-white p-3 text-base file:me-4 file:min-h-10 file:cursor-pointer file:rounded-md file:border-0 file:bg-brand-600 file:px-4 file:font-bold file:text-white hover:file:bg-brand-700">
                    <p id="form-tds-hint" class="mt-2 text-sm text-muted">{{ __('products.hints.tds') }}</p>
                    @error('form.tds')
                        <p id="form-tds-error" class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
                    @enderror
                    <div wire:loading wire:target="form.tds" class="mt-2 text-sm text-muted">…</div>

                    @if ($currentTds)
                        <p class="mt-3 text-sm"><a href="{{ $currentTds }}" target="_blank" rel="noopener noreferrer" class="font-semibold underline underline-offset-4">{{ __('products.tds_current') }}</a></p>
                        <label class="mt-1 inline-flex min-h-11 items-center gap-3 text-sm font-semibold">
                            <input type="checkbox" wire:model="form.removeTds" class="size-5 rounded border-ink/50 accent-brand-600">
                            {{ __('products.tds_remove') }}
                        </label>
                    @endif
                </div>
            </div>
        </x-dashboard.card>

        <x-dashboard.card class="max-w-2xl">
            <h2 class="text-xl font-bold">{{ __('products.fields.links') }}</h2>
            <p class="mt-1 text-sm text-muted">{{ __('products.hints.links') }}</p>
            @error('form.links')
                <p class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
            @enderror

            <ul class="mt-4 space-y-4">
                @foreach ($form->links as $index => $link)
                    <li wire:key="link-{{ $index }}" class="grid gap-3 sm:grid-cols-[1fr_2fr_auto] sm:items-start">
                        <x-dashboard.field :name="'form.links.'.$index.'.label'" :label="__('products.fields.link_label')" wire:model="form.links.{{ $index }}.label" autocomplete="off" />
                        <x-dashboard.field :name="'form.links.'.$index.'.url'" :label="__('products.fields.link_url')" type="url" wire:model="form.links.{{ $index }}.url" autocomplete="off" />
                        <div class="sm:pt-8">
                            <button type="button" wire:click="removeLink({{ $index }})" class="row-action">{{ __('products.link_remove') }}<span class="sr-only"> {{ $index + 1 }}</span></button>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="mt-4">
                <x-dashboard.button type="button" variant="secondary" wire:click="addLink">{{ __('products.link_add') }}</x-dashboard.button>
            </div>
        </x-dashboard.card>

        <x-dashboard.card>
            <h2 class="text-xl font-bold">{{ __('products.fields.languages') }}</h2>
            <p class="mt-1 text-sm text-muted">{{ __('products.hints.languages') }}</p>
            @error('form.descriptions')
                <p class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
            @enderror

            <nav aria-label="{{ __('products.fields.languages') }}" class="mt-4">
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
                    <label for="form-descriptions-{{ $code }}" class="field-label">{{ __('products.fields.description') }}</label>
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
            <x-dashboard.button type="submit" wire:loading.attr="disabled" wire:target="save,form.tds">{{ __('products.save') }}</x-dashboard.button>
            <x-dashboard.button variant="secondary" :href="route('app.products.index')">{{ __('products.cancel') }}</x-dashboard.button>
        </div>
    </form>
</div>
