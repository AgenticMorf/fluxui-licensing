<div class="space-y-6">
    {{-- Toolbar --}}
    <div class="flex flex-wrap items-center gap-3">
        <flux:input
            wire:model.live.debounce.300ms="search"
            :placeholder="__('Search by UID…')"
            icon="magnifying-glass"
            class="max-w-xs"
        />

        <flux:select wire:model.live="statusFilter" class="max-w-xs" :placeholder="__('All statuses')">
            <flux:select.option value="">{{ __('All statuses') }}</flux:select.option>
            @foreach ($statuses as $status)
                <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="scopeFilter" class="max-w-xs" :placeholder="__('All scopes')">
            <flux:select.option value="">{{ __('All scopes') }}</flux:select.option>
            @foreach ($scopes as $scope)
                <flux:select.option value="{{ $scope->id }}">{{ $scope->name }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:spacer />

        <flux:button type="button" variant="primary" icon="plus" wire:click="openCreateModal">
            {{ __('New license') }}
        </flux:button>
    </div>

    {{-- Table --}}
    @if ($licenses->isEmpty())
        <flux:text variant="subtle">{{ __('No licenses found.') }}</flux:text>
    @else
        <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-white/10">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-white/5">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">{{ __('UID') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">{{ __('Scope') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">{{ __('Usages') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">{{ __('Expires') }}</th>
                        <th class="px-4 py-3 text-right font-medium text-zinc-500 dark:text-zinc-400">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-white/5">
                    @foreach ($licenses as $license)
                        <tr wire:key="license-{{ $license->id }}" class="hover:bg-zinc-50 dark:hover:bg-white/[0.03]">
                            <td class="px-4 py-3">
                                <div class="font-mono text-xs font-medium text-zinc-800 dark:text-zinc-200">
                                    {{ $license->uid }}
                                </div>
                                @if ($license->template)
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $license->template->name }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if ($license->scope)
                                    <flux:badge size="sm" color="blue">{{ $license->scope->name }}</flux:badge>
                                @else
                                    <span class="text-zinc-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColor = match ($license->status) {
                                        \LucaLongo\Licensing\Enums\LicenseStatus::Active => 'green',
                                        \LucaLongo\Licensing\Enums\LicenseStatus::Pending => 'yellow',
                                        \LucaLongo\Licensing\Enums\LicenseStatus::Grace => 'blue',
                                        \LucaLongo\Licensing\Enums\LicenseStatus::Expired,
                                        \LucaLongo\Licensing\Enums\LicenseStatus::Suspended,
                                        \LucaLongo\Licensing\Enums\LicenseStatus::Cancelled => 'red',
                                        default => 'zinc',
                                    };
                                @endphp
                                <flux:badge size="sm" :color="$statusColor">{{ $license->status->label() }}</flux:badge>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-mono text-xs {{ $license->usages_count >= $license->max_usages ? 'text-red-600 dark:text-red-400' : 'text-zinc-700 dark:text-zinc-300' }}">
                                    {{ $license->usages_count }}/{{ $license->max_usages }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($license->expires_at)
                                    <span class="text-xs {{ $license->expires_at->isPast() ? 'text-red-600 dark:text-red-400' : 'text-zinc-700 dark:text-zinc-300' }}">
                                        {{ $license->expires_at->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-xs text-zinc-400">{{ __('Never') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-1">
                                    @if ($license->status === \LucaLongo\Licensing\Enums\LicenseStatus::Pending)
                                        <flux:button
                                            size="xs"
                                            variant="ghost"
                                            icon="play"
                                            wire:click="confirmActivate('{{ $license->id }}')"
                                        >{{ __('Activate') }}</flux:button>
                                    @endif

                                    @if ($license->status === \LucaLongo\Licensing\Enums\LicenseStatus::Active)
                                        <flux:button
                                            size="xs"
                                            variant="ghost"
                                            icon="pause"
                                            wire:click="confirmSuspend('{{ $license->id }}')"
                                        >{{ __('Suspend') }}</flux:button>
                                    @endif

                                    @if ($license->canRetrieveKey())
                                        <flux:button
                                            size="xs"
                                            variant="ghost"
                                            icon="key"
                                            wire:click="showKey('{{ $license->id }}')"
                                        >{{ __('Key') }}</flux:button>
                                    @endif

                                    @if ($license->canRegenerateKey())
                                        <flux:button
                                            size="xs"
                                            variant="ghost"
                                            icon="arrow-path"
                                            wire:click="confirmRegenerateKey('{{ $license->id }}')"
                                        >{{ __('Regen') }}</flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-center">
            {{ $licenses->links() }}
        </div>
    @endif

    {{-- Create modal --}}
    <flux:modal wire:model.self="showCreateModal" class="max-w-lg">
        <form wire:submit.prevent="createLicense" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('New license') }}</flux:heading>
                <flux:subheading class="mt-1">{{ __('A license key will be generated automatically. You will only see the key once.') }}</flux:subheading>
            </div>

            <flux:field>
                <flux:label>{{ __('Scope') }}</flux:label>
                <flux:select wire:model.live="createScopeId">
                    <flux:select.option value="">{{ __('Select a scope…') }}</flux:select.option>
                    @foreach ($scopes as $scope)
                        <flux:select.option value="{{ $scope->id }}">{{ $scope->name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="createScopeId" />
            </flux:field>

            @if ($templatesForScope->isNotEmpty())
                <flux:field>
                    <flux:label>{{ __('Template') }}</flux:label>
                    <flux:select wire:model="createTemplateId">
                        <flux:select.option value="">{{ __('No template') }}</flux:select.option>
                        @foreach ($templatesForScope as $template)
                            <flux:select.option value="{{ $template->id }}">{{ $template->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>
            @endif

            <flux:field>
                <flux:label>{{ __('Max usages') }}</flux:label>
                <flux:input wire:model="createMaxUsages" type="number" min="1" />
                <flux:error name="createMaxUsages" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Expires at') }}</flux:label>
                <flux:input wire:model="createExpiresAt" type="datetime-local" />
                <flux:description>{{ __('Leave blank for a non-expiring license.') }}</flux:description>
            </flux:field>

            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="closeCreateModal">{{ __('Cancel') }}</flux:button>
                <flux:button type="submit" variant="primary">{{ __('Create') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Key reveal modal --}}
    <flux:modal wire:model.self="showKeyModal" class="max-w-lg">
        <div class="space-y-4">
            <div>
                <flux:heading size="lg">{{ __('License key') }}</flux:heading>
                @if ($revealedLicenseUid)
                    <flux:subheading class="mt-1 font-mono text-xs">{{ $revealedLicenseUid }}</flux:subheading>
                @endif
                <flux:subheading class="mt-2">
                    {{ __('Copy this key now. For security reasons, it may not be shown again.') }}
                </flux:subheading>
            </div>

            @if ($revealedKey)
                <flux:field>
                    <flux:label>{{ __('Key') }}</flux:label>
                    <div class="flex gap-2">
                        <flux:input readonly :value="$revealedKey" class="font-mono text-sm" />
                        <flux:button
                            type="button"
                            variant="subtle"
                            icon="clipboard-document"
                            x-on:click="navigator.clipboard.writeText(@js($revealedKey))"
                        >{{ __('Copy') }}</flux:button>
                    </div>
                </flux:field>

                <flux:callout variant="warning">
                    {{ __('Store this key securely. If you lose it, regenerate the key.') }}
                </flux:callout>
            @endif

            <div class="flex justify-end">
                <flux:button type="button" variant="primary" wire:click="closeKeyModal">{{ __('Done') }}</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Confirm action modal --}}
    <flux:modal wire:model.self="showConfirmModal" class="max-w-md">
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Confirm action') }}</flux:heading>
            <flux:text>{{ $confirmMessage }}</flux:text>
            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="cancelConfirm">{{ __('Cancel') }}</flux:button>
                <flux:button type="button" variant="primary" wire:click="executeConfirmedAction">{{ __('Confirm') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
