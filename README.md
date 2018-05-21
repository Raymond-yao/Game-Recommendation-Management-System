Game Recommendation Management System provides functionalities of creating, updating, and deleting
game recommendation list of a user.

This is a web application whose backend is built using PHP slim MVC framework.
the dependency is injected using PHP composer, in order to build the project,
please first make sure PHP and PHP composer is installed:
PHP:  http://php.net/manual/en/install.php
PHP composer: https://getcomposer.org/

Then under project root directory (check whether there is a composer.json in this dir)
run the following command:

MAC OS: php composer.phar install
Windows: composer install

then diredct yourslef to /src, under this directory, run

MAC OS: php -S localhost:3000
Windows: php.exe -S localhost:3000

a web server should be started and listening on port 3000 on localhost