# Installation

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

## Requirements

- Laravel 11 or 12
- Livewire 3 or 4
- Livewire Flux 2 (Pro or free)
- [masterix21/laravel-licensing](https://github.com/masterix21/laravel-licensing) installed and migrated

## Settings navigation

Add links to your settings sidebar:

```blade
<flux:navlist.item :href="route(config('fluxui-licensing.route_name', 'licensing.index'))" wire:navigate>
    {{ __('Licenses') }}
</flux:navlist.item>
```

## Configuration

See `config/fluxui-licensing.php`:

- `route` — URL under `/settings` (default: `settings/licenses`)
- `route_name` — Named route (default: `licensing.index`)
- `middleware` — Middleware applied to routes (default: `['web', 'auth', 'verified']`)
- `per_page` — Licenses per page (default: `15`)
