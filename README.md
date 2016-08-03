# How to use

## The languages

Put all the language files on the `langs` folder, they are all PHP files. On every file, define the same variables, but with the respective translation. For exemple, in the `en-us.php`, you can define the variable `$helloWorld`:

    $helloWorld = "Hello World";
	
So in the `pt-br.php`, you do the translation of the same variable:

    $helloWorld = "Olá Mundo";

## The pre-processor

First of all, change the primary language in the line 6 of `app.php`:

    const PRIMARY_LANG = 'pt-br';
	
This string have to match the language filename (without the extension). If the script find a primary language, then the static files will be generated in the root of the output folder (`/output/`), instead of the respective lang folder (e.g. "/output/en-us").

Put all the files to be processed in the `files` folder, every PHP file will be converted to the respective HTML file on the output folder. To print something that have to be translated, use PHP on the field:

    <h1><?= $helloWorld ?></h1>
	
The pre-processor will generate the file with every language on the respective folder. The files that aren't PHP, will remains intact.

## Run it
Simply run the `app.php` in a browser or command line:

    php app.php