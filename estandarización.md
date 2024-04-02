# Estandarización de código

Como se ha visto en muchos proyectos, el código no siempre es limpio o listo para su uso. Es importante tener un proceso de estandarización para asegurar que el código está en el formato correcto, para evitar malos entendidos, código innecesario, malformación de funciones o métodos y clases; con esto se garantiza el entendimiento del código y su uso, así como futuros cambios.
Por ello, se declaran las siguientes reglas para la creación de archivos como clases, controles, ayudantes, manejadores, clases de librerías, entidades, modelos entre otros archivos a ser usados en la aplicación.

## Reglas de estandarización

Se estandariza el uso de las recomendaciones PSR para la creación de archivos que contendrán clases o funciones (métodos) a ser usados por la aplicación. Siguiendo las normas PSR.

La denominación de los controladores será en “camel-case” siguiendo la norma PSR-4 para crear espacios de nombre, en plural, en inglés, dicho nombre deberá terminar con la palabra “Controller” para denotar su uso como controlador.
Por ejemplo: UsersController.php

La denominación de los modelos al igual que el controlador, deberá ser siguiendo la normativa PSR-4 de creación de espacios de nombre, estar en idioma inglés, en número singular, en “camel-case”, con la terminación “Model” para denotar su uso como modelo del módulo.
Por ejemplo: UserModel.php

De utilizar ayudantes, estos deberán seguir la normativa PSR-4, con su nombre en “camel-case”, iniciando con un guion bajo, en idioma inglés, en número singular, con la terminación “Helper”, para denotar su uso como ayudante del módulo.
Por ejemplo: _UserNameCreatorHelper.php

De utilizarse manejadores, estos deberán seguir la normativa PSR-4, con su nombre en formato “camel-case”, iniciando con dos guiones bajos, en idioma inglés, número singular y con la terminación “Handler”, para denotar su uso como manejador del módulo.
Por ejemplo: __UserNamePermisionHandler.php

Sí se utilizarán librerías o extensiones de librerías, estos archivos deberán comenzar con un guion bajo, siguiendo la normativa PSR-4, con su nombre en formato “camel-case”, en idioma inglés, seguido por otro guion bajo y la palabra “Library”, para denotar que el uso es como librería.
Por ejemplo: _UserUniqueId_Library.php

El estándar PSR-12 se utilizará para darle un formato más legible y coherente al estilo del documento, así como la legibilidad de la lectura.
