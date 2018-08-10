<?php

namespace ServerDev\UserVoice\Like;

class Thread extends \XF\Like\AbstractHandler
{
	public function likesCounted(\XF\Mvc\Entity\Entity $entity)
	{
		return false;
	}

	public function getEntityWith()
	{
		$visitor = \XF::visitor();

		return ['Forum', 'Forum.Node.Permissions|' . $visitor->permission_combination_id];
	}

	public function publishLikeNewsFeed(\XF\Entity\User $sender, $contentId, \XF\Mvc\Entity\Entity $content)
	{
	}

	public function unpublishLikeNewsFeed(\XF\Entity\LikedContent $like)
	{

	}
}