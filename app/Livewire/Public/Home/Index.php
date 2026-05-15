<?php

namespace App\Livewire\Public\Home;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    public function render(): Factory|View
    {
        return view('public.home._index', [
            'stack' => $this->getStackVersions(),
        ]);
    }

    /**
     * Obtiene las versiones instaladas de las paqueterías del stack.
     *
     * @return list<array{name: string, version: string, icon: string}>
     */
    private function getStackVersions(): array
    {
        $packages = [
            ['name' => 'PHP',                 'package' => null,                          'icon' => 'M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 0'],
            ['name' => 'Laravel',              'package' => 'laravel/framework',           'icon' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z'],
            ['name' => 'Livewire',             'package' => 'livewire/livewire',           'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
            ['name' => 'TallStackUI',          'package' => 'tallstackui/tallstackui',     'icon' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z'],
            ['name' => 'Tailwind CSS',         'package' => null,                          'icon' => 'M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 0', 'static' => 'v4'],
            ['name' => 'Alpine.js',            'package' => null,                          'icon' => 'M12 2L2 19h20L12 2z',                                                         'static' => 'v3'],
            ['name' => 'Spatie Permission',    'package' => 'spatie/laravel-permission',   'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
            ['name' => 'Media Library',        'package' => 'spatie/laravel-medialibrary', 'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['name' => 'Laravel Auditing',     'package' => 'owen-it/laravel-auditing',    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
            ['name' => 'Fortify',              'package' => 'laravel/fortify',             'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
        ];

        $installed = $this->getInstalledPackages();

        return collect($packages)->map(function (array $item) use ($installed): array {
            if ($item['package'] === null && isset($item['static'])) {
                $version = $item['static'];
            } elseif ($item['package'] === null) {
                $version = 'v'.PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;
            } else {
                $pretty = $installed[$item['package']]['pretty_version'] ?? null;
                $version = $pretty ? 'v'.ltrim($pretty, 'v') : '—';
            }

            return [
                'name' => $item['name'],
                'version' => $version,
                'icon' => $item['icon'],
            ];
        })->toArray();
    }

    /**
     * Lee los paquetes instalados desde composer.
     */
    private function getInstalledPackages(): array
    {
        $path = base_path('vendor/composer/installed.php');

        if (! file_exists($path)) {
            return [];
        }

        return (require $path)['versions'] ?? [];
    }
}
