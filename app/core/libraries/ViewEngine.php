<?php
/**
 * ViewEngine Library
 * @description This class is used to initialize and configure the engine to render the views
 * @category Library
 * @author Jorge Echeverria <jecheverria@bytes4run.com>
 * @package Kernel\libraries\ViewEngine
 * @version 1.0.0
 * @date 2024-03-06
 * @time 11:00:00
 *
 */

declare (strict_types = 1);

namespace Kernel\libraries;

class ViewEngine
{
    /**
     * @var mixed
     */
    private mixed $engineClass;
    /**
     * Constructor
     */
    public function __construct(
        private string $engine = 'smarty',
        private array $literal = ['left' => '{{', 'right' => '}}'],
        private bool $caching = false) {
        $this->initEngine();
    }

    /**
     * Function to assign a variable to the view
     * @param string $name
     * @param mixed $value
     * @return void|\Exception
     */
    public function assign(string $name, $value): void {
        switch ($this->engine) {
            case 'smarty':
                $this->engineClass->assign($name, $value);
                break;
            case 'twig':
                $this->engineClass->addGlobal($name, $value);
                break;
            default:
                throw new \Exception("Engine not supported");
                break;
        }
    }

    /**
     * Function to render the view
     * @param string $view
     * @return void|\Exception
     */
    public function render(string $view): void {
        $renderized = '';
        switch ($this->engine) {
            case 'smarty':
                $renderized = $this->engineClass->fetch($view);
                break;
            case 'twig':
                $renderized = $this->engineClass->render($view);
                break;
            default:
            $renderized = json_encode(['message'=>"Engine not supported", 'status'=>500]);
                break;
        }
        echo $renderized;
        /* echo "<pre>";
        var_dump($this->engine);
        echo "</pre>";
        exit; */
    }

    /**
     * Function to initialize the engine
     *
     * @param string $engine
     * @param array $literal
     * @param bool $caching
     * @return void|\Exception
     */
    private function initEngine(): void
    {
        switch ($this->engine) {
            case 'smarty':
                $this->initSmarty($this->literal, $this->caching);
                break;
            /* case 'twig':
                $this->initTwig($this->literal, $this->caching);
                break; */
            case 'json':
                break;
            default:
                throw new \Exception("Engine not supported");
                break;
        }
    }

    /**
     * Function to initialize the Smarty engine
     *
     * @param array $literal
     * @param bool $caching
     * @return void
     */
    private function initSmarty(array $literal, bool $caching): void
    {
        $this->engineClass = new \Smarty;
        $this->engineClass->setTemplateDir(_VIEW_);
        $this->engineClass->setConfigDir(_CONF_ . "smarty/config");
        $this->engineClass->setCacheDir(_CACHE_ . "smarty/cache/");
        $this->engineClass->setCompileDir(_CACHE_ . "smarty/compiles/");
        $this->engineClass->left_delimiter = $literal['left'];
        $this->engineClass->right_delimiter = $literal['right'];
        $this->engineClass->caching = $caching;
    }

    /**
     * Function to initialize the Twig engine
     *
     * @param array $literal
     * @param bool $caching
     * @return void
     */
    /* private function initTwig(array $literal, bool $caching): void
    {
        $loader = new \Twig\Loader\FilesystemLoader(_VIEW_);
        $this->engineClass = new \Twig\Environment($loader, [
            'cache' => _CACHE_ . 'twig',
            'auto_reload' => true,
        ]);
    } */
}
