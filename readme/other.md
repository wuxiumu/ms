---
layout:     readme
title:      "Composer class library command that will be used"
subtitle:   "Log class"
date:       2019-01-11 20:00:00
author:     "吴庆宝"
tags:
    - phpms框架
---

(catfan/medoo: 最轻的PHP数据库框架，以加速开发) [https://medoo.in/doc] 

```
composer require catfan/Medoo
composer update
```

```
// If you installed via composer, just use this code to require autoloader on the top of your projects.
require 'vendor/autoload.php';

// Using Medoo namespace
use Medoo\Medoo;

// Initialize
$database = new Medoo([
    'database_type' => 'mysql',
    'database_name' => 'name',
    'server' => 'localhost',
    'username' => 'your_username',
    'password' => 'your_password'
]);

// Enjoy
$database->insert('account', [
    'user_name' => 'foo',
    'email' => 'foo@bar.com'
]);

$data = $database->select('account', [
    'user_name',
    'email'
], [
    'user_id' => 50
]);

echo json_encode($data);
```

(twig/twig:Twig，PHP的灵活，快速，安全的模板语言)[https://twig.symfony.com/doc/2.x/]


**Prerequisites¶**

Twig needs at least PHP 7.0.0 to run.

**Installation¶**

The recommended way to install Twig is via Composer:

```
composer require "twig/twig:^2.0"
```

**Basic API Usage¶**

This section gives you a brief introduction to the PHP API for Twig.
```
require_once '/path/to/vendor/autoload.php';

$loader = new Twig_Loader_Array([
    'index' => 'Hello {{ name }}!',
]);
$twig = new Twig_Environment($loader);

echo $twig->render('index', ['name' => 'Fabien']);
```

Twig uses a loader (Twig_Loader_Array) to locate templates, and an environment (Twig_Environment) to store the configuration.

The render() method loads the template passed as a first argument and renders it with the variables passed as a second argument.

As templates are generally stored on the filesystem, Twig also comes with a filesystem loader:

```
$loader = new Twig_Loader_Filesystem('/path/to/templates');
$twig = new Twig_Environment($loader, [
    'cache' => '/path/to/compilation_cache',
]);

echo $twig->render('index.html', ['name' => 'Fabien']);
```