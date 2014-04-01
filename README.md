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

The above is an example for adding comments to a Post model. If you want comments on something else, obviously change
 the mentions of the word 'Post' or '$post' to that of your model.

To use in conjunction with fbf/laravel-blog, (or the model in any other package for that matter), see the section in the
 readme about extending the Post model in your own app. I.e. you need to extend the Post model in your app/models directory
 and add the relationship in that file, rather than adding this to the model file in the vendor directory.

## To note...

To make integration really easy, and to increase performance, there's a line in the `list.blade.php` view partial that lazy eager
 loads the user data for each comment. If you override this view partial, and you omit this line, when you try to show the
 username of the user who wrote each comment, you may cause a big performance hit on your database, as it makes a separate
 query for each comment's author:

```php
{{-- Lazy eager load the user data for each comment, this is for --}}
{{-- performance reasons to mitigate against the n+1 query problem --}}
<?php $comments->load('user'); ?>
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

### Example to use webpurify to replace profanities in comments with asterisks on create

* Install https://github.com/agencyrepublic/webpurify via composer
* Sign up for an API key https://www.webpurify.com/
* Override the comment model in the package (create `app/models/comment.php`):

```php
<?php

class Comment extends Fbf\LaravelComments\Comment {

	public static function boot()
	{
		parent::boot();

		static::creating(function($comment)
		{
			$apiKey = Config::get('webpurify.api_key');
			$webPurifyText = new WebPurify\WebPurifyText($apiKey);
			$comment->comment = $webPurifyText->replace($comment->comment);
		});

	}

}
```

* Ensure your new model is the one that is used by the controller, rather than the package model (in `app/start/global.php`):

```php
App::bind('Fbf\LaravelComments\CommentsController', function() {
    return new Fbf\LaravelComments\CommentsController(new Comment);
});
```

* Add your API key to a config file (`app/config/webpurify.php`):

```php
<?php

return array(
	'api_key' => 'your_api_key_goes_here',
);
```