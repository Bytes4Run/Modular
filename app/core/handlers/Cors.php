<?php

/**
 * Cors 
 * @description Clase que maneja las peticiones del usuario
 * @category Handler
 * @package app\core\handlers\Cors
 * @version 1.0.0 
 * @date 2024-01-10 
 * @time 16:00:00
 */

declare(strict_types=1);

namespace B4R\Kernel\handlers;

class Cors {
    private $allowedOrigins;

    public function __construct($configFile) {
        $this->allowedOrigins = $this->loadAllowedOrigins($configFile);
    }

    public function handleCors() {
        $origin = $_SERVER['HTTP_ORIGIN'];

        if ($this->isOriginAllowed($origin)) {
            header("Access-Control-Allow-Origin: $origin");
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
            header("Access-Control-Allow-Headers: Content-Type");
        } else {
            header("HTTP/1.1 403 Forbidden");
            exit;
        }
    }

    private function loadAllowedOrigins($configFile) {
        // Load the configuration file and extract the allowed origins
        // You can use any format for the configuration file (e.g., JSON, XML, INI)
        // Here's an example using a JSON file:
        $config = json_decode(file_get_contents($configFile), true);
        return $config['allowed_origins'];
    }

    private function isOriginAllowed($origin) {
        // Check if the origin is in the list of allowed origins
        return in_array($origin, $this->allowedOrigins);
    }
}