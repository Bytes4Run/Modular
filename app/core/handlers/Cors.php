<?php

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

// Usage example:
$configFile = '/path/to/config.json'; // Replace with the actual path to your configuration file
$cors = new Cors($configFile);
$cors->handleCors();