<?php namespace Fbf\LaravelComments;

use \Crypt,
	\Input,
	\Config,
	\Auth,
	\Validator,
	\Redirect;

/**
 * Class CommentsController
 *
 * @package Fbf\LaravelComments
 */
class CommentsController extends \BaseController {

	/**
	 * @var Comment
	 */
	protected $comment;

	/**
	 * @param Comment $comment
	 */
	public function __construct(Comment $comment)
	{
		$this->comment = $comment;
	}

	/**
	 * Saves a comment
	 *
	 * @return \Redirect
	 */
	public function create()
	{
		$return = Input::get('return');
		if (empty($return))
		{
			return Redirect::to('/');
		}
		try {
			$commentable = Input::get('commentable');
			if (empty($commentable))
			{
				throw new Exception();
			}
			$commentable = Crypt::decrypt($commentable);
			if (strpos($commentable, '.') == false)
			{
				throw new Exception();
			}
			list($commentableType, $commentableId) = explode('.', $commentable);
			if (!class_exists($commentableType))
			{
				throw new Exception();
			}
			$commentableObj = new $commentableType;
			$table = $commentableObj->getTable();
			$key = $commentableObj->getKeyName();
			$data = array(
				'commentable_type' => $commentableType,
				'commentable_id' => $commentableId,
				'comment' => Input::get('comment'),
				'user_id' => Auth::user()->id,
			);
			$rules = array(
				'commentable_type' => 'required|in:'.implode(',', Config::get('laravel-comments::commentables')),
				'commentable_id' => 'required|exists:'.$table.','.$key,
				'comment' => 'required',
			);
			$validator = Validator::make($data, $rules);
			if ($validator->fails())
			{
				return Redirect::to($return)->withErrors($validator);
			}
			$this->comment->fill($data);
			$this->comment->save();
			$newCommentId = $this->comment->id;
			return Redirect::to($return.'#C'.$newCommentId);

		} catch (\Exception $e) {

			return Redirect::to($return.'#'.trans('laravel-comments::messages.add_form_anchor'))
				->with('laravel-comments::error', trans('laravel-comments::messages.unexpected_error'));

		}

	}

}