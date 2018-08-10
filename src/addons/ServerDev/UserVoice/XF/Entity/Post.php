<?php

namespace ServerDev\UserVoice\XF\Entity;

class Post extends XFCP_Post
{
	public function isUserVoiceStaffPost()
	{
		$thread = $this->Thread;
		if (!$thread)
		{
			return false;
		}

		return ($this->post_id == $thread->staff_answer_post_id);
	}
}