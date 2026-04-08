<x-layouts.app :title="__('License Scopes')">
    <section class="w-full">
        @includeIf('partials.settings-heading')

        <x-settings.layout
            :heading="__('License scopes')"
            :subheading="__('Group and categorize your licenses by product, plan, or feature set')"
        >
            <div class="mb-4 flex gap-2 border-b border-zinc-200 dark:border-white/10">
                <a
                    href="{{ route(config('fluxui-licensing.route_name', 'licensing.index')) }}"
                    class="border-b-2 px-1 pb-3 text-sm font-medium border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200"
                >{{ __('Licenses') }}</a>
                <a
                    href="{{ route('licensing.scopes') }}"
                    class="border-b-2 px-1 pb-3 text-sm font-medium border-zinc-900 text-zinc-900 dark:border-white dark:text-white"
                >{{ __('Scopes') }}</a>
            </div>

            <livewire:fluxui-licensing.license-scope-manager />
        </x-settings.layout>
    </section>
</x-layouts.app>
