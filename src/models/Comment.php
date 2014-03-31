<?php namespace Fbf\LaravelComments;

class Comment extends \Eloquent {

	/**
	 * Name of the table to use for this model
	 * @var string
	 */
	protected $table = 'fbf_comments';

	/**
	 * The fields that are fillable
	 * @var array
	 */
	protected $fillable = array(
		'commentable_type',
		'commentable_id',
		'comment',
		'user_id',
	);

	/**
	 * Defines polymorphic relationship type
	 * @return mixed
	 */
	public function commentable()
	{
		return $this->morphTo();
	}

	/**
	 * Defines the belongsTo relationship
	 * @return mixed
	 */
	public function user()
	{
		return $this->belongsTo('User');
	}

	/**
	 * Returns a string representing the record that the comment was on. This is normally the name or title field of the
	 * commentable model. E.g. the title of the Blog Post. The method checks to see if the commentable model has a method
	 * called getCommentableTitle() first, else it checks for a field called title and then one called name. If none of
	 * the cases are met, an exception is thrown.
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function getOnTitleAttribute()
	{
		$commentable = $this->commentable;

		$attributes = $commentable->getAttributes();

		if (method_exists($commentable, 'getCommentableTitle'))
		{
			$on = $commentable->getCommentableTitle();
		}
		elseif (array_key_exists('title', $attributes))
		{
			$on = $attributes['title'];
		}
		elseif (array_key_exists('name', $attributes))
		{
			$on = $attributes['name'];
		}
		else
		{
			throw new \Exception(sprintf('%s model does not have title or name attribute, nor implements getCommentableTitle() method', get_class($commentable)));
		}

		return \Str::limit($on, 50);

	}

	public function getCommentForAdministratorAttribute($value)
	{
		return \Str::limit(htmlspecialchars($this->comment, null, 'UTF-8'), 50);
	}

	public function getUrl()
	{
		$commentable = $this->commentable;

		$url = false;
		if (method_exists($commentable, 'getUrl'))
		{
			$url = $commentable->getUrl();
		}

		return \URL::to($url.'#C'.$this->id);
	}

	/**
	 * Returns the locale formatted date
	 * @return string
	 */
	public function getDate()
	{
		$date = $this->created_at;
		$date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date);
		setlocale(LC_TIME, \App::getLocale());
		$dateFormat = trans('laravel-comments::messages.date_format');
		if ($dateFormat == 'laravel-comments::messages.date_format')
		{
			$dateFormat = '%e %B %Y at %H:%M';
		}
		$date = $date->formatLocalized($dateFormat);
		return $date;
	}

}

