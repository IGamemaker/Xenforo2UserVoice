<?php

namespace ServerDev\UserVoice\XF\ControllerPlugin;

use XF\Mvc\Entity\Entity;

class Like extends XFCP_Like
{
	//based at actionToggleLike, extend for dislike support
	public function actionToggleLikeUservoice(Entity $entity, $confirmUrl, $returnUrl, $likesUrl, $contentTitle = null)
	{
		$visitor = \XF::visitor();

		$contentType = $entity->getEntityContentType();
		$contentId = $entity->getEntityId();

		if (!$contentType)
		{
			throw new \InvalidArgumentException("Provided entity {$entity->structure()->shortName} must define a content type in its structure");
		}

		/** @var \XF\Repository\LikedContent $likeRepo */
		$likeRepo = $this->repository('XF:LikedContent');

		$likeHandler = $likeRepo->getLikeHandler($contentType, true);

		if ($this->isPost())
		{
			$isLikedRaw = $likeRepo->toggleLikeVote($contentType, $contentId, $visitor);
			$isLiked = $isLikedRaw != 0;
			$cache = $likeHandler->getContentLikeCaches($entity);
			$options = \XF::options();

			if ($this->filter('_xfWithData', 'bool'))
			{
				$viewParams = [
					'isLiked' => $isLiked,
					'count' => isset($cache['count']) ? $cache['count'] : null,
					'likes' => isset($cache['recent']) ? $cache['recent'] : null,
					'increment_like' => $isLikedRaw == 1 || $isLikedRaw == -1,
					'decrement_like' => $isLikedRaw == 0,
					'increment_dislike' => false,
					'decrement_dislike' => $isLikedRaw == -1,
					'listUrl' => $likesUrl,
					'likeCoef' => $options->uservoiceLikeCoefficient,
					'dislikeCoef' => $options->uservoiceDislikeCoefficient,
				];
				return $this->view('ServerDev\UserVoice:Like\Like', '', $viewParams);
			}
			else
			{
				return $this->redirect($returnUrl);
			}
		}
		else
		{
			$isLiked = (bool)$likeRepo->getLikeByContentAndLikerForVote($contentType, $contentId, $visitor->user_id);

			$viewParams = [
				'confirmUrl' => $confirmUrl,
				'contentTitle' => $contentTitle,
				'isLiked' => $isLiked
			];
			return $this->view('XF:Like\Confirm', 'like_confirm', $viewParams);
		}
	}

	//based at actionLikes, add dislike condition
	public function actionLikesUservoice(Entity $entity, array $likeLinkData, $title = null, array $breadcrumbs = [])
	{
		$contentType = $entity->getEntityContentType();
		$contentId = $entity->getEntityId();

		if (!$contentType)
		{
			throw new \InvalidArgumentException("Provided entity must defined a content type in its structure");
		}

		/** @var \XF\Repository\LikedContent $likeRepo */
		$likeRepo = $this->repository('XF:LikedContent');

		$page = $this->filterPage();
		$perPage = 50;

		$likes = $likeRepo->findContentLikes($contentType, $contentId)
			->with('Liker')
			->where('is_dislike', false)
			->limitByPage($page, $perPage, 1)
			->fetch();

		if (!count($likes))
		{
			return $this->message(\XF::phrase('no_one_has_liked_this_content_yet'));
		}

		$hasNext = count($likes) > $perPage;
		$likes = $likes->slice(0, $perPage);

		$viewParams = [
			'type' => $contentType,
			'id' => $contentId,

			'linkRoute' => isset($likeLinkData[0]) ? $likeLinkData[0] : '',
			'linkData' => isset($likeLinkData[1]) ? $likeLinkData[1] : null,
			'linkParams' => (isset($likeLinkData[2]) && is_array($likeLinkData[2])) ? $likeLinkData[2] : [],

			'likes' => $likes,
			'hasNext' => $hasNext,
			'page' => $page,

			'title' => $title,
			'breadcrumbs' => $breadcrumbs
		];
		return $this->view('XF:Like\Listing', 'like_list', $viewParams);
	}

	//based at actionToggleLike, extend for dislike support
	public function actionToggleDislikeUservoice(Entity $entity, $confirmUrl, $returnUrl, $likesUrl, $contentTitle = null)
	{
		$visitor = \XF::visitor();

		$contentType = $entity->getEntityContentType();
		$contentId = $entity->getEntityId();

		if (!$contentType)
		{
			throw new \InvalidArgumentException("Provided entity {$entity->structure()->shortName} must define a content type in its structure");
		}

		/** @var \XF\Repository\LikedContent $likeRepo */
		$likeRepo = $this->repository('XF:LikedContent');

		$likeHandler = $likeRepo->getLikeHandler($contentType, true);

		if ($this->isPost())
		{
			$isLikedRaw = $likeRepo->toggleLikeUnvote($contentType, $contentId, $visitor);
			$isLiked = $isLikedRaw != 0;
			$cache = $likeHandler->getContentLikeCaches($entity);
			$options = \XF::options();

			if ($this->filter('_xfWithData', 'bool'))
			{
				$viewParams = [
					'isLiked' => $isLiked,
					'count' => isset($cache['count']) ? $cache['count'] : null,
					'likes' => isset($cache['recent']) ? $cache['recent'] : null,
					'increment_like' => false,
					'decrement_like' => $isLikedRaw == -1,
					'increment_dislike' => $isLikedRaw == 1 || $isLikedRaw == -1,
					'decrement_dislike' => $isLikedRaw == 0,
					'listUrl' => $likesUrl,
					'likeCoef' => $options->uservoiceLikeCoefficient,
					'dislikeCoef' => $options->uservoiceDislikeCoefficient,
				];
				return $this->view('ServerDev\UserVoice:Like\Like', '', $viewParams);
			}
			else
			{
				return $this->redirect($returnUrl);
			}
		}
		else
		{
			$isLiked = (bool)$likeRepo->getLikeByContentAndLikerForUnvote($contentType, $contentId, $visitor->user_id);

			$viewParams = [
				'confirmUrl' => $confirmUrl,
				'contentTitle' => $contentTitle,
				'isLiked' => $isLiked
			];
			return $this->view('XF:Like\Confirm', 'like_confirm', $viewParams);
		}
	}

	//based at actionLikes, add dislike condition
	public function actionDislikesUservoice(Entity $entity, array $likeLinkData, $title = null, array $breadcrumbs = [])
	{
		$contentType = $entity->getEntityContentType();
		$contentId = $entity->getEntityId();

		if (!$contentType)
		{
			throw new \InvalidArgumentException("Provided entity must defined a content type in its structure");
		}

		/** @var \XF\Repository\LikedContent $likeRepo */
		$likeRepo = $this->repository('XF:LikedContent');

		$page = $this->filterPage();
		$perPage = 50;

		$likes = $likeRepo->findContentLikes($contentType, $contentId)
			->with('Liker')
			->where('is_dislike', true)
			->limitByPage($page, $perPage, 1)
			->fetch();

		if (!count($likes))
		{
			return $this->message(\XF::phrase('no_one_has_liked_against_this_content_yet'));
		}

		$hasNext = count($likes) > $perPage;
		$likes = $likes->slice(0, $perPage);

		$viewParams = [
			'type' => $contentType,
			'id' => $contentId,

			'linkRoute' => isset($likeLinkData[0]) ? $likeLinkData[0] : '',
			'linkData' => isset($likeLinkData[1]) ? $likeLinkData[1] : null,
			'linkParams' => (isset($likeLinkData[2]) && is_array($likeLinkData[2])) ? $likeLinkData[2] : [],

			'likes' => $likes,
			'hasNext' => $hasNext,
			'page' => $page,

			'title' => $title,
			'breadcrumbs' => $breadcrumbs
		];
		return $this->view('XF:Like\Listing', 'like_list', $viewParams);
	}
}