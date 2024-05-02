<?php
/**
 * Controller class
 * @description: This class is the base class for all controllers
 * @category Class
 * @author Jorge Echeverria <jecheverria@bytes4run.com>
 * @package Kernel\classes\Controller
 * @version 1.0.0
 * @date 2024-03-06
 * @time 10:00:00
 */

declare(strict_types=1);

namespace Kernel\classes;

class Controller {
    private ?array $error;
    
    public function __construct() {
        $this->error = null;
    }
    public function getError(): ?array {
        return $this->error;
    }
    public function setError($error): void {
        if ($error instanceof \Exception) {
            $this->error = ['code'=>$error->getCode(),'message' => $error->getMessage()];
        }
    }
    protected function getController (string $name) {
        $splitName = explode("/", $name);
        if (sizeof($splitName) > 1) {
            $moduleName = $splitName[0];
            $controllerName = $splitName[1];
        } else {
            $moduleName = $splitName[0];
            $controllerName = $splitName[0];
        }
        return $this->getComponent($moduleName, "controller", $controllerName);
    }
    protected function getModel (string $name) {
        $splitName = explode("/", $name);
        if (sizeof($splitName) > 1) {
            $moduleName = $splitName[0];
            $modelName = $splitName[1];
        } else {
            $moduleName = $splitName[0];
            $modelName = $splitName[0];
        }
        return $this->getComponent($moduleName, "model", $modelName);
    }
    private function getComponent(string $moduleName, string $type = "controller", string $componentName = null): object {
        $moduleName = ucfirst($moduleName);
        $componentName = (is_null($componentName)) 
            ? $moduleName 
            : ucfirst($componentName);
        $path = match ($type) {
            "model"   => "Modules/$moduleName/models/" . $componentName . "Model",
            default   => "Modules/$moduleName/controllers/" . $componentName . "Controller",
            "helper"  => "Modules/$moduleName/helpers/_" . $componentName . "Helper",
            "handler" => "Modules/$moduleName/handlers/__" . $componentName . "handler",
            "library" => "Modules/$moduleName/libraries/_" . $componentName . "_Library",
        };
        $path = str_replace("/", "\\", $path);
        try {
            return new $path;
        } catch (\Exception $e) {
            return $e;
        }
    }
    /**
     * Crea una respuesta de tipo vista, para el helper view
     * @param string $name
     * @param array $content
     * @param array $breadcrumbs
     * @param string $type
     * @param string|int $code
     * @param array $style
     * @return array
     */
    public function view(string $name, array $content = [], string $type = 'template', array $breadcrumbs = [], string | int $code = '', array $style = []): array
    {
        if (!empty($name)) {
            if (empty($breadcrumbs)) {
                $breadcrumbs = $this->createBreadcrumbs($name);
            }
        }
        return [
            'view' => [
                'type' => $type,
                'name' => $name,
                'data' => [
                    'code' => $code,
                    'style' => $style,
                ],
            ],
            'data' => [
                'breadcrumbs' => $breadcrumbs,
                'datos' => $content,
            ],
        ];
    }
    /**
     * Función que genera un arreglo de breadcrums.
     * @param string|array $values puede recibir una cade de caracteres con el nombre de la vista, ej.: "home/index"
     * o puede recibir un arreglo con los hijos de una vista, ej.:
     * ```php
     * $arreglo = [
     *  'view'=>"home/index",
     *  'children'=>[
     *    'main'=>"zapatos",
     *    'module'=>"accesorios",
     *    'method'=>"list",
     *    'params'=>null
     *   ]
     * ]
     * ```
     * @return array
     */
    protected function createBreadcrumbs(string | array $values): array
    {
        $routes = array();
        $mdl = 'home';
        $ctr = 'home';
        $mtd = 'index';
        $prm = null;
        if (is_string($values)) {
            $name = explode("/", $values);
            if (sizeof($name) > 2) {
                $mdl = $name[0];
                $ctr = $name[0];
                $mtd = $name[1];
                $prm = $name[2];
            } else {
                $mdl = $name[0];
                $ctr = $name[0];
                $mtd = "index";
            }
            array_push($routes, [
                'text' => $mdl,
                'param' => $prm,
                'method' => $mtd,
                'controller' => $ctr,
            ]);
        } else {
            if (isset($values['view'])) {
                $name = explode("/", $values['view']);
            }

            if (sizeof($name) > 1) {
                $mdl = $name[0];
                $ctr = $name[0];
            }
            foreach ($values['children'] as $child) {
                $mdl = ($child['main']) ?? $child['module'];
                $ctr = $child['module'];
                $mtd = $child['method'];
                if (isset($child['params'])) {
                    $prm = (is_array($child['params'])) 
                        ? implode("|", $child['params'])
                        : $child['params'];
                }
                array_push($routes, [
                    'text' => $mdl,
                    'param' => $prm,
                    'method' => $mtd,
                    'controller' => $ctr,
                ]);
            }
        }
        return [
            'main' => $mdl,
            'routes' => $routes,
        ];
    }
    /**
     * Function to generate a error message
     * @param string|int $type
     * @param mixed $content
     * @param string $display
     * @return array
     */
    public function error(string | int $type, mixed $content, string $display = "alert"): array {
        if (is_string($type)) {
            $type = match ($type) {
                "info" => 200,
                "error" => 500,
                "success" => 200,
                "warning" => 200,
                default => 200,
            };
        }
        if (is_array($content)) {
            $message = $content['message'];
            $code = $content['code'];
        } elseif (is_string($content)) {
            $message = $content;
            $code = $type;
        } elseif ($content instanceof \Exception) {
            $message = $content->getMessage();
            $code = $content->getCode();
        }
        if ($display == "view" || $display == "template") {
            return [
                'view' => [
                    'type' => $display,
                    'name' => $code,
                    'data' => [
                        'code' => $code,
                        'style' => [
                            'title' => "Error " . $code,
                            'color' => "danger",
                        ],
                    ],
                ],
                'data' => [
                    'message' => $message,
                ],
            ];
        } else {
            return [
                'view' => [
                    'type' => 'json',
                    'name' => "error",
                ],
                'data' => [
                    'message' => $message,
                    'code' => $code,
                    'type' => $type,
                ],
            ];
        }
    }
    /**
     * Generates a JSON response based on the provided data array.
     *
     * @param array $data The data array containing the response data.
     * @return array The JSON response array with the response type and encoded data.
     */
    protected function json (array $data) {
        $code = (isset($data['status'])) ? $data['status'] : $data['data']['code'];
        http_response_code(intval($code));
        return array('json',json_encode($data, JSON_PRETTY_PRINT));
    }
    /** 
     * Function to Redirect to another page given
     * @param string $url
     */
    protected function redirect(string $url) {
        http_response_code(301);
        return array('redirect',$url);
    }
}