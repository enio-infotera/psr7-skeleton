# PHP Skeleton Application

A very basic PSR-7 / PSR-15 application for PHP.

[![Latest Version on Packagist](https://img.shields.io/github/release/odan/psr7-skeleton.svg)](https://github.com/odan/psr7-skeleton/releases)
[![Software License](https://img.shields.io/badge/license-BSD-brightgreen.svg)](LICENSE.md)
[![Build Status](https://travis-ci.org/odan/psr7-skeleton.svg?branch=master)](https://travis-ci.org/odan/psr7-skeleton)
[![Quality Score](https://scrutinizer-ci.com/g/odan/psr7-skeleton/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/odan/psr7-skeleton/?branch=master)
[![Total Downloads](https://poser.pugx.org/odan/psr7-skeleton/downloads)](https://packagist.org/packages/odan/psr7-skeleton)

## Requirements

* PHP 7.2+
* Composer
* MySQL 5.7+
* Apache with mod_rewrite
* [Apache Ant](https://ant.apache.org/)

## Recommended

* NPM

## Features

This project comes configured with:

* Dependency injection container (PSR-11)
* HTTP request and response (PSR-7)
* Middleware (PSR-15)
* Routes with groups and middleware
* Single action controllers
* Logging (Monolog)
* Translations
* Sessions
* Authentication and Authorization
* Database Query Builder (cakephp/database)
* Database Migrations (Phinx)
* Database Migration Generator
* Date and time (Chronos)
* Console Commands (Symfony)
* Unit testing (phpunit)

**Middleware:**

* CSRF protection
* CORS
* Session
* Language
* Authentication

**Rendering:**

* Twig
* Assets (js, css) minification and caching
* Twig translations

**Continous integration:**

* Tested on Travis CI and Scrutinizer CI
* Unit tests
* Integration tests (http and database)
* PHPStan
* Code style checker and fixer (PSR-1, PSR-2, PSR-12)
* DocBlock checker (PSR-5)
* Ant scripts
* Deployment scripts

## Installation

### Manual

* [Download ZIP](https://github.com/odan/psr7-hello-world/archive/master.zip)
* Create a new database
* Run `composer update`
* Run `php bin/cli.php install`
* Open the application in your browser

### Using Composer

Read more: [Install the application with Composer.](https://odan.github.io/psr7-skeleton/#installation)

## Documentation

Full documentation of this application can be found here: <https://odan.github.io/psr7-skeleton/>.

## License

The BSD 2-Clause License. Please see [License File](LICENSE) for more information.

