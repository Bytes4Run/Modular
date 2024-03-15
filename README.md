# Modular

Modular (**Skeleton for a modular PHP web applications base on MVC pattern with Vite for auto web refresh, and 
Tailwindcss for a modern design**)

This framework is tailor over MVC pattern principals, in order to bring a efficient, easy to develop and modular 
applications; the framework application contains embedded base modules to have an example to work with, classes to resolve 
queries, build SQL queries, responses and views renderization in order to have a modular web application, api rest, or
service with a less programing code.

To use this application, you need PHP, JavaScript or TypeScript, HTML, Object Oriented Programing, server configuration, 
Git work flow basis (or higher) knowledge, download this repository, install all dependencies, configure the server to 
always resolve to "public/index.php". 
see apache .htaccess configuration for guidance. Also this application has a ".htaccess" file in it to make this easy.

For more information visit [Bytes4Run.com](https://bytes4run.com/projects/modular).

## Content Table

- [Modular](#modular)
  - [Content Table](#content-table)
  - [Prerequisites](#prerequisites)
  - [Getting the Project](#getting-the-project)
  - [Configuring the Project](#configuring-the-project)
  - [Technologies](#technologies)
  - [Data and Directory Structure](#data-and-directory-structure)
    - [Map](#map)
    - [Server Request](#server-request)
    - [Server Response](#server-response)
      - [Response types](#response-types)
      - [View Data Structure Example](#view-data-structure-example)
  - [Credits](#credits)
  - [FQA](#fqa)
  - [Open Source and Contribution](#open-source-and-contribution)

## Prerequisites

- PHP 8.1 or higher (8.1.7 recommended)
- MariaDB 8 or higher, or MySQL in its defect
- Had installed and activated this modules: php_curl, pdo, mod_rewrite
- A compatible Composer with PHP 8 (to use PHP 7 to install, update the composer.json to make it so)
- Node.js 18 or higher
- Git updated

## Getting the Project

To get the project, you can clone or fork this repository, install dependencies using Composer and NPM (you can use Yarn 
or bun if you want). Configure your server to resolve any request to "public/index.php"

**NOTE:** Add "AllowOverride All" to allow the overriding to your server configuration.

## Configuring the Project

Make sure you have a .env file in your "_configs/_" application directory, if you does not had one, copy the 
"_.env.example_" file and rename it to ".env". Also make this with "_config.json.example_" file too. 

Add your database configuration to the "_.env_" file, your application and company configuration to the "config.json" file
and we are ready to go.

## Technologies

- **[PHP](http://www.php.com)**
- **[HTML5](http://ww3.school.com)**
- **[JavaScript](http://www.javascript.com)**
- **[TypeScript](https://www.typescriptlang.org/)**

## Data and Directory Structure

### Map

```markdown
├── app
│ ├── core
│ │ ├── classes
│ │ ├── helpers
│ │ ├── libraries
│ │ ├── handlers
│ │ ├── entities
│ ├── modules
│ │ ├── _module_
│ │ │ ├── controllers
│ │ │ ├── models
│ │ │ ├── libraries
│ ├── App.php
├── public
│ ├── assets
│ │ ├── js
│ │ │ ├── custom
│ │ │ ├── global
│ │ ├── css
│ │ │ ├── custom
│ │ │ ├── global
│ │ ├── fonts
│ │ ├── img
│ │ ├── browserconfig.xml
│ │ ├── manifest.json
│ ├── uploads
│ ├── .htaccess
│ ├── index.html
│ ├── index.php
│ ├── robots.txt
├── resources
│ ├── views
│ │ ├── _engine_
│ │ │ ├── theme
│ │ │ │ ├── _module_
│ │ │ │ │ ├── templates
│ │ │ │ │ ├── layouts
├── cache
│ ├── smarty
│ ├── vite
├── configs
│ ├── .env.example
│ ├── config.json.example
├── tests
├── vendor
├── .env
├── .gitignore
├── composer.json
├── composer.lock
├── LICENCE
├── licence.txt
├── package.json
├── package-lock.json
├── postcss.config.js
├── README.md
├── tailwind.config.js
├── vite.config.ts
```

### Server Request

URI: [/{module}/{controller}/{method}]

```JSON
{
    "params": "array() | string | int"
}
```

Request example:

POST: [/{module}/{controller}/{method}/{params}]

GET: [/{module}/{controller}/{method}/{params}]

URL:
<http://server/{module}/{controller}/{method}/{params}> the params can be string [{param1}/{param2}] 
"_producto=algodon/date=12-16-05/date2=10-05-01_" o [?{key=value}&{key=value}] "_?producto=albondiga_"

URL Format:

- module/ (When controller has the same name of the module, this resolve: _method read in a controller with the same module 
name_)
- module/method/ (When you know the method name and the controller has the same name of the module, this resolve: 
  _a method in a controller with the same module name_)
- module/method?param1&param2 (When you have parameters and you know the method name and the controller has the same name 
  of the module, this resolve: _parameters for a method in a controller with the same module name_)
- module/controller/method/param1/param2 (When you pass all parameters as URI, this resolve: _parameters for a method in a 
  controller in a module_)
- module/controller/method?param1=value1&param2=value2(When you pass all parameters as URL, this resolve: _params for a method 
  in a controller in a module_)

### Server Response

The response from server will be a rendered view by the template engine configured in the .env file (Smarty, twig, self)
or a JSON string to be consume by a frontend framework like vue, react, astro, etc.

The response will be always at this format `php ['view'=>"",'content'=[]]`; for engine consumption, 
`json {status:200,message:"ok",data:[]}`; for frontend consumption.

#### Response types

All response has a "header", "body","footer" format, those can be a plain text, an array with additional information, 
according with the controller response.

If the response is a message, the schema could be:

```php
$response = [
    'head' => [
        'style' => [
            'title' => [
                'icon'  => "",
                'text'  => ""
                'color' => "",
            ]
        ],
        'text' => [
            'color' => "",
            'title' => ""
        ]
    ],
    'body' => [
        'breadcrumb' => [
            'main' => "",
            'routes' => [
                ['module' => "",
                'controller' => "",
                'method' => "",
                'params' => ""],
            ],
        ],
        'content' => [
                'mensaje' => "",
                'extra'   => "" | []
        ]
    ],
    'foot' => []
];
```

If message is a band to be displayed, the schema could be:

```php
$response = [
    'head' => [
        'title' => [
            'color' => "",
            'icon'  => "",
            'text'  => ""
        ],
    ],
    'body'=>[
        'message' => [
            'type'   => "",
            'text'   => "", | []
            'extra'  => ""
            'header' => [
                'icon'  => "",
                'title' => "",
                'text'  => ""
            ],
        ]
    ],
    'foot'=>[]
];
```

All views have a general information for the main layout, this consist in "head","css","js","icons","navbar","sidebar",
"footer","app configs" if you want to use a different one you can make your own structure inside the module as a helper to
be call any time a view is has to be render.

#### View Data Structure Example

```php
$response = [
    'content' => [], //where the content for the body will be
    'layout' => [
        'head' => [
            'template' => "template_name.tpl",
            'data' => [
                ['meta_name' => "", 'meta_content' => ""],
            ],
            'css' => ['<link rel="stylesheet" type="text/css" href="\assets\css\style.css">']
        ],
        'body' => ['layout' => '', 'darkmode' => ''],
        'footer' => [
            'tempalate' => "template_name.tpl",
            'data' => []
        ],
        'navbar' => [
            'template' => "template_name.tpl",
            'data' => []
        ],
        'sidebar' => [
            'template' => "template_name.tpl",
            'data' => []
        ],
        'jslibs'=>['<script type="" src=""></script>',...]
        'scripts' => '<script></script>' // An script to be use at the beginning of the DOM
    ]
];
```

## Credits

- Author: Jorge Echeverria.
- Contact: [jecheverria@bytes4run.com](mailto:jecheverria@bytes4run.com)
- Website: [bystes4run](https://bytes4run.com)
- Theme: Bytes4Run base on flowbite
- Version: 2.0.0 a.r1
- Short-version: 2.0

## FQA

## Open Source and Contribution

This "framework" or "skeleton" is Open Source, licensed under the Bytes4Run free licence base on MIT licence.

If you would like to contribute to the materials, please feel free to fork the repository and send us a pull request. Or
if you have a comment, question, or suggestion for improvements over this or other Bytes4Run projects, please feel free
[raise an issue](https://github.com/bytes4run/modular/issues); or writes us to [projects@bytes4run.com](mailto:projects@bytes4run.com)
