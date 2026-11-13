<div>
    <x-dashboard.page-header :title="__('chat_manager.title')" :description="__('chat_manager.description')" />

    <x-dashboard.flash />

    <x-dashboard.card>
        <div class="flex justify-end">
            <x-dashboard.button :href="route('app.chat.create')">{{ __('chat_manager.create') }}</x-dashboard.button>
        </div>

        <div class="mt-6">
            <x-dashboard.table>
                <thead>
                    <tr>
                        <th scope="col">{{ __('chat_manager.fields.question') }}</th>
                        <th scope="col">{{ __('chat_manager.fields.languages') }}</th>
                        <th scope="col"><span class="sr-only">{{ __('chat_manager.actions') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr wire:key="chat-{{ $row['entry']->id }}" class="align-middle">
                            <td>
                                <div class="flex items-baseline gap-3" style="padding-inline-start: {{ min($row['depth'], 8) * 1.75 }}rem">
                                    <span class="shrink-0 text-sm font-bold text-muted tabular-nums" aria-hidden="true">{{ $row['number'] }}</span>
                                    <span class="cell-truncate font-semibold" title="{{ $row['label'] }}">
                                        <span class="sr-only">{{ __('chat_manager.level', ['number' => $row['number']]) }}: </span>{{ $row['label'] }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <ul class="flex gap-1.5">
                                    @foreach ($row['locales'] as $code)
                                        <li><x-dashboard.badge tone="muted"><span title="{{ config("locales.locales.{$code}.name") }}">{{ strtoupper($code) }}</span></x-dashboard.badge></li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-end whitespace-nowrap">
                                <a href="{{ route('app.chat.create', ['parent' => $row['entry']->id]) }}" class="row-action">{{ __('chat_manager.add_follow_up') }}<span class="sr-only"> {{ $row['label'] }}</span></a>
                                <a href="{{ route('app.chat.edit', $row['entry']) }}" class="row-action">{{ __('chat_manager.edit') }}<span class="sr-only"> {{ $row['label'] }}</span></a>
                                <button type="button" wire:click="move({{ $row['entry']->id }}, 'up')" @disabled($row['first']) class="row-action disabled:cursor-not-allowed disabled:opacity-40">{{ __('chat_manager.move_up') }}<span class="sr-only"> {{ $row['label'] }}</span></button>
                                <button type="button" wire:click="move({{ $row['entry']->id }}, 'down')" @disabled($row['last']) class="row-action disabled:cursor-not-allowed disabled:opacity-40">{{ __('chat_manager.move_down') }}<span class="sr-only"> {{ $row['label'] }}</span></button>
                                <button type="button" wire:click="delete({{ $row['entry']->id }})" data-confirm="{{ __('chat_manager.confirm_delete') }}" data-confirm-label="{{ __('chat_manager.delete') }}" class="row-action row-action--danger">{{ __('chat_manager.delete') }}<span class="sr-only"> {{ $row['label'] }}</span></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-muted">{{ __('chat_manager.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-dashboard.table>
        </div>
    </x-dashboard.card>
</div>
