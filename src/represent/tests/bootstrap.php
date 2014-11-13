<?php

if (!($loader = include __DIR__ . '/../../../vendor/autoload.php')) {
    die(<<<EOT
You need to install the project dependencies using Composer:
$ wget http://getcomposer.org/composer.phar
OR
$ curl -s https://getcomposer.org/installer | php
$ php composer.phar install --dev
$ phpunit
EOT
    );
}
$loader->add('Represent\Tests', __DIR__);
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');