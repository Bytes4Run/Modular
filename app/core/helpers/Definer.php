<?php
    /**
     * Global variables Definer
     * @description Class to define the global constant variables of the application
     * @author Jorge Echeverria <jecheverria@bytes4run.com>
     * @category Helper
     * @package Kernel\helpers\Definer
     * @version 1.0.0
     */
    declare(strict_types=1);

    namespace B4R\core\helpers;
    class Definer {
        public function __construct()
        {
            $this->define();
        }
        private function define() :void
        {
            ##GLOBAL APP Core Variable
            if (!defined("_APP_")) define("_APP_", dirname(__FILE__,2));
            ##GLOBAL CLASS Core Variable
            if (!defined("_CLASS_")) define("_CLASS_", _APP_ . "/classes/");
            ##GLOBAL HELPER Core Variable
            if (!defined("_HELPER_")) define("_HELPER_", _APP_ . "/helpers/");
            ##GLOBAL MODULE Core Variable
            if (!defined("_MODULE_")) define("_MODULE_", dirname(_APP_) . "/modules/");
            ##GLOBAL VIEW Variable
            if (!defined("_VIEW_")) define("_VIEW_", dirname(_APP_,2) . "/resources/views/");
            ##GLOBAL CONFIGURATION Variable
            if (!defined("_CONF_")) define("_CONF_", dirname(_APP_,2) . "/configs/");
            if (!defined("_CACHE_")) define("_CACHE_", dirname(_APP_,2) . "/cache/");
            if (!defined("_ENT_")) define("_ENT_", _APP_ . "/entities/");
            if (!defined("_ASSETS_")) define("_ASSETS_", dirname(_APP_,2) . "/public/");
        }

    }