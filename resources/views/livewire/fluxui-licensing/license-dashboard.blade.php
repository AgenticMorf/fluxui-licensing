<div class="space-y-8">
    {{-- Stats grid --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
        <div class="rounded-xl border border-zinc-200 p-4 dark:border-white/10">
            <flux:text variant="subtle" class="text-xs uppercase tracking-wider">{{ __('Total') }}</flux:text>
            <p class="mt-1 text-2xl font-semibold text-zinc-900 dark:text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 p-4 dark:border-white/10">
            <flux:text variant="subtle" class="text-xs uppercase tracking-wider">{{ __('Active') }}</flux:text>
            <p class="mt-1 text-2xl font-semibold text-green-600 dark:text-green-400">{{ $stats['active'] }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 p-4 dark:border-white/10">
            <flux:text variant="subtle" class="text-xs uppercase tracking-wider">{{ __('Pending') }}</flux:text>
            <p class="mt-1 text-2xl font-semibold text-yellow-600 dark:text-yellow-400">{{ $stats['pending'] }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 p-4 dark:border-white/10">
            <flux:text variant="subtle" class="text-xs uppercase tracking-wider">{{ __('Expiring (30d)') }}</flux:text>
            <p class="mt-1 text-2xl font-semibold {{ $stats['expiring_soon'] > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-zinc-900 dark:text-white' }}">{{ $stats['expiring_soon'] }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 p-4 dark:border-white/10">
            <flux:text variant="subtle" class="text-xs uppercase tracking-wider">{{ __('Inactive') }}</flux:text>
            <p class="mt-1 text-2xl font-semibold text-red-600 dark:text-red-400">{{ $stats['expired'] }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Recent activations --}}
        <div class="space-y-3">
            <flux:heading size="sm">{{ __('Recent activations') }}</flux:heading>
            @if ($recentLicenses->isEmpty())
                <flux:text variant="subtle">{{ __('No recent activations.') }}</flux:text>
            @else
                <div class="divide-y divide-zinc-100 overflow-hidden rounded-xl border border-zinc-200 dark:divide-white/5 dark:border-white/10">
                    @foreach ($recentLicenses as $license)
                        <div class="flex items-center justify-between p-3" wire:key="recent-{{ $license->id }}">
                            <div class="space-y-0.5">
                                <flux:text class="font-mono text-xs font-medium">{{ $license->uid }}</flux:text>
                                @if ($license->scope)
                                    <flux:text variant="subtle" class="text-xs">{{ $license->scope->name }}</flux:text>
                                @endif
                            </div>
                            @if ($license->activated_at)
                                <flux:text variant="subtle" class="shrink-0 text-xs">{{ $license->activated_at->diffForHumans() }}</flux:text>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Expiring soon --}}
        <div class="space-y-3">
            <flux:heading size="sm">{{ __('Expiring within 30 days') }}</flux:heading>
            @if ($expiringLicenses->isEmpty())
                <flux:text variant="subtle">{{ __('No licenses expiring soon.') }}</flux:text>
            @else
                <div class="divide-y divide-zinc-100 overflow-hidden rounded-xl border border-zinc-200 dark:divide-white/5 dark:border-white/10">
                    @foreach ($expiringLicenses as $license)
                        <div class="flex items-center justify-between p-3" wire:key="expiring-{{ $license->id }}">
                            <div class="space-y-0.5">
                                <flux:text class="font-mono text-xs font-medium">{{ $license->uid }}</flux:text>
                                @if ($license->scope)
                                    <flux:text variant="subtle" class="text-xs">{{ $license->scope->name }}</flux:text>
                                @endif
                            </div>
                            <flux:badge size="sm" color="orange">{{ $license->expires_at->format('d M Y') }}</flux:badge>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
