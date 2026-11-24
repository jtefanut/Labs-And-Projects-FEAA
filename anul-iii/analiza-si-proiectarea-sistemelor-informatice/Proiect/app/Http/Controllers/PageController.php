<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('welcome', [
            'plans' => Product::query()->where('category', 'hosting')->where('is_active', true)->orderBy('sort_order')->get(),
            'services' => [
                ['title' => __('messages.home.web_title'), 'summary' => __('messages.home.web_text'), 'route' => route('hosting'), 'glyph' => 'W'],
                ['title' => __('messages.home.wordpress_title'), 'summary' => __('messages.home.wordpress_text'), 'route' => route('wordpress'), 'glyph' => 'WP'],
                ['title' => __('messages.home.vps_title'), 'summary' => __('messages.home.vps_text'), 'route' => route('vps'), 'glyph' => 'V'],
                ['title' => __('messages.home.cloud_title'), 'summary' => __('messages.home.cloud_text'), 'route' => route('cloud'), 'glyph' => 'C'],
                ['title' => __('messages.home.domain_title'), 'summary' => __('messages.home.domain_text'), 'route' => route('domains'), 'glyph' => '.ro'],
                ['title' => __('messages.home.email_title'), 'summary' => __('messages.home.email_text'), 'route' => route('email'), 'glyph' => '@'],
                ['title' => __('messages.home.backup_title'), 'summary' => __('messages.home.backup_text'), 'route' => route('backup'), 'glyph' => 'B'],
                ['title' => __('messages.home.ssl_title'), 'summary' => __('messages.home.ssl_text'), 'route' => route('ssl'), 'glyph' => 'S'],
                ...collect(['apps', 'dedicated', 'gpu', 'managed', 'storage', 'database', 'security', 'devops'])->map(fn (string $key) => [
                    'title' => __('messages.catalog.categories.'.$key),
                    'summary' => __('messages.catalog.intro'),
                    'route' => route(app()->getLocale() === 'ro' ? 'catalog.category' : 'catalog.category.en', $key),
                    'glyph' => strtoupper(substr($key, 0, 2)),
                ])->all(),
            ],
        ]);
    }

    public function catalog(string $category): View
    {
        abort_unless(in_array($category, ['hosting', 'wordpress', 'vps', 'cloud', 'domain', 'email', 'backup', 'ssl', 'apps', 'dedicated', 'gpu', 'managed', 'storage', 'database', 'security', 'devops'], true), 404);
        $titleKey = 'messages.catalog.categories.'.$category;
        $title = __($titleKey);

        return view('public.catalog', [
            'category' => $category,
            'title' => $title,
            'products' => Product::query()->where('category', $category)->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function about(): View
    {
        return view('public.about');
    }

    public function legal(string $page): View
    {
        abort_unless(in_array($page, ['terms', 'privacy', 'cookies', 'withdrawal', 'complaints'], true), 404);

        return view('public.legal', ['page' => $page, 'document' => __('legal.pages.'.$page)]);
    }
}
