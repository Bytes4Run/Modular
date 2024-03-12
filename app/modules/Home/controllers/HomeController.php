<?php
    /**
     * @description Home controller
     * @category Loader
     * @author Jorge Echeverria <jecheverria@bytes4run.com>
     * @package B4R\Modules\Home\controllers
     * @license Bytes4Run
     * @version 1.0.0
     * @link https://bytes4run.com/
     * @copyright (c) 2021-2024 Bytes4Run
     */

     declare(strict_types=1);

     namespace B4R\Modules\Home\controllers;
     use B4R\Kernel\classes\Controller;

     class HomeController extends Controller{
         public function index() {
            return $this->view('home/index');
         }
         
    public function about()
    {
        return $this->view('home/about');
    }
    public function contact()
    {
        return $this->view('home/contact');
    }
     }