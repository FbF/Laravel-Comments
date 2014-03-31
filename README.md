Laravel Comments
================

A Laravel 4 package for adding commenting to a website that has user accounts

## Features

* Attach comments to any/all existing models in your app. Comment model is polymorphic.
* Comments belong to a user (there is no anonymous commenting functionality built in at the moment)
* Includes a migration, model, controller for adding comments, sample partials for showing the comments list and the add comment form
* After adding a comment, user is redirected back to previous page, anchored to their new comment.
* A FrozenNode/Laravel-Administrator config file for administrators to view, edit and delete submitted comments

## Installation

Add the following to you composer.json file (Recommend swapping "dev-master" for the latest release)

    "fbf/laravel-comments": "dev-master"

Run

    composer update

Add the following to app/config/app.php

    'Fbf\LaravelComments\LaravelCommentsServiceProvider'

Run the package migration

    php artisan migrate --package=fbf/laravel-comments

Publish the config

    php artisan config:publish fbf/laravel-comments

Add your namespaced models to the list of commentable models in the `config/commentables.php` config file (this is just for validation purposes)

Optionally tweak the settings in the other config files for your app.

Optionally copy the administrator config file (`src/config/administrator/posts.php`) to your administrator model config directory.

## Configuration

See the configuration options in the files in the config directory

## Administrator

You can use the excellent Laravel Administrator package by FrozenNode to administer your comments.

http://administrator.frozennode.com/docs/installation

A ready-to-use model config file for the Comment model (commentsphp) is provided in the src/config/administrator directory of the package, which you can copy into the app/config/administrator directory (or whatever you set as the model_config_path in the administrator config file).

## Usage

Add the polymorphic / hasMany relationship to the models in your app that you want to be commentable:

```php
/**
 * Defines the polymophic hasMany / morphMany relationship between Post and Comment
 *
 * @return mixed
 */
public function comments()
{
    return $this->morphMany('Fbf\LaravelComments\Comment', 'commentable');
}
```

Add the comments partial to the blade templates where you want the comments displayed, passing in the instance of the
 commentable model, and the comments.

```php
@include('laravel-comments::comments', array('commentable' => $post, 'comments' => $post->comments))
```

## Extending

The `laravel-comments::comments` partial used above actually includes 2 other partials, `laravel-comments::list` and
 `laravel-comments::form`. You can override any or all of these simply by copying them in your `app/views/fbf/laravel/comments`
 directory.

To override the `Comment` model in the package, create a model in you app/models directory that extends the package model.

Then, update the IoC Container to inject an instance of your model into the `Fbf\LaravelComments\CommentsController`,
instead of the package's model, e.g. in `app/start/global.php`

```php
App::bind('Fbf\LaravelComments\CommentsController', function() {
    return new Fbf\LaravelComments\CommentsController(new Comment);
});
```

You can choose not to use the package routes file and use your own, for routing through to your own controller to handle
 the creation of comments, which you may wish to extend from the package's controller or not.