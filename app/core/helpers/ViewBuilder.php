<?php
/**
 * ViewBuilder
 * @description This class is used to build the views of the application
 * @category Helper
 * @author Jorge Echeverria <jecheverria@bytes4run.com>
 * @package B4R\core\helpers\ViewBuilder
 * @version 1.0.0
 * @date 2024-03-06
 * @time 11:00:00
 * @copyright (c) 2024 Bytes4Run
 */

declare (strict_types = 1);

namespace B4R\Kernel\helpers;

use B4R\Kernel\helpers\Config;
use B4R\Kernel\libraries\ViewEngine;

class ViewBuilder
{
    /**
     * @var array
     */
    private array $vars;

    /**
     * @var string
     */
    private string $view;

    /**
     * @var string
     */
    private string $path;

    /**
     * @var string
     */
    private string $theme;

    /**
     * @var ViewEngine
     */
    private ViewEngine $engine;
    /**
     * @var string
     */
    private string $token;

    /**
     * Constructor
     */
    public function __construct()
    {
        $conf = new Config();
        $this->vars = $conf->get('config', 'json');
        $this->theme = $this->vars['APP_VIEW']['engine'] . "/" . $this->vars['APP_VIEW']['theme'];
        if ($this->vars['APP_VIEW']['engine'] !== 'json') {
            $this->engine = new ViewEngine($this->vars['APP_VIEW']['engine']);
        } else {
            $this->engine = 'json';
        }
    }

    /**
     * Function to render the view
     * @param string $view
     * @param array $data
     * @return void
     */
    public function render(string | array $view, array $data = []): void
    {
        $this->token = $_SESSION['token'] ?? '';
        if ($this->engine !== "json") {
            if ($this->find($view)) {
                $this->engine->assign('data', $this->createData($data));
                $this->engine->assign('token', $this->token);
                $this->engine->assign('theme', $this->theme);
                $this->engine->render($this->path);
            } else {
                $this->buildDefaultView($view,$data,'not_found');
            }
        } else {
            header('Content-Type: application/json'); //Especificamos el tipo de contenido a devolver
            $code = (isset($data['code'])) ? $data['code'] : $view['data']['code'];
            http_response_code(intval($code));
            echo json_encode($data, JSON_THROW_ON_ERROR); //Devolvemos el contenido
        }
    }

    /**
     * Function to find the view
     * @param string|array $view
     * @return bool
     */
    private function find(string | array $view): bool
    {
        if (is_array($view)) {
            if ($view['type'] !== "json") {
                $this->path = $this->getViewPath($view);
                return file_exists($this->path);
            }
            $view = ['type' => "template", 'name' => $view];
        }
        return false;
    }

    /**
     * Function to create structured data for view
     * @param array $data
     * @return array
     */
    private function createData(array $data): array
    {
        if (!empty($data['view'])) {
            $title = str_replace("/", " | ", $data['view']);
        }
        if (!empty($data['user'])) {
            $userData = $data['user'];
        }
        $this->vars['technology'][0]['name'] = "PHP " . phpversion();
        $this->vars['technology'][0]['icon'] = "fab fa-php";
        return [
            'content' => $data,
            'layout' => [
                'head' => [
                    'template' => "_shared/templates/_head.tpl",
                    'page_title' => $title ?? "",
                    'meta' => $this->getMeta(),
                    'css' => '',
                ],
                'body' => ['layout' => 'hold-transition sidebar-mini layout-fixed layout-footer-fixed', 'darkmode' => false],
                'footer' => [
                    'template' => "_shared/templates/_footer.tpl",
                    'data' => [],
                ],
                'navbar' => [
                    'template' => "_shared/templates/_navbar.tpl",
                    'data' => [
                        'app_logo' => ($userData['mode'] == "dark") ? $this->vars['darkLogo'] : $this->vars['app_logo'],
                        'user' => $userData
                    ],
                ],
                'scripts' => '',
                'app' => [
                    'data' => $this->vars,
                ],
            ],
        ];
    }

    /**
     * Function to get the view path
     * @param array $view
     * @return string
     */
    private function getViewPath(array $view): string
    {
        $path = _VIEW_ . $this->theme . "/";
        $path .= match ($view['type']) {
            "template" => function ($view) {
                $name = explode('/', $view['name']);
                if (count($name) > 2) {
                    $app = $name[0];
                    $module = $name[1];
                    $viewName = $name[2];
                    return $app . "/" . $module . "/templates/" . $viewName . ".tpl";
                } elseif (count($name) == 2) {
                    $module = $name[0];
                    $viewName = $name[1];
                    return $module . "/templates/" . $viewName . ".tpl";
                } else {
                    return "default/templates/" . $name[0] . ".tpl";
                }
            },
            "layout" => function ($view) {
                $name = explode('/', $view['name']);
                if (count($name) > 2) {
                    $app = $name[0];
                    $module = $name[1];
                    $viewName = $name[2];
                    return $app . "/" . $module . "/layouts/" . $viewName . ".tpl";
                } elseif (count($name) == 2) {
                    $module = $name[0];
                    $viewName = $name[1];
                    return $module . "/layouts/" . $viewName . ".tpl";
                } else {
                    return "default/layouts/" . $name[0] . ".tpl";
                }

            },
            default => $view['name'] . ".tpl",
        };
        return $path;
    }

    /**
     * Función que devuelve la lista de metadata para la vista
     * @return array
     */
    private function getMeta(): array
    {
        return [
            ['meta_name' => "msapplication-TileColor", 'meta_content' => $this->vars['app_title_color']],
            ['meta_name' => "msapplication-TileImage", 'meta_content' => "assets/img/app_icons/ms-icon-144x144.png"],
            ['meta_name' => "theme-color", 'meta_content' => $this->vars['app_theme_color']],
            ['meta_name' => "background_color", 'meta_content' => $this->vars['app_background_color']],
            ['meta_name' => "apple-mobile-web-app-capable", 'meta_content' => "yes"],
            ['meta_name' => "apple-mobile-web-app-status-bar-style", 'meta_content' => "black"],
            ['meta_name' => "apple-mobile-web-app-title", 'meta_content' => $this->vars['app_name']],
            ['meta_name' => "application-name", 'meta_content' => $this->vars['app_name']],
            ['meta_name' => "description", 'meta_content' => $this->vars['app_description']],
            ['meta_name' => "format-detection", 'meta_content' => "telephone=no"],
            ['meta_name' => "mobile-web-app-capable", 'meta_content' => "yes"],
            ['meta_name' => "msapplication-config", 'meta_content' => ""],
            ['meta_name' => "msapplication-tap-highlight", 'meta_content' => "no"],
            ['meta_name' => "viewport", 'meta_content' => "width=device-width, initial-scale=1, shrink-to-fit=no"],
        ];
    }

    /**
     * Function to build the default view
     * @param string|array $view
     * @param array $data
     * @param string $type
     * @return void
     */
    private function buildDefaultView(string | array $view, array $data, string $type): void {
        $this->path = _VIEW_ . $this->theme . "/default/" . $type . ".tpl";
        $viewData = [
            'content' => $data,
            'view' => $view,
            'layout' => [
                'head' => [
                    'template' => "_shared/templates/_head.tpl",
                    'css' => '',
                    'page_title' => $type,
                    'meta' => $this->getMeta(),
                ],
                'body' => ['layout' => 'hold-transition sidebar-mini layout-fixed', 'darkmode' => false],
                'footer' => [
                    'template' => "_shared/templates/_footer.tpl",
                    'data' => [],
                ],
                'scripts' => '',
                'app' => [
                    'data' => $this->vars,
                ],
            ],
        ];
        $this->engine->assign('data', $viewData);
        $this->engine->assign('token', $this->token);
        $this->engine->assign('theme', $this->theme);
        $this->engine->render($this->path);
    }
}
