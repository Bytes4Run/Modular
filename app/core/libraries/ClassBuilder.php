<?php
/** 
 * ClassBuilder
 * @description Helper to build classes
 * @author Jorge Echeverria <jecheverria@bytes4run.com> 
 * @category Library
 * @package Kernel\libraries\ClassBuilder
 * @version 1.0.0 rev.1 
 * @date 2024-04-16
 * @time 16:37:00
 * @copyright (c) 2023 Bytes4Run 
 */
declare(strict_types=1);
namespace Kernel\libraries;

use Kernel\helpers\Definer;

class ClassBuilder
{

    public function __construct()
    {
        new Definer;
    }

    /**
     * @param array $values
     * @return bool|string
     */
    public function build(array $values): bool|string
    {
        return match ($values[1]) {
            'model' => $this->buildModel($values[2], $values[3] ?? null, $values[4] ?? null),
            'controller' => $this->buildController($values[2], $values[3] ?? null, $values[4] ?? null),
            'class' => $this->buildClass($values[2], $values[3] ?? null, $values[4] ?? null),
            default => "The type $values[1] is not valid\n",
        };
    }
    /**
     * Prints a help message for the class builder CLI mode.
     *
     * @return string
     */
    public function help(): string
    {
        return "This is a class builder for CLI mode. Here's how to use it:\n
           To create a new class use: 'php build [type_of_class] [class_name] [options] [location]'\n
           Replace 'type_of_class' with the type of class you want to create (e.g., 'model', 'controller').\n
           Replace 'class_name' with the name of the class.\n
           Replace 'options' with any additional options you want to include in the class.\n
           Replace 'location' with the location where you want to save the class.\n
           For example, to create a new model class named 'User' in the 'models' directory, use: 'php model User models'\n
           To see this help message again, use: 'php help'\n
           ['options']:\n
               -f --force: Force the creation of the class even if it already exists\n
               -h --help: Show this help message\n
               -c --components: Create a class with components.\n";
    }
    /**
     * Builds a model class based on the given name, options, and location.
     *
     * @param string $name The name of the model
     * @param string|null $options The options for building the model (default: null)
     * @param string|null $location The location of the model (default: null)
     * @return bool|string Returns a message indicating whether the model was created or already exists, or null if an error occurred
     */
    private function buildModel(string $name, string $options = null, string $location = null): bool|string
    {
        $name = ucfirst($name);
        $model_basic = $this->getBase($name,'Model');
        if ($options == '-c' || $options == '--components') {
            ob_start();
            include "_build/templates/_model_components.php";
            $model_components = ob_get_clean();
            $content = str_replace('{name}', $name, $model_components);
            $model = str_replace('{content}', $content, $model_basic);
        } else {
            $content = "    public function __construct(int \$id = null) {\n";
            $content .= "        parent::__construct();\n";
            $content .= "        \$this->table = \"$name\";\n";
            $content .= "        \$this->_setTable(\$this->table);\n";
            $content .= "        \$this->error = null;\n";
            $content .= "        if (!is_null(\$id) && \$id > 0) {\n";
            $content .= "            \$this->_init_Model(\$id);\n";
            $content .= "        }\n";
            $content .= "    }\n";
            // Getters and Setters
            $content .= "   /** \n";
            $content .= "    * Function to get any value property from the Model\n";
            $content .= "    * \n";
            $content .= "    * @param string \$prop\n";
            $content .= "    * @param return mixed\n";
            $content .= "    */\n";
            $content .= "   public function __get(string \$name):mixed {\n";
            $content .= "       \$result = null;\n";
            $content .= "       if (property_exists(\$this, \$name)) {\n";
            $content .= "           if (in_array(\$name, ['id'])) {\n";
            $content .= "               \$result = intval(\$this->\$name);}\n";
            $content .= "           } else {\n";
            $content .= "               \$result = \$this->\$name;\n";
            $content .= "           }\n";
            $content .= "       }\n";
            $content .= "       return \$result;\n";
            $content .= "   }\n";
            $content .= "   /** \n";
            $content .= "    * Function to set any value property from the Model\n";
            $content .= "    * \n";
            $content .= "    * @param string \$name\n";
            $content .= "    * @param mixed \$value\n";
            $content .= "    * @return void\n";
            $content .= "    * @throws \Exception\n";
            $content .= "    */\n";
            $content .= "   public function __set(string \$name, \$value): void {\n";
            $content .= "       if (property_exists(\$this, \$name)) {\n";
            $content .= "           if (\$name == 'created_at' || \$name == 'updated_at') {\n";
            $content .= "               if (!is_null(\$value) && !empty(\$value)) {\n";
            $content .= "                   \$this->\$name = new DateTime(\$value);\n";
            $content .= "               } else {\n";
            $content .= "                   \$this->\$name = new DateTime();\n";
            $content .= "               }\n";
            $content .= "           } else {\n";
            $content .= "               \$this->\$name = \$value;\n";
            $content .= "           }\n";
            $content .= "       }\n";
            $content .= "   }\n";
            // Get error and set error
            $content .= "   /** \n";
            $content .= "    * Function to set any error occurring on the Model\n";
            $content .= "    * \n";
            $content .= "    * @param array \$error\n";
            $content .= "    * @return void\n";
            $content .= "    */\n";
            $content .= "   private function __setError(array \$error): void {\n";
            $content .= "       if (!is_null(\$this->error) && !empty(\$this->error)) {\n";
            $content .= "           self::\$error = \$error;\n";
            $content .= "       } else {\n";
            $content .= "           self::\$error = \$error;\n";
            $content .= "       }\n";
            $content .= "   }\n";
            $content .= "   /** \n";
            $content .= "    * Function to get the error from the Model\n";
            $content .= "    * \n";
            $content .= "    * @return null|array\n";
            $content .= "    * @throws \Exception\n";
            $content .= "    */\n";
            $content .= "   public static function getError (): ?array {\n";
            $content .= "       return self::\$error;\n";
            $content .= "   }\n";
            $model = str_replace('{content}', $content, $model_basic);
        }
        $filePath = (!is_null($location)) ? _MODULE_ . "$location/Models/" : _MODULE_ . "$name/Models/";
        $fileName = $name . "Model.php";
        if (file_exists($filePath . $fileName) && $options != '-f' && $options != '--force') {
            return "The Model $name already exists\n";
        } else {
            if (!file_exists($filePath)) {
                mkdir($filePath, 0777, true);
            }
            file_put_contents($filePath . $fileName, $model);
            return "The Model $name has been created\n";
        }
    }
    private function buildController(string $name, string $options = null, string $location = null): string
    {
        $name = ucfirst($name);
        $controller_basic = $this->getbase($name,'Controller');
        if ($options == '-c' || $options == '--components') {
            ob_start();
            include "_build/templates/_controller_components.php";
            $controller_components = ob_get_clean();
            $content = str_replace('{name}', $name, $controller_components);
            $controller = str_replace('{content}', $content, $controller_basic);
        } else {
            // Constructor
            $content = "    public function __construct(int \$id = null) {\n";
            $content .= "        \$this->model = new $name" . "Model;\n";
            $content .= "    }\n";
            // CRUD
            $controller = str_replace('{content}', $content, $controller_basic);
        }
        $filePath = (!is_null($location)) ? _MODULE_ . "$location/Controllers/" : _MODULE_ . "$name/Controllers/";
        $fileName = $name . "Controller.php";
        if (file_exists($filePath) && $options != '-f' && $options != '--force') {
            return "The Controller $name already exists\n";
        } else {
            if (!file_exists($filePath)) {
                mkdir($filePath, 0777, true);
            }
            file_put_contents($filePath . $fileName, $controller);
            return "The Controller $name has been created\n";
        }
    }
    private function buildClass(string $name, string $options = null, string $location = null): string
    {
        $name = ucfirst($name);
        $class = '';
        $class_basic = $this->getbase($name,'Class');
        if ($options == '-c' || $options == '--components') {
            ob_start();
            include "_build/templates/_class_components.php";
            $class_components = ob_get_clean();
            $class_basic = str_replace('{name}', $name, $class_components);
            $class = str_replace('{content}', $class_components, $class_basic);
        } else {
            $class = str_replace('{content}', '', $class_basic);
        }
        $filePath = (!is_null($location)) ? _MODULE_ . "$location/Classes/" : _MODULE_ . "$name/Classes/";
        $fileName = $name . "Class.php";
        if (file_exists($filePath) &&  $options != '-f' && $options != '--force') {
            return "The file $name already exists in $filePath\n";
        } else {
            if (!file_exists($filePath)) {
                mkdir($filePath, 0777, true);
            }
            file_put_contents($filePath . $fileName, $class);
            return "The Controller $name has been created\n";
        }
    }

    /**
     * @param string $name
     * @param string|null $type
     * @return string
     */
    public function getBase(string $name, string $type = null): string
    {
        $today = date('Y-m-d');
        $now = date('H:i:s');
        $name = ucfirst($name);
        $class = 'Model';
        if (!is_null($type)) {
            if ($type == 'model') {
                $class = 'Context';
            } elseif ($type == 'controller') {
                $class = 'Controller';
            }
        }
        $type = ucfirst($type);
        $_basic = file_get_contents(__DIR__ . "/_build/templates/class.txt");
        return str_replace(['{name}','{date}','{now}','{type}','{class}'], [$name,$today,$now,$type,$class], $_basic);
    }
}