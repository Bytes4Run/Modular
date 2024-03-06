<?php
    /**
     * This is the main file that will be loaded when the user visits the website, and lunch the application
     */
    require '../vendor/autoload.php';

    # NameSpace For main root loader
    use B4R\App;
    
    # Instance of App to Initialize the application and apply authorization and cors resolution
    $app = new App();
    # Obtaining Module response
    $response = ($app->run()) ? $app->response() : $app->getError();
    # Rendering the view or message
    $app->render($response);
    # Destroying application instance
    $app->end();