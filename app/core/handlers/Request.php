<?php
/**
 * Request 
 * @description Clase que maneja las peticiones del usuario
 * @category Handler
 * @package Kernel\handlers\Request
 * @version 1.0.0 
 * @date 2024-01-10 
 * @time 16:00:00
 * @copyright 2024 Bytes4Run 
 */
    declare(strict_types=1);

    namespace Kernel\handlers;
    
class Request {
    public static function getRemoteAddr() {
        return $_SERVER['REMOTE_ADDR'];
    }

    public static function getRequestUrl() {
        return $_SERVER['REQUEST_URI'];
    }

    public static function getRequestMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function getHttpReferer() {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    }

    public static function getHttpOrigin() {
        return isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
    }

    public static function getHttpHost() {
        return $_SERVER['HTTP_HOST'];
    }
    public static function getHeaders () {
        return getallheaders();
    }
}