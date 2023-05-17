# Kellton Laravel Tools

This package was created in mind to be used internally.
We will never provide backward compatibility for this package.
This package probably have many bugs that we are not aware of, or we don't use this package in a way that you do.
If you want to use this package, you should fork it and use your own version.
Happy to get any feedback, or pull requests, but don't expect us to merge them (sorry!).

This package provides a set of tools to help you develop Laravel applications.

## Installation

You can install the package via composer:

```bash
composer require kellton/laravel-tools
```

## Development

To start developing first you must install the dependencies:

```bash
docker run --rm -v $(pwd):/app composer install
```

### Testing

To test the package you can run:

```bash
docker run --rm -v $(pwd):/app -w /app php:8.2-cli vendor/bin/phpunit
```

### Security

If you discover any security related issues, please email rafal.lempa@tivix.com instead of using the issue tracker.

## Features

### Undefined

In PHP, we are still missing a way to define undefined variables. The [Undefined class](src/Undefined.php) is a
solution for that.

### Builder

The [Builder class](src/Builders/Builder.php) is a wrapper around the Laravel's Eloquent Builder, it provides a set of
methods to help you build complex queries.

### Command

The [Command class](src/Commands/Command.php) is a wrapper around the Laravel's Command class, that allowing you to use
Dependency Injection in your commands.

### Data

The [Data feature](src/Features/Data) is a extended version
of [Value object](https://en.wikipedia.org/wiki/Value_object) that can be used to represent a data structure.

This solution allows you to validate input data
using [Laravel's validation rules](https://laravel.com/docs/8.x/validation#available-validation-rules).

### Dependency

The [Dependency feature](src/Features/Dependency) allowing you Lazy Dependency Injection in your classes.

Standard way of using Dependency Injection in Laravel is to use the constructor to inject dependencies, but whenever a
classes is created, all the dependencies are also resolved. This can be a problem when you have a lot of dependencies,
and you don't need all of them in every case.

In this case we resolve dependencies only when they are first used.

### Action

The [Action feature](src/Features/Action) is a wrapper around the Laravel's Controller and Service classes, that
allowing you to skip huge amount of boilerplate code.

### Initializer

The [Initializer feature](src/Features/Initializer) allows you to initialize your project with a set of predefined data.

Initializer needs to be **impotent**, so it can be run multiple times without any side effects.
So you are able to add new data to the initializer without worrying about breaking existing data.
As this is only mechanism to initialize data, you are **responsible** for keeping queries **impotent**.

### OpenApi documentation

The [OpenApi documentation feature](src/Features/OpenApi) allows you to generate OpenApi documentation for your API.

### Read model

The [Read model feature](src/Features/ReadModel) allows you to create a read models for your application.

## Directory structure

### Builder

The [Builder directory](src/Builders) contains a set of commonly used traits and Eloquent Builders.

### Commands

The [Commands directory](src/Commands) is a place for a commonly used commands.

### Data

The [Data directory](src/Data) contains a set of commonly used data classes.

### Enums

The [Enums directory](src/Enums) contains a set of commonly used enums.

### Exceptions

The [Exception directory](src/Exceptions) contains a set of commonly used exceptions.

### Features

The [Features directory](src/Features) contains a set of commonly used features. If you need to create a feature that is
composed of multiple files you can create a directory with the same name as the feature and put all the files there.

### Helpers

The [Helpers directory](src/Helpers) contains a set of commonly used helpers.

### Models

The [Models directory](src/Models) contains a set of commonly used models.

### Rules

The [Rules directory](src/Rules) contains a set of commonly used rules.
