# agenticmorf/fluxui-licensing

FluxUI Pro (Livewire) settings screens for managing software licenses with [masterix21/laravel-licensing](https://github.com/masterix21/laravel-licensing).

Inspired by the [Laravel Licensing article on Laravel News](https://laravel-news.com/laravel-licensing) and the [Filament UI manager](https://github.com/masterix21/laravel-licensing-filament-manager) — this package brings the same feature set to a Livewire + FluxUI Pro stack.

Part of the [AgenticMorf FluxUI suite](https://github.com/AgenticMorf):
- [fluxui-sanctum](https://github.com/AgenticMorf/fluxui-sanctum) — Sanctum personal access tokens
- [fluxui-devices](https://github.com/AgenticMorf/fluxui-devices) — Device/session management
- [fluxui-teams](https://github.com/AgenticMorf/fluxui-teams) — Team management
- [fluxui-theme](https://github.com/AgenticMorf/fluxui-theme) — Theme & appearance settings

## Features

- **License list** — searchable, filterable table with status badges, usage counts, and expiry dates
- **Status management** — activate (Pending → Active) and suspend (Active → Suspended) with confirmation modals
- **Key management** — reveal or regenerate license keys securely; keys are shown once and copyable
- **License creation** — create licenses with scope, template, max usages, and optional expiry
- **Scope management** — full CRUD for license scopes with license count
- **Dashboard** — at-a-glance stats (total, active, pending, expiring soon, inactive), recent activations panel, and expiring-within-30-days panel

## Requirements

- Laravel 11+
- Livewire 3+ or 4+
- [Livewire Flux](https://fluxui.dev) 2+
- [masterix21/laravel-licensing](https://github.com/masterix21/laravel-licensing) installed and migrated
- Blade components `x-layouts.app` and `x-settings.layout` (as in the Laravel Livewire starter kit)

## Installation

```bash
composer require agenticmorf/fluxui-licensing
```

Optionally publish the config:

```bash
php artisan vendor:publish --tag=fluxui-licensing-config
```

Optionally publish views for customisation:

```bash
php artisan vendor:publish --tag=fluxui-licensing-views
```

## Settings navigation

Add a link to your settings sidebar (route name defaults to `licensing.index`):

```blade
<flux:navlist.item :href="route(config('fluxui-licensing.route_name', 'licensing.index'))" wire:navigate>
    {{ __('Licenses') }}
</flux:navlist.item>
```

## Configuration

See `config/fluxui-licensing.php`:

| Key | Default | Description |
|-----|---------|-------------|
| `route` | `settings/licenses` | URL path for the licenses page |
| `route_name` | `licensing.index` | Named route for the licenses page |
| `middleware` | `['web', 'auth', 'verified']` | Middleware applied to all licensing routes |
| `per_page` | `15` | Licenses shown per page in the list view |

## Components

All three Livewire components can be embedded individually:

```blade
{{-- Stats dashboard + license table --}}
<livewire:fluxui-licensing.license-dashboard />
<livewire:fluxui-licensing.license-manager />

{{-- Scope CRUD --}}
<livewire:fluxui-licensing.license-scope-manager />
```

## License

MIT
