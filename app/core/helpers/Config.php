<?php

    /**
     * Ayudante para obtener y establecer variables de configuración
     * @description Helper to get and set configuration variables
     * @category Helper
     * @author JEcheverria <jecheverria@bytes4run.com>
     * @package app\core\helpers\Config
     * @version 1.3.0 rev. 1
     * 10-01-2023/03-05-2023
     */
    declare(strict_types=1);

    namespace B4R\Kernel\helpers;

    use Dotenv\Dotenv;
    use Exception;

    class Config
    {
        public function __construct(string $configFile='default', $type = 'env')
        {
            $this->getConfigVars($configFile, $type);
        }
        /**
         * Función para obtener las variables de configuración
         * @param string $value Nombre del archivo de configuración
         * @param string $type Tipo de archivo de configuración
         * @return array
         */
        public function get(string $value = "default",string $type = 'env'): array
        {
            return $this->getConfigVars($value, $type);
        }
        /**
         * Función para establecer las variables de configuración
         * @param string $name Nombre del archivo de configuración
         * @param array $data Datos a establecer en el archivo de configuración
         * @return bool
         */
        public function set(string $name, array $data) : bool
        {
            return $this->setConfigVars($name, $data);
        }
        /**
         * Función para obtener las variables de configuración
         * @param string $file Nombre del archivo de configuración
         * @param string $type Tipo de archivo de configuración
         * @return array
         */
        private function getConfigVars(string $file, string $type): array
        {
            $conf = [];
            try {
                if ($type == "json") {
                    $path = _CONF_ . $file . ".json";
                    $conf = json_decode(file_get_contents($path), true);
                } else {
                    if (!empty($file) && $file !== "default") {
                        $dotenv = Dotenv::createImmutable(_CONF_, $file . ".env");
                    } else {
                        $dotenv = Dotenv::createImmutable(_CONF_);
                    }
                    $dotenv->safeLoad();
                }
            } catch (Exception $ex) {
                return ['type' => "error", 'data' => $ex];
            }
            return $conf;
        }
        /**
         * Función para establecer las variables de configuración
         * @param string $fileName Nombre del archivo de configuración
         * @param array $fileData Datos a establecer en el archivo de configuración
         * @return bool
         */
        private function setConfigVars (string $fileName, array $fileData) : bool
        {
            $result = false;
            if (file_exists(_CONF_ . $fileName . ".json")) {
                $conf = json_decode(file_get_contents(_CONF_ . $fileName . ".json"), true);
                $conf = array_merge($conf, $fileData);
                $result = file_put_contents(_CONF_ . $fileName . ".json", json_encode($conf));
            } else {
                $result = file_put_contents(_CONF_ . $fileName . ".json", json_encode($fileData));
            }
            return (bool)$result;
        }
    }