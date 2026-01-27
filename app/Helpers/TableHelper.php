<?php

if (!function_exists('sortLink')) {
    function sortLink(string $label, string $field): string
    {
        $currentSort = request('sort');
        $currentDir  = request('dir', 'asc');

        $dir = ($currentSort === $field && $currentDir === 'asc') ? 'desc' : 'asc';
        $icon = $currentSort === $field
            ? ($currentDir === 'asc' ? '↑' : '↓')
            : '';

        $url = request()->fullUrlWithQuery([
            'sort' => $field,
            'dir'  => $dir,
        ]);

        return '<a href="'.$url.'" class="flex items-center gap-1 hover:underline">'
                .$label.' '.$icon.
               '</a>';
    }
}
