# Standarization

As we have seen on many projects, the code is not always clean and ready to use. It is important to have a standarization process to make sure that the code is in the right format, to avoid missunderstandings, not-used code, function or method, classes wrong format and time spent on cleaning, with this we warrantee the understanding of what the code do or use, also future changes.

For that in mind,  the following rules are base to create new files like classes, controllers, helpers, handlers, libraries, entities, models, amoun others that will be use by the application.

## Use of PHP PSR standards

The use of PHP PSR (PHP Standar Recommendations) for files creation with classes, methods or functions to be use for the application, base on PSR.

The naming convention for controllers will be with all words capitalize base on PSR-4 to create name spaces, in plural and English, this name must end with "Controller" to denote its use.
For example: _UsersController.php_

The naming convention for models, must be iqual to the controller, following the PSR-4 for name spaces, with all words capitalize, write in English, singular, and end with “Model” to denote its use as model of the module.
For exampl: _UserModel.php_

If the module use helpers, this has to follow PSR-4 recommendations for name spaces, with all words capitalize, starting with underscore, in English language, singular, end with "Helper" to denote its use as helper for the module.
For example: *_UserHelper.php*

If the module use handlers, this has to follow PSR-4 recommendations for name spaces, starting with two underscores, with all words capitalize, in English language, singular, ending with “Handler”, to denote its use in the module.
For example: *__UserHandler.php*

If the module use libraries or extends libraries, this has to follow PSR-4 recommendations, with all words capitalize starting with underscore, in English, singular, follow by underscore and the word “Library”, to denote the use of a library.
For example: *_User_Library.php*

The standar PSR-12 will be use to format the document to more legible and coherent styling and readability.
