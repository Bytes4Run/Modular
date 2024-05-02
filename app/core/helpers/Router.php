<?php

/**
 * Ayudante para resolver la ruta proporcionada
 * @description Helper to resolve the provided route
 * @category Helper
 * @author JEcheverria <jecheverria@piensads.com>
 * @package Kernel\helpers\Router
 * @version 1.0.0 rev. 1
 * @Time: 2021-04-27 19:00:00
 */

declare(strict_types=1);

namespace Kernel\helpers;

use Kernel\handlers\Request;
use Kernel\helpers\Config;

class Router
{
    /**
     * Url de la ruta
     * @var string
     */
    private string $url;
    /**
     * Request de la ruta
     * @var Request
     */
    private Request $request;
    /**
     * Método de la petición
     * @var string
     */
    protected string $http_method = "GET";
    /**
     * Callback de la ruta
     * @var array
     */
    protected array $callback = array();
    /**
     * Parámetros de la ruta
     * @var array
     */
    protected array $params = array();
    /**
     * Función constructora
     */
    public function __construct()
    {
        $this->request = new Request();
        $this->resolve();
    }

    /**
     * Función para obtener la ruta
     * @return array
     */
    public function getPath(): array
    {
        $this->url = $this->request::getRequestUrl();
        $this->url = ($this->url == "/" || $this->url == "/index.php" || $this->url == "/index.html") ? "" : substr($this->url, 1);
        if (preg_match('/\?/', $this->url)) {
            return preg_split('/\?/', $this->url, -1, PREG_SPLIT_NO_EMPTY);
        } else {
            return empty($this->url) ? array() : explode("/", $this->url);
        }
    }

    /**
     * Función para resolver la ruta
     * @return void
     */
    public function resolve(): void
    {
        $path = $this->getPath();
        $method = $this->getMethod();
        $this->http_method = $method;
        if (!empty($path[0])) {
            $this->callback = $path;
            if (preg_match('/\//',$path[0])) {
                $this->callback = explode('/', $path[0]);
            }
            if (empty($this->callback[0])) {
                array_shift($this->callback);
            }
        }
        if ($method == "option") {
            $this->corsResolver();
        } else {
            if (empty($this->callback)) {
                $this->callback = $this->getDefaults();
            }
            if ($method === "get") {
                if (in_array($this->callback[0], ["assets","css","js","img"])) {
                    $this->resolveStaticContent($path);
                }
                $this->params = isset($path[1]) ? $this->createParams($path[1]) : [];
                # Resolve params on slashed URL
                ## /module
                ## /module/controller
                ## /module/controller/method
                ## /module/controller/method/param1..
                $count = count($this->callback);
                if ($count > 3) {
                    for ($x = 2; $x < $count; $x++) {
                        $this->params[] = $this->callback[$x + 1];
                        unset($this->callback[$x + 1]);
                    }
                } elseif ($count < 3) {
                    $this->callback = $this->getDefaults($this->callback,$method);
                } else {
                    $this->callback = $this->getCallback();
                }
            } elseif (in_array($method, ["post","put","patch"])) {
                $this->params = (!empty($_POST)) ? $_POST : [];
                if (empty($this->params)) {
                    $result = json_decode(file_get_contents('php://input'),true);
                    $this->params = ($result !== false || $result !== null) ? $result : [];
                }
            } else {
                $this->callback = $this->getCallback();
            }
        }
    }

    /**
     * Función para obtener los parámetros de la ruta
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Función para obtener el método de la petición
     * @return string
     */
    public function getMethod(): string
    {
      return strtolower($this->request::getRequestMethod());
    }

    /**
     * Función para obtener la url de la ruta
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Función para obtener el callback de la ruta
     * @return array
     */
    public function getCallback(): array
    {
        return $this->callback;
    }

    /**
     * Función para obtener el request de la ruta
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Función que crea el arreglo de parametros
     * @param string $uriParams Uri de la petición
     * @return array
     */
    protected function createParams(string $uriParams): array
    {
        if (empty($uriParams)) {
            return array();
        }

        $uriArray = explode("&", $uriParams);
        $uriParameters = array();
        foreach ($uriArray as $param) {
            $parameter = explode("=", $param);
            if ($parameter[0] != "_") {
                if (isset($parameter[1])) {
                    $uriParameters[$parameter[0]] = $parameter[1];
                }
            }
        }
        return (!empty($uriParameters)) ? $uriParameters : array();
    }

    /**
     * Función para extraer los datos por defecto
     * @throws \Exception
     * @return array
     */
    private function getDefaults(array $callback = [], string $method = 'get'): array
    {
        $module = "home";
        $controller = $module;
        $method = match ($method) {
            "get" => "read",
            "put" => "update",
            "post" => "create",
            "patch" => "edit",
            "delete" => "delete",
            default => "index"
        };
        if (empty($callback)) {
            if (empty($_ENV)) {
                $configs = new Config;
                $configs->get();
            }
            if (!empty($_ENV)) {
                $module = $_ENV['APP_DEFAULT_MODULE'];
                $controller = $_ENV['APP_DEFAULT_CONTROLLER'];
                $method = $_ENV['APP_DEFAULT_METHOD'];
            }
            return array($module, $controller, $method);
        } else {
            $count = count($callback);
            if ($count == 1) {
                return array($callback[0], $callback[0], $method);
            } elseif ($count == 2) {
                return ($callback[1] == $method) ? array($callback[0], $callback[0], $method) : array($callback[0], $callback[0], $callback[1]);
            } else {
                return ($callback[2] == $method) ? array($callback[0], $callback[1], $method) : array($callback[0], $callback[1], $callback[2]);
            }
        }

    }

    /**
     * Función para obtener el MIME del archivo solicitado
     * @return string
     */
    public function getMIME(string $asset): string {
        $nameSplited = explode('.', $asset);
        $extension = end($nameSplited);
        $mime = match ($extension) {
            "js"    => "text/javascript",
            "css"   => "text/css",
            "png"   => "image/png",
            "jpg"   => "image/jpeg",
            "gif"   => "image/gif",
            "svg"   => "image/svg+xml",
            "ico"   => "image/x-icon",
            "jpeg"  => "image/jpeg",
            default => "text/plain"
        };
        return $mime;
    }

    /**
     * Función para resolver la petición OPTIONS
     */
    private function corsResolver(): void {
        // filter origins from file
        $origin = $this->request::getHttpOrigin();
        header("Access-Control-Allow-Origin: {$origin}");
        header('Access-Control-Allow-Credentials: true');
        header("Access-Control-Allow-Methods: GET, DELETE, HEAD, OPTIONS, PATCH, POST, PUT");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        header("Access-Control-Max-Age: 6400");
        header("Content-Length: 0");
        header("Content-Type: text/plain");
        http_response_code(204);
        exit;
    }

    /**
     * Function to resolve any static content
     */
    private function resolveStaticContent (array $path):void {
        $fileURL = _ASSETS_ . $path[0];
        $mime = $this->getMIME(end($path));
        header("Content-Type: " . $mime);
        include $fileURL;
        exit;
    }
}