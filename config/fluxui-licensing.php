<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Settings route
    |--------------------------------------------------------------------------
    |
    | Path and route name for the licenses settings page. Add a matching
    | flux:navlist.item in your settings layout using route($route_name).
    |
    */

    'route' => 'settings/licenses',

    'route_name' => 'licensing.index',

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Applied to all licensing routes.
    |
    | @var list<string>
    */

    'middleware' => ['web', 'auth', 'verified'],

    /*
    |--------------------------------------------------------------------------
    | Per-page
    |--------------------------------------------------------------------------
    |
    | Number of licenses shown per page in the list view.
    |
    */

    'per_page' => 15,

    /*
    |--------------------------------------------------------------------------
    | Authorization gate
    |--------------------------------------------------------------------------
    |
    | When set to a non-null string, every mutating action in the LicenseManager
    | component (create, activate, suspend, show/regenerate key) will check this
    | Gate ability before proceeding. Define the gate in your AppServiceProvider:
    |
    |   Gate::define('manage-licenses', fn (User $user) => $user->isAdmin());
    |
    | When null (default), only the route middleware (auth, verified) is applied.
    |
    */

    'gate' => null,

];
