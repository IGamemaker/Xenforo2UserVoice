<?php

namespace ServerDev\UserVoice\XF\Service\Post;

class Preparer extends XFCP_Preparer
{
	public function afterInsert()
	{
		parent::afterInsert();

		$this->checkIsUserVoiceStaffAnswer();
	}

	public function afterUpdate()
	{
		parent::afterUpdate();

		$this->checkIsUserVoiceStaffAnswer();
	}

	protected function checkIsUserVoiceStaffAnswer()
	{
		$thread = $this->post->Thread;
		$forum = $thread->Forum;

		if($forum->uservoice_forum)
		{
			$user = $this->post->User;

			if($user->is_staff)
			{
				$message = $this->post->message;

				if(strpos($message, '[prefix=')!==false && !$this->post->isFirstPost())
				{
					$thread->staff_answer_post_id = $this->post->post_id;
					$thread->save();
				}
			}
		}
	}
}