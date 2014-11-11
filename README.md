Represent
===========

A representation layer that can be helpful when building RESTful API's



Install Guide
==============
1. make sure you have a composer install. If not just get it `curl -sS https://getcomposer.org/installer | php`
2. run `php composer.phar install`


Running Tests
=============
From the root directory: `phpunit`


Structure
=========

This library will use the (Builder Pattern)[http://en.wikipedia.org/wiki/Builder_pattern] to construct different representations of objects. The GenericRepresentationBuilder handles creating a basic array representation of objects. Format specific builders can take the GenericRepresentationBuilder's output and modify it as needed to create format specific representations. The GenericRepresentationBuilder will handle all visibility and context. Format Builders only need to specialize in their exact format, and do not need to worry about visibility.

