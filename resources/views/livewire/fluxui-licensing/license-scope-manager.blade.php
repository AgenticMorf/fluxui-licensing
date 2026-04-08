<div class="space-y-6">
    <div class="flex flex-wrap items-center gap-3">
        <flux:button type="button" variant="primary" icon="plus" wire:click="openCreateModal">
            {{ __('New scope') }}
        </flux:button>
    </div>

    @if ($scopes->isEmpty())
        <flux:text variant="subtle">{{ __('No scopes yet. Create a scope to group your licenses.') }}</flux:text>
    @else
        <div class="divide-y divide-zinc-200 overflow-hidden rounded-xl border border-zinc-200 dark:divide-white/10 dark:border-white/10">
            @foreach ($scopes as $scope)
                <div
                    class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between"
                    wire:key="scope-{{ $scope->id }}"
                >
                    <div class="min-w-0 flex-1 space-y-1">
                        <div class="flex items-center gap-2">
                            <flux:text class="font-medium">{{ $scope->name }}</flux:text>
                            <flux:badge size="sm" color="zinc">{{ $scope->licenses_count }} {{ __('licenses') }}</flux:badge>
                        </div>
                        @if ($scope->description)
                            <flux:text variant="subtle" class="text-xs">{{ $scope->description }}</flux:text>
                        @endif
                    </div>
                    <div class="flex gap-1">
                        <flux:button
                            type="button"
                            variant="ghost"
                            size="sm"
                            icon="pencil"
                            wire:click="openEditModal('{{ $scope->id }}')"
                        >{{ __('Edit') }}</flux:button>
                        <flux:button
                            type="button"
                            variant="ghost"
                            size="sm"
                            icon="trash"
                            wire:click="confirmDelete('{{ $scope->id }}')"
                        >{{ __('Delete') }}</flux:button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Create modal --}}
    <flux:modal wire:model.self="showCreateModal" class="max-w-lg">
        <form wire:submit.prevent="createScope" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('New scope') }}</flux:heading>
                <flux:subheading class="mt-1">{{ __('Scopes group related licenses together (e.g. a product or feature set).') }}</flux:subheading>
            </div>

            <flux:field>
                <flux:label>{{ __('Name') }}</flux:label>
                <flux:input wire:model="createName" :placeholder="__('e.g. Pro Plan, Enterprise')" autocomplete="off" />
                <flux:error name="createName" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Description') }}</flux:label>
                <flux:textarea wire:model="createDescription" :placeholder="__('Optional description')" rows="2" />
            </flux:field>

            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="closeCreateModal">{{ __('Cancel') }}</flux:button>
                <flux:button type="submit" variant="primary">{{ __('Create') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Edit modal --}}
    <flux:modal wire:model.self="showEditModal" class="max-w-lg">
        <form wire:submit.prevent="updateScope" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Edit scope') }}</flux:heading>
            </div>

            <flux:field>
                <flux:label>{{ __('Name') }}</flux:label>
                <flux:input wire:model="editName" autocomplete="off" />
                <flux:error name="editName" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Description') }}</flux:label>
                <flux:textarea wire:model="editDescription" rows="2" />
            </flux:field>

            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="closeEditModal">{{ __('Cancel') }}</flux:button>
                <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Delete modal --}}
    <flux:modal wire:model.self="showDeleteModal" class="max-w-md">
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Delete scope?') }}</flux:heading>
            <flux:text>{{ __('This scope will be permanently deleted. Licenses belonging to this scope will not be automatically removed.') }}</flux:text>
            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="cancelDelete">{{ __('Cancel') }}</flux:button>
                <flux:button type="button" variant="danger" wire:click="deleteScope">{{ __('Delete') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
