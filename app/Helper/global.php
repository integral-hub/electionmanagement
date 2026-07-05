<?php

if (!function_exists('global_data')) {
    function global_data(?string $key = null)
    {
        if (!app()->bound('global')) {
            return $key ? null : [];
        }

        $global = app('global');

        return $key === null
            ? $global
            : ($global[$key] ?? null);
    }
}

if (!function_exists('voter')) {
    function voter(): ?\App\Models\Voter
    {
        return app()->bound('voter')
            ? app('voter')
            : null;
    }
}