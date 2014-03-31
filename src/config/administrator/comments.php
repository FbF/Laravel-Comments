<?php

return array(

	/**
	 * Model title
	 *
	 * @type string
	 */
	'title' => 'Comments',

	/**
	 * The singular name of your model
	 *
	 * @type string
	 */
	'single' => 'comment',

	/**
	 * The class name of the Eloquent model that this config represents
	 *
	 * @type string
	 */
	'model' => 'Fbf\LaravelComments\Comment',

	/**
	 * The columns array
	 *
	 * @type array
	 */
	'columns' => array(
		'user_id' => array(
			'title' => 'User',
			'relationship' => 'user', //this is the name of the Eloquent relationship method!
			'select' => "CONCAT((:table).username, ' / ', (:table).email)",
		),
		'comment_for_administrator' => array(
			'title' => 'Comment',
			'select' => 'comment',
		),
		'commentable_type' => array(
			'title' => 'Type',
			'editable' => false,
		),
		'on_title' => array(
			'title' => 'Title',
		),
		'created_at' => array(
			'title' => 'Created'
		),
	),

	/**
	 * The edit fields array
	 *
	 * @type array
	 */
	'edit_fields' => array(
		'comment' => array(
			'title' => 'Comment',
			'type' => 'textarea',
		),
		'created_at' => array(
			'title' => 'Created',
			'type' => 'datetime',
			'editable' => false,
		),
		'updated_at' => array(
			'title' => 'Updated',
			'type' => 'datetime',
			'editable' => false,
		),
	),

	/**
	 * The filter fields
	 *
	 * @type array
	 */
	'filters' => array(
		'comment' => array(
			'type' => 'text',
			'title' => 'Content',
		),
		'created_at' => array(
			'title' => 'Created',
			'type' => 'datetime',
		),
	),

	/**
	 * The width of the model's edit form
	 *
	 * @type int
	 */
	'form_width' => 500,

	/**
	 * The validation rules for the form, based on the Laravel validation class
	 *
	 * @type array
	 */
	'rules' => array(
		'comment' => 'required',
	),

	/**
	 * The sort options for a model
	 *
	 * @type array
	 */
	'sort' => array(
		'field' => 'created_at',
		'direction' => 'desc',
	),

	/**
	 * The action_permissions option lets you define permissions on the four primary actions: 'create', 'update', 'delete', and 'view'.
	 * It also provides a secondary place to define permissions for your custom actions.
	 *
	 * @type array
	 */
	'action_permissions'=> array(
		'create' => function($model)
			{
				return false;
			}
	),

	/**
	 * If provided, this is run to construct the front-end link for your model
	 *
	 * @type function
	 */
	'link' => function($model)
		{
			return $model->getUrl();
		},

);