<?php

Route::post(
	Config::get('laravel-comments::routes.base_uri'),
	array(
		'before' => array(
			'csrf',
			'auth',
		),
		'uses' => 'Fbf\LaravelComments\CommentsController@create',
	)
);