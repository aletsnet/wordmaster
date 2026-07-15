<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\Option;
use App\Models\Template;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;

class TemplateRenderer
{
    public function render(string $templateSlug, array $data = []): string
    {
        $template = Template::where('slug', $templateSlug)->first();

        if (!$template) {
            return "<!-- Template '$templateSlug' not found -->";
        }

        $templateVars = $this->templateSpecificVars($template);
        $data = array_merge($this->commonLayoutVars(), $templateVars, $data);

        $compiled = Blade::compileString($template->content);

        $obLevel = ob_get_level();
        ob_start();

        try {
            extract($data, EXTR_SKIP);
            eval('?>' . $compiled);
        } catch (\Exception $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }
            return "<!-- Error rendering template: " . $e->getMessage() . " -->";
        }

        return ob_get_clean();
    }

    /**
     * Variables específicas del template (assets_url, etc.)
     */
    private function templateSpecificVars(Template $template): array
    {
        $vars = [];

        if ($template->assets_path) {
            $vars['assets_url'] = rtrim(
                asset('storage/' . $template->assets_path),
                '/'
            );
        } else {
            $vars['assets_url'] = null;
        }

        return $vars;
    }

    /**
     * Variables comunes disponibles en todos los templates de la DB.
     * Se generan como HTML puro para evitar dependencias de $__env (Blade env).
     */
    public function commonLayoutVars(): array
    {
        $siteTitle    = Option::getValue('site_title', config('app.name'));
        $homeUrl      = url('/');
        $currentYear  = date('Y');
        $currentLang  = app()->getLocale();
        $csrfToken    = csrf_token();

        return [
            'site_title'           => $siteTitle,
            'home_url'             => $homeUrl,
            'current_year'         => $currentYear,
            'lang'                 => $currentLang,
            'csrf_token'           => $csrfToken,
            'header_menu_html'     => $this->buildMenuHtml('header'),
            'footer_menu_html'     => $this->buildMenuHtml('footer'),
            'locale_switcher_html' => $this->buildLocaleSwitcherHtml($currentLang, $csrfToken),
        ];
    }

    private function buildMenuHtml(string $location): string
    {
        $menu = Menu::where('location', $location)->with('items')->first();

        if (!$menu || $menu->items->isEmpty()) {
            return '';
        }

        $currentPath = request()->path();
        if ($currentPath === '/') {
            $currentPath = '/';
        } else {
            $currentPath = '/' . $currentPath;
        }

        $html = '<ul class="nav-menu" id="navMenu">';
        foreach ($menu->items as $item) {
            $url    = htmlspecialchars($item->url, ENT_QUOTES);
            $title  = htmlspecialchars($item->title, ENT_QUOTES);
            $active = ($url === $currentPath) ? ' active' : '';
            $html  .= '<li><a href="' . $url . '" class="' . $active . '">' . $title . '</a></li>';
        }
        $html .= '</ul>';

        return $html;
    }

    private function buildLocaleSwitcherHtml(string $current, string $csrfToken): string
    {
        $locales = ['es', 'en'];
        $html    = '<div class="flex items-center gap-1 text-sm font-medium">';

        foreach ($locales as $lang) {
            $label   = strtoupper($lang);
            $url     = url('/locale/' . $lang);

            if ($lang === $current) {
                $html .= '<span class="text-gray-900 bg-gray-200 px-2 py-1 rounded font-semibold">' . $label . '</span>';
            } else {
                $html .= '<form method="POST" action="' . $url . '" class="inline">'
                       . '<input type="hidden" name="_token" value="' . $csrfToken . '">'
                       . '<button type="submit" class="text-gray-500 hover:text-gray-900 hover:bg-gray-100 px-2 py-1 rounded transition">'
                       . $label
                       . '</button></form>';
            }
        }

        $html .= '</div>';

        return $html;
    }

    public static function getTemplateOptions(): array
    {
        return Template::pluck('name', 'slug')->toArray();
    }
}
