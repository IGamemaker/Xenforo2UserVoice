<?php

namespace ServerDev\UserVoice\XF\Pub\Controller;

class Forum extends XFCP_Forum
{
	protected function getAvailableForumSorts(\XF\Entity\Forum $forum)
	{
		// maps [name of sort] => field in/relative to Thread entity
		$filters = parent::getAvailableForumSorts($forum);
		$filters['likes'] = new \XF\Mvc\Entity\FinderExpression('(%s + %s)', ['likes', 'likes_base']);
		return $filters;
	}
}