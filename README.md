# Boilerplate para proyectos Laravel

[![phpstan](https://github.com/josecl/php-library-boilerplate/actions/workflows/phpstan.yml/badge.svg)](https://github.com/josecl/php-library-boilerplate/actions/workflows/phpstan.yml)
[![tests](https://github.com/josecl/php-library-boilerplate/actions/workflows/tests.yml/badge.svg)](https://github.com/josecl/php-library-boilerplate/actions/workflows/tests.yml)

## Requerimientos

- php 8.0

## Features

- Pest (phpunit)
- php-cs-fixer
- phpstan
- roave/security-advisories
- GitHub actions: tests en php 8.0, 8.1. 

## Instalación

```shell
git clone https://github.com/josecl/php-library-boilerplate.git
cd php-library-boilerplate
git remote rm origin
```

Cambiar las referencias, por ejemplo para un proyecto `user/example-project`:

```shell
git remote add origin https://github.com/user/example-project.git
```

Reemplazar masivamente (case-sentitive) los strings:

- `Josecl`: `User`
- `josecl`: `user`
- `php-library-boilerplate`: `example-project`
- `PhpLibraryBoilerplate`: `ExampleProject`

Instalar dependencias y probar que todo funcione bien.

```shell
composer update
vendor/bin/pest
```


## Tareas siguientes

- Comprobar licencia: Por defecto utiliza MIT.
