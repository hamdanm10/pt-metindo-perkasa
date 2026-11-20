@php
    $languages = config('locales.landing');
    $erroredLocales = collect($errors->keys())
        ->map(fn ($key) => preg_match('/^form\.descriptions\.([a-z]+)$/', $key, $m) || preg_match('/^form\.steps\.\d+\.descriptions\.([a-z]+)$/', $key, $m) ? $m[1] : null)
        ->filter()->unique()->all();
    $stepCount = count($form->steps);
@endphp

<div class="space-y-8">
    <x-dashboard.page-header :title="$process === null ? __('processes.create_title') : __('processes.edit_title')" :description="$process === null ? __('processes.create_description') : __('processes.edit_description')" />

    <form wire:submit="save" class="space-y-8" novalidate>
        <x-dashboard.card class="max-w-2xl">
            <div class="space-y-6">
                <x-dashboard.field name="form.name" :label="__('processes.fields.name')" :hint="__('processes.hints.name')" wire:model="form.name" autocomplete="off" required />

                <x-dashboard.lookup-field name="form.processCategoryId" :label="__('processes.fields.category')">
                    <livewire:lookup source="process_category" wire:model="form.processCategoryId" input-id="form-processCategoryId" :placeholder="__('processes.choose_category')" :invalid="$errors->has('form.processCategoryId')" />
                </x-dashboard.lookup-field>

                <div>
                    <label class="inline-flex min-h-11 items-center gap-3 font-semibold">
                        <input type="checkbox" wire:model="form.isActive" class="size-5 rounded border-ink/50 accent-brand-600">
                        {{ __('processes.fields.active') }}
                    </label>
                    <p class="text-sm text-muted">{{ __('processes.hints.active') }}</p>
                </div>
            </div>
        </x-dashboard.card>

        <x-dashboard.card>
            <h2 class="text-xl font-bold">{{ __('processes.fields.steps') }}</h2>
            <p class="mt-1 text-sm text-muted">{{ __('processes.hints.steps') }}</p>
            @error('form.steps')
                <p class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
            @enderror

            @if ($stepCount === 0)
                <p class="mt-4 rounded-lg border-2 border-dashed border-ink/25 p-4 text-muted">{{ __('processes.steps_empty') }}</p>
            @endif

            <ol class="mt-4 space-y-4">
                @foreach ($form->steps as $index => $step)
                    <li wire:key="step-{{ $index }}" class="rounded-lg border border-ink/15 p-4">
                        <div class="flex flex-wrap items-start gap-3">
                            <span class="mt-8 inline-flex size-9 shrink-0 items-center justify-center rounded-full bg-brand-600 text-sm font-extrabold text-white" aria-hidden="true">{{ $index + 1 }}</span>
                            <div class="min-w-0 flex-1 sm:max-w-md">
                                <x-dashboard.field :name="'form.steps.'.$index.'.name'" :label="__('processes.fields.step_name')" wire:model="form.steps.{{ $index }}.name" autocomplete="off" />
                            </div>
                            <div class="flex flex-wrap gap-1 sm:mt-8">
                                <button type="button" wire:click="moveStep({{ $index }}, true)" @disabled($index === 0) class="row-action disabled:cursor-not-allowed disabled:opacity-40">{{ __('processes.move_up') }}<span class="sr-only"> {{ $index + 1 }}</span></button>
                                <button type="button" wire:click="moveStep({{ $index }}, false)" @disabled($index === $stepCount - 1) class="row-action disabled:cursor-not-allowed disabled:opacity-40">{{ __('processes.move_down') }}<span class="sr-only"> {{ $index + 1 }}</span></button>
                                <button type="button" wire:click="removeStep({{ $index }})" class="row-action">{{ __('processes.step_remove') }}<span class="sr-only"> {{ $index + 1 }}</span></button>
                            </div>
                        </div>

                        <div class="mt-4">
                            <p class="field-label">{{ __('processes.fields.step_products') }}</p>
                            @if ($step['products'] !== [])
                                <ul class="mt-2 flex flex-wrap gap-2">
                                    @foreach ($step['products'] as $productId)
                                        <li wire:key="step-{{ $index }}-product-{{ $productId }}">
                                            <span class="inline-flex min-h-9 items-center gap-2 rounded-lg border border-ink/30 bg-white ps-3 text-sm font-semibold">
                                                {{ $productNames[$productId] ?? '#'.$productId }}
                                                <button type="button" wire:click="removeProduct({{ $index }}, {{ $productId }})" class="inline-flex min-h-9 items-center px-2 text-brand-700">
                                                    <span aria-hidden="true">×</span><span class="sr-only">{{ __('processes.product_remove', ['name' => $productNames[$productId] ?? '#'.$productId]) }}</span>
                                                </button>
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            @error('form.steps.'.$index.'.products')
                                <p class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
                            @enderror
                            @error('form.steps.'.$index.'.products.*')
                                <p class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
                            @enderror
                            <div class="mt-3 max-w-md">
                                <livewire:lookup source="product" wire:model.live="picker.{{ $index }}" :input-id="'picker-'.$index" :placeholder="__('processes.add_product')" :invalid="false" wire:key="picker-{{ $index }}-{{ count($step['products']) }}" />
                            </div>
                        </div>
                    </li>
                @endforeach
            </ol>

            <div class="mt-4">
                <x-dashboard.button type="button" variant="secondary" wire:click="addStep">{{ __('processes.step_add') }}</x-dashboard.button>
            </div>
        </x-dashboard.card>

        <x-dashboard.card>
            <h2 class="text-xl font-bold">{{ __('processes.fields.languages') }}</h2>
            <p class="mt-1 text-sm text-muted">{{ __('processes.hints.languages') }}</p>
            @error('form.descriptions')
                <p class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
            @enderror

            <nav aria-label="{{ __('processes.fields.languages') }}" class="mt-4">
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
                    <div>
                        <label for="form-descriptions-{{ $code }}" class="field-label">{{ __('processes.fields.description') }}</label>
                        <textarea id="form-descriptions-{{ $code }}" rows="5" dir="{{ $dir }}" lang="{{ $lang }}" wire:model="form.descriptions.{{ $code }}"
                                  @error('form.descriptions.'.$code) aria-invalid="true" @enderror
                                  class="field-input"></textarea>
                        @error('form.descriptions.'.$code)
                            <p class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    @foreach ($form->steps as $index => $step)
                        <div wire:key="step-text-{{ $code }}-{{ $index }}">
                            <label for="form-steps-{{ $index }}-{{ $code }}" class="field-label">{{ __('processes.fields.step_description') }} {{ $index + 1 }}@if ($step['name'] !== ''): {{ $step['name'] }}@endif</label>
                            <textarea id="form-steps-{{ $index }}-{{ $code }}" rows="3" dir="{{ $dir }}" lang="{{ $lang }}" wire:model="form.steps.{{ $index }}.descriptions.{{ $code }}"
                                      @error('form.steps.'.$index.'.descriptions.'.$code) aria-invalid="true" @enderror
                                      class="field-input"></textarea>
                            @error('form.steps.'.$index.'.descriptions.'.$code)
                                <p class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach
                </div>
            @endforeach
        </x-dashboard.card>

        <div class="flex flex-wrap gap-3">
            <x-dashboard.button type="submit">{{ __('processes.save') }}</x-dashboard.button>
            <x-dashboard.button variant="secondary" :href="route('app.processes.index')">{{ __('processes.cancel') }}</x-dashboard.button>
        </div>
    </form>
</div>
