# Modular

Modular (**Skeleton para aplicaciones web PHP modulares basado en patrón MVC con Vite para auto refresco, y Tailwindcss 
para un diseño moderno**)

Este framework está desarrollado sobre principios del patrón MVC, para un desarrollo eficiente y fácil, de aplicaciones 
modulares; la applicación framework contiene módulos base para tener un ejemplo de como trabajar él, clases para resolver 
consultas, constructor de consultas SQL, renderizado de vistas para tener una aplicación web modular, API Rest o servicio 
con poco programación de código.

Para usar esta aplicación, necesitas tener conocimientos básicos (o altos) de PHP, JavaScript o TypeScript, HTML, 
Programación Orientada a Objectos, configuración de servidores, flujo de trabajo Git, descargar este repositorio, instalar 
todas sus dependencias, configurar el servidor para siempre resolver hacia "public/index.php". ver configuración .htaccess 
para apache para tener una guía. Así mismo ésta aplicación tiene un archivo ".htaccess" dentro para hacerlo más fácil.

Para más información visita [Bytes4Run.com](https://bytes4run.com/projects/modular).

## Tabla de Contenido

- [Modular](#modular)
  - [Tabla de Contenido](#tabla-de-contenido)
  - [Prerequisitos](#prerequisitos)
  - [Obteniendo el Proyecto](#obteniendo-el-proyecto)
  - [Configurando el Proyecto](#configurando-el-proyecto)
  - [Tecnologías](#tecnologías)
  - [Tema y diseño](#tema-y-diseño)
  - [Estructura de Datos y Directorios](#estructura-de-datos-y-directorios)
    - [Map](#map)
    - [Petición al Servidor](#petición-al-servidor)
    - [Respuesta del Servidor](#respuesta-del-servidor)
      - [Tipos de Respuesta](#tipos-de-respuesta)
      - [Ejemplo de estructura de Datos para Vistas](#ejemplo-de-estructura-de-datos-para-vistas)
  - [Créditos](#créditos)
  - [FQA](#fqa)
  - [Código Abierto y Contribución](#código-abierto-y-contribución)

## Prerequisitos

- PHP 8.1 o superior (8.1.7 recomendado)
- MariaDB 8 o superior, o MySQL en su defecto
- Tener instalado y activado los módulos, php_curl, pdo, mod_rewrite
- Un Composer compatible con PHP 8 (para usar PHP 7 al instalar, actualice el archivo composer.json)
- Node.js 18 o superior
- Git

## Obteniendo el Proyecto

Para obtener el proyecto, puede clonar o hacer fork de este repositorio, instalar las dependencias usando 
Composer y NPM (puede usar Yarn o bun sí así lo desea). Configure su servidor para resolver cualquier 
petición hacia "public/index.php"

**NOTA:** Agregar "AllowOverride All" para permitir la sobre escritura de su configuración del servidor.

## Configurando el Proyecto

Asegurece de tener un archivo .env en su carpeta "_configs/_"; si no tiene uno, copie el archivo "_.env.example_" y 
renombrelo como ".env". Así mismo hacer con el archivo "_config.json.example_".

Agregue su configuración de database al archivo "_.env_", su configuración de aplicación and compañia al archivo 
"config.json" y todo está listo.

## Tecnologías

- **[PHP](http://www.php.com)**
- **[HTML5](http://ww3.school.com)**
- **[JavaScript](http://www.javascript.com)**
- **[TypeScript](https://www.typescriptlang.org/)**

## Tema y diseño

- **[Tailwindcss](https://tailwindcss.com/)**
- **[Flowbite](https://flowbite.com/)**

## Estructura de Datos y Directorios

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

### Petición al Servidor

URI: [/{module}/{controller}/{method}]

```JSON
{
    "params": "array() | string | int"
}
```

Petición ejemplo:

POST: [/{module}/{controller}/{method}/{params}]

GET: [/{module}/{controller}/{method}/{params}]

URL:
<http://server/{module}/{controller}/{method}/{params}> Los parámetros pueden ser cadenas de texto [{param1}/{param2}]
"_producto=algodon/date=12-16-05/date2=10-05-01_" o [?{key=value}&{key=value}] "_?producto=albondiga_"

URL Format:

- module/ (Cuando el controlador tiene el mismo nombre del módulo, esto resuelve: _método "read" en un controlador 
  con el mismo nombre del módulo_)
- module/method/ (Cuando se conoce el nombre del método, y el controlador tiene el mismo nombre que el módulo 
  esto resuelve: _un método en un controlador con el mismo nombre del módulo_)
- module/method?param1&param2 (Cuando se conocen los parámetros, el nombre del método y el controlador tiene 
  el mismo nombre que el módulo, esto resuelve: _parámetros para un método en un controlador con el mismo nombre 
  del módulo_)
- module/controller/method/param1/param2 (Cuando se pasan todos los parámetros como una URI, esto resuelve: _parámetros 
  para un método en un controlador en un módulo_)
- module/controller/method?param1=value1&param2=value2(Cuando se pasan parámetros como una URL, esto resuelve: _parámetros 
  para método en un controlador en un módulo_)

### Respuesta del Servidor

La respuesta del servidor será una vista renderizada por el motor de plantillas configurado en el archivo .env (Smarty, 
twig, self) o una cadena en formato JSON para ser consumida por un framework frontend como vue, react, astro, etc.

La respuesta será siempre en este formato `php ['view'=>"",'content'=[]]`; para el consumo con motores,
`json {status:200,message:"ok",data:[]}`; para el consumo frontend.

#### Tipos de Respuesta

Toda respuesta tiene un formato "header", "body","footer", esta puede ser un texto plano, un arreglo con información 
adicional, dependiendo de la respuesta del controlador.

Sí la respuesta es un mensaje, sú esquema será:

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

Sí sú mensaje es mostrado en una banda, el esquema será:

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

Toda vista tiene la información general de la "layout" principal a ser usada, esto consiste en "head","css","js","icons",
"navbar","sidebar","footer","app configs" si se desea usar una diferente, puede hacer su estructura dentro de un helper y
llamarle antes de responder al creador de vistas.

#### Ejemplo de estructura de Datos para Vistas

```php
$response = [
    'content' => [], // Donde el contenido será el cuerpo de la vista
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
        'scripts' => '<script></script>' // Un script para ser usado en el inicio del DOM
    ]
];
```

## Créditos

- Author: Jorge Echeverria.
- Contact: [jecheverria@bytes4run.com](mailto:jecheverria@bytes4run.com)
- Website: [bystes4run](https://bytes4run.com)
- Theme: Bytes4Run basado en tailwindcss y flowbite
- Version: 2.0.0 a.r1
- Short-version: 2.0

## FQA

## Código Abierto y Contribución

Este "framework" o "skeleton" es de Código Abierto, licensiado bajo la licencia de uso libre de Bytes4Run basada en la 
licencia de uso de MIT (Massachusetts Institute Technology).

Sí desea contribuir, sientase libre de realizar una bifurcación al repositorio y enviar una petición de "pull". O puede 
dejar un comentario, inquietud o sugerencia para mejoras sobre este u otro proyecto de Bytes4Run, [raise an issue](https://github.com/bytes4run/modular/issues);
o escríbanos a [projects@bytes4run.com](mailto:projects@bytes4run.com)