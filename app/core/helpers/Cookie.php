<?php
/** 
 * Cooki
 * @description Shopify handler for manage Shopify Cooki
 * @author Jorge Echeverria <jecheverria@bytes4run.com>
 * @category Handler
 * @package Kernel\handlers\Cook
 * @version 1.0.0
 * @date 2024-04-18
 * @time 17:14:02
 */
declare(strict_types=1);
namespace Kernel\helpers;
# Base
use Exception;
use Throwable;
# Classes
class Cookie {
    public static function cookACookie(array $values) {
        $name = (!empty($values['name'])) ? $values['name'] : $_ENV['APP_NAME'];
        $value = (!empty($values['value'])) ? $values['value'] : '';
        $expire = (!empty($values['expires'])) ? $values['expires'] : 3600;
        $path = "/";
        $domain = (!empty($values['domain'])) ? $values['domain'] : "localhost";
        $secure = (!empty($values['secure'])) ? $values['secure'] :true;
        $httponly = (!empty($values['httponly'])) ? $values['httponly'] :false;
        if(setcookie($name, $value, time() + $expire, $path, $domain, $secure, $httponly)) {
            return true;
        } else {
            return false;
        }
    }
}