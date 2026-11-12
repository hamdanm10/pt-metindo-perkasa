<div>
    <x-dashboard.page-header :title="$entry === null ? __('chat_manager.create_title') : __('chat_manager.edit_title')" :description="$entry === null ? __('chat_manager.create_description') : __('chat_manager.edit_description')" />

    <x-dashboard.card class="max-w-3xl">
        @if ($parentLabel !== null)
            <p class="mb-6 rounded-lg bg-lavender px-4 py-3 text-sm"><span class="font-bold text-muted">{{ __('chat_manager.follows') }}</span> <span class="font-semibold">{{ $parentLabel }}</span></p>
        @endif

        <form wire:submit="save" class="space-y-8" novalidate>
            @error('form.translations')
                <p class="text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
            @enderror
            <p class="text-sm text-muted">{{ __('chat_manager.hints.languages') }}</p>

            @foreach ($languages as $code)
                @php($dir = config("locales.locales.{$code}.dir"))
                @php($lang = config("locales.locales.{$code}.html_lang"))
                <fieldset class="space-y-4 rounded-lg border border-ink/15 p-4 sm:p-5">
                    <legend class="px-2 text-base font-black">{{ config("locales.locales.{$code}.name") }}</legend>

                    <x-dashboard.field :name="'form.translations.'.$code.'.question'" :label="__('chat_manager.fields.question')" wire:model="form.translations.{{ $code }}.question" :dir="$dir" lang="{{ $lang }}" autocomplete="off" />

                    <div>
                        <label for="form-translations-{{ $code }}-answer" class="field-label">{{ __('chat_manager.fields.answer') }}</label>
                        <textarea id="form-translations-{{ $code }}-answer" rows="4" dir="{{ $dir }}" lang="{{ $lang }}" wire:model="form.translations.{{ $code }}.answer"
                                  @error('form.translations.'.$code.'.answer') aria-invalid="true" aria-describedby="form-translations-{{ $code }}-answer-error" @enderror
                                  class="field-input"></textarea>
                        @error('form.translations.'.$code.'.answer')
                            <p id="form-translations-{{ $code }}-answer-error" class="mt-2 text-sm font-bold text-brand-700" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-dashboard.field :name="'form.translations.'.$code.'.link_label'" :label="__('chat_manager.fields.link_label')" wire:model="form.translations.{{ $code }}.link_label" :dir="$dir" lang="{{ $lang }}" autocomplete="off" />
                        <x-dashboard.field :name="'form.translations.'.$code.'.link_url'" :label="__('chat_manager.fields.link_url')" wire:model="form.translations.{{ $code }}.link_url" dir="ltr" autocomplete="off" :hint="__('chat_manager.hints.link_url')" />
                    </div>
                </fieldset>
            @endforeach

            <div class="flex flex-wrap gap-3">
                <x-dashboard.button type="submit">{{ __('chat_manager.save') }}</x-dashboard.button>
                <x-dashboard.button variant="secondary" :href="route('app.chat.index')">{{ __('chat_manager.cancel') }}</x-dashboard.button>
            </div>
        </form>
    </x-dashboard.card>
</div>
