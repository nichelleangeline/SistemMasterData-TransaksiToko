<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Request;

class MenuHelper
{
    public static function getMenuGroups()
    {
        return [
            [
                'title' => 'Menu',
                'items' => [
                    [
                        'icon' => 'dashboard',
                        'name' => 'Dashboard',
                        'path' => '/',
                    ],
                    [
                        'icon' => 'tables',
                        'name' => 'Master Data',
                        'subItems' => [
                            ['name' => 'Tables', 'path' => '/basic-tables'],
                        ],
                    ],
                ],
            ],
        ];
    }

    public static function isActive($path)
    {
        return Request::is(ltrim($path, '/'));
    }

    // Cek apakah ada anak yang aktif
    public static function isChildActive($item)
    {
        if (!isset($item['subItems'])) return false;
        foreach ($item['subItems'] as $sub) {
            if (self::isActive($sub['path'])) return true;
        }
        return false;
    }

    public static function getIconSvg($iconName)
    {
        $icons = [
            'dashboard' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>',
            'tables'    => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>',
        ];
        return $icons[$iconName] ?? '';
    }
}