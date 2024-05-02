<?php

/**
 * Session
 * @description Handler for manage user sessions
 * @author Jorge Echeverria <jorge.echeverria@ferranfg.com>
 * @category Handler
 * @package Kernel\handlers\Session
 * @version 1.0.0
 * @date 2022-03-26
 * @time 13:40:35
 */

namespace Kernel\handlers;

class Session {
    /**
     * @var int $expiration Session expiration time in seconds
     */
    private static $expiration = 86400;
    private string $coockie;
    protected int $sessionid;
    protected int $sessionTime;
    protected int $sessionTimeout;
    protected string $sessionName;
    /**
     * Sesión de usuario
     * @var 
     */
    private bool $sessionUser;
    /**
     * Estado de la session
     * @var bool
     */
    //Public Properties
    public bool $status;
    /**
     * Token de la sesión
     * @var string
     */
    public string $token;
    /**
     * Tiempo de la sesión
     * @var float
     */
    public float $time;
    /**
     * Opciones de la sesión
     * @var array
     */
    public array $options;
    /**
     * Permisos de la sesión
     * @var array
     */
    public array $permissions;
    /**
     * Start a session
     * @return void
     */
    public static function start(?array $values = null):Session
    {
        if (!is_null($values)) {
            foreach ($values as $key => $value) {
                self::$$key = $value;
            }
        }
        session_name(self::$sessionName);
        session_start([
            'time'             => self::$sessionTime,
            'token'            => self::$token,
            'cookie'           => self::$coockie,
            'cookie_secure'    => true,
            'cookie_lifetime'  => self::$expiration,
            'cookie_httponly'  => true,
            'use_only_cookies' => 1,
        ]);
        return new Session();
    }

    /**
     * Destroy a session
     * @return boolean
     */
    public static function destroy()
    {
        return session_destroy();
    }

    /**
     * Get session value
     * @param string $key Session key
     * @return mixed
     */
    public static function get(?string $key = null)
    {
        if (!is_null($key)){
            if (isset($_SESSION[$key])) {
                return $_SESSION[$key];
            }
        } else {
            return $_SESSION;
        }
    }

    /**
     * Set session value
     * @param string $key Session key
     * @param mixed $value Session value
     * @return boolean
     */
    public static function set(string $key, $value)
    {
        $_SESSION[$key] = $value;

        return true;
    }

    /**
     * Remove session value
     * @param string $key Session key
     * @return boolean
     */
    public static function remove(string $key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);

            return true;
        }

        return false;
    }

    /**
     * Check if a session is set
     * @param string $key Session key
     * @return boolean
     */
    public static function has(string $key)
    {
        return isset($_SESSION[$key]);
    }

    public static function status () {
        return session_status();
    }

    /**
     * Función que verifica si la sesión está activa
     * @return bool
     */
    public function isSessionStarted(): bool
    {
        $response = false;
        if ($this->status === false)
            @session_start();
        if (!empty($_SESSION)) {
            if (isset($_SESSION['time']) && !empty($_SESSION['time'])) {
                if ($this->isTimeOut($_SESSION['time'])) {
                    $this->destroy();
                } else {
                    $response = true;
                }
            } else {
                $this->destroy();
            }
        }
        return $response;
    }
    public function validSession(array $storagedSession = []): bool
    {
        $response = false;
        if (!empty($storagedSession)) {
            if (isset($storagedSession['token']) && !empty($storagedSession['token'])) {
                if (!$this->isTimeOut($storagedSession['time'])) {
                    $response = true;
                } else {
                    if ($storagedSession['options']['keepAlive']) {
                        $storagedSession['time'] = $this->setTimeOut($storagedSession['time'], false);
                        $response = true;
                    } else {
                        $this->destroy();
                    }
                }
            } else {
                $this->destroy();
            }
        }
        return $response;
    }


    /**
     * Función establece el tiempo de vida de la sesión
     * @param float $time
     * @param bool $limit
     * @return float|int
     */
    protected function setTimeOut(float $time = 0, bool $limit = true): float
    {
        date_default_timezone_set("America/El_Salvador");
        $serverTime = time();
        $timeLimit = 18000;
        if ($limit) {
            if ($time == 0) {
                $response = $serverTime + $timeLimit;
            } else {
                $leftTime = abs($time - $serverTime);
                $response = $serverTime + ($leftTime - $timeLimit);
            }
        } else {
            $response = $this->keepAlive($time);
        }
        return $response;
    }
    /**
     * Función que verifica si el tiempo de la sesión ha expirado
     * Devuelve true si ha expirado, false si aún no ha expirado
     * @param float $time
     * @return bool
     */
    protected function isTimeOut(float $time): bool
    {
        date_default_timezone_set("America/El_Salvador");
        $serverTime = time();
        $leftTime = $time - $serverTime;
        return ($leftTime <= 0) ? true : false;
    }
    /**
     * Verifica sí la sesión aún no ha caducado
     * @param float $time
     * @return float|int
     */
    private function keepAlive(float $time = 0): float
    {
        date_default_timezone_set("America/El_Salvador");
        $serverTime = time();
        $timeLimit = (60 ^ 3) * 24;
        if ($time == 0) {
            $response = $serverTime + $timeLimit;
        } else {
            $leftTime = abs($time - $serverTime);
            $response = $serverTime + ($leftTime - $timeLimit);
        }
        return $response;
    }
}
