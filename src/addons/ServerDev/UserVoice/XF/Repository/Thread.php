<?php

namespace ServerDev\UserVoice\XF\Repository;

class Thread extends XFCP_Thread
{
	public function findThreadsForForumView(\XF\Entity\Forum $forum, array $limits = [])
	{
		$finder = parent::findThreadsForForumView($forum, $limits);
		
		if($forum->uservoice_forum)
		{
			$finder->with('FirstPost');
			$finder->with('StaffPost');
		}

		return $finder;
	}
}