<?php

namespace ServerDev\UserVoice\Pub\View\Like;

use XF\Mvc\View;

class Like extends View
{
	public function renderJson()
	{
		$isLiked = $this->params['isLiked'];
		$count = $this->params['count'];
		$likes = $this->params['likes'];
		$listUrl = $this->params['listUrl'];

		$incrementLike = $this->params['increment_like'];
		$decrementLike = $this->params['decrement_like'];
		$incrementDislike = $this->params['increment_dislike'];
		$decrementDislike = $this->params['decrement_dislike'];

		$likeCoef = $this->params['likeCoef'];
		$dislikeCoef = $this->params['dislikeCoef'];

		if ($count && $likes)
		{
			$templater = $this->renderer->getTemplater();
			$html = $templater->fn('likes', [$count, $likes, $isLiked, $listUrl]);
		}
		else
		{
			$html = '';
		}

		return [
			'text' => $isLiked ? \XF::phrase('unlike') : \XF::phrase('like'),
			'html' => $this->renderer->getHtmlOutputStructure($html),

			'increaseLike' => $incrementLike,
			'decreaseLike' => $decrementLike,
			'increaseDislike' => $incrementDislike,
			'decreaseDislike' => $decrementDislike,

			'likeCoef' => $likeCoef,
			'dislikeCoef' => $dislikeCoef,
		];
	}
}