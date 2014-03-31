@if (!$comments->isEmpty())
	{{-- Lazy eager load the user data for each comment, this is for --}}
	{{-- performance reasons to mitigate against the n+1 query problem --}}
	<?php $comments->load('user'); ?>
	<ol class="comments--list">
		@foreach ($comments as $comment)
			<li class="comment" id="C{{ $comment->id }}">
				<p class="comment--text">
					{{ nl2br(htmlspecialchars($comment->comment, null, 'UTF-8')) }}
				</p>
				<p class="comment--author">
					{{{ $comment->user->username }}}
				</p>
				<p class="comment--date">
					{{ $comment->getDate() }}
				</p>
			</li>
		@endforeach
	</ol>
@else
	<p class="no-comments">
		{{ trans('laravel-comments::messages.no_comments') }}
	</p>
@endif