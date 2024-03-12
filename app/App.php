<?php
    /**
     * @description Application loader
     * @category Loader
     * @author Jorge Echeverria <jecheverria@bytes4run.com>
     * @package B4R\App
     * @license Bytes4Run
     * @version 1.0.0
     * @link https://bytes4run.com
     * @copyright (c) 2021-2024 Bytes4Run
     */

    declare(strict_types=1);

    namespace B4R;

    # Helpers
    use B4R\Kernel\helpers\Definer;
    use B4R\Kernel\helpers\Router;
    //use Kernel\helpers\Messenger;
    //use Kernel\helpers\ViewBuilder;
    # Handlers
    //use Kernel\handlers\Authorization;
    class App {
        /**
         * @var Router
         * @description Routes container variable.
         */
        private Router $routes;

        /**
         * @var array
         * @description Array of callback function to run.
         */
        private array $callback;

        /**
         * @var array
         * @description Array of parameters
         */
        private array $params;

        /**
         * @var array|null
         * @description Array of response
         */
        private ?array $response;

        /**
         * @var array|null
         * @description Error
         */
        private ?array $error;

        /**
         * @var string
         * @description Method of the request
         */
        private string $method;

        /**
         * @var Messenger
         * @description Messenger Helper Object
         */
        //private Messenger $messenger;

        /**
         * @var ViewBuilder
         * @description View builder for any engine to be use
         */
        //private ViewBuilder $view;

        /**
         * @var Authorization
         * @description Object Auth to authorize any action of the user.
         */
        //private Authorization $auth

        /**
         * Class constructor
         */
        public function __construct()
        {
            new Definer;
            $this->routes = new Router;
            //$this->messenger = new Messenger;
            $this->error = null;
            $this->response = null;
            $this->params = [];
            $this->init();
        }

        /**
         * @description Method to set the routes
         * @return void
         */
        public function  init() :void {
            $this->params   = $this->routes->getParams();
            $this->method   = $this->routes->getMethod();
            $this->callback = $this->routes->getCallback();
            $this->run();
        }

        /**
         * @description Method to run the application
         * @return array $callback 
         * @return array $params
         * @return void
         */
        public function run(array $callback = [], array $params = []) :void {
            if (empty($callback)) {
                if (!empty($this->routes)) {
                    $params   = $this->params;
                    $callback = $this->callback;
                } else {
                    $this->error = ['status'=>404,'message'=>'No callback function found','data'=>[]];
                }
            } else {
                if (empty($params)) {
                    if (isset($this->params)) $params = $this->params;
                }
            }
            $count = count($callback);
            if ($count > 0) {
                if ($count === 1) {
                    $this->response = $this->getModuleResponse($callback[0],params:$params);
                } elseif ($count === 2) {
                    $this->response = $this->getModuleResponse($callback[0],$callback[1],params:$params);
                } else {
                    $this->response = $this->getModuleResponse($callback[0],$callback[1],$callback[2],$params);
                }
            } else {
                $this->error = ["status"=>404,"message" => "No callback function found","data"=>[]];
            }
        }

        /**
         * @description Method to render the response
         * @return array $response
         */
        public function render(array $response) :void {
            if (is_array($response)) {
                echo json_encode($response);
            } else {
                echo $response;
            }
        }

        /**
         * @description Method to destroy the application
         * @return void
         */
        public function end () :void {
            echo "Finish application execution";
            //if (isset($this->messenger)) {
            //    unset($this->messenger);
            //}
            //if (isset($this->routes)) {
            //    unset($this->routes);
            //}
            if (isset($this->error)) {
                unset($this->error);
            }
            if (isset($this->response)) {
                unset($this->response);
            }
            if (isset($this->method)) {
                unset($this->method);
            }
        }

        /**
         * @description Method to get the error
         */
        public function getError() :array {
            return $this->error;
        }

        /**
         * @description Method to get the response
         * @return array
         */
        public function response() :array {
            return $this->response;
        }

        /**
         * @description Method to get the response
         * @param string $module Module Name
         * @param string|null $controller Controller Name
         * @param string|null $method Method Name
         * @param array $params Parameter to pass
         * @return array|null
         */
        private function getModuleResponse(string $module, string $controller = null, string $method = null, array $params = []) :array|null {
            $component = $this->getComponent($module,$controller);
            if ($component instanceof \Throwable) {
                $this->error = ["status"=>$component->getCode(),"message" => $component->getMessage(), "data"=>[]];
                return null;
            }
            if (method_exists($component,$method)) {
                try {
                    return call_user_func_array($component::$method,$params);
                } catch (\Throwable $th) {
                    $this->error = ["status"=>$th->getCode(),"message" => $th->getMessage(), "data"=>[]];
                    return null;
                }
            } else {
                $this->error = ["status"=>404,"message" => "Method not found", "data"=>[]];
                return null;
            }
        }

        /**
         * @description Method to get the Module Component
         * @param string $module Module Name
         * @param string|null $component Component Name
         * @return object
         */
        private function getComponent(string $module, string $controller = null): object{
            $module = ucwords($module);
            $component = "B4R\\Modules\\".$module;
            $component .= $module . "\\Controllers\\";
            $component .= ($controller === null) ? $module : ucwords($controller);
            $component .= "Controller";
            try {
                if (class_exists($component)) {
                    try {
                        return new $component;
                    } catch (\Throwable $th) {
                        return $th;
                    }
                } else {
                    throw new \Error("Component not found");
                }
            } catch (\Throwable $th) {
                return $th;
            }
        }
    }