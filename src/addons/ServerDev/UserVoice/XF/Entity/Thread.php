<?php

namespace ServerDev\UserVoice\XF\Entity;

class Thread extends XFCP_Thread
{
	public function rebuildCounters()
	{
		$return = parent::rebuildCounters();

		$this->rebuildStaffAnswerInfo();

		return $return;
	}

	public function rebuildStaffAnswerInfo()
	{
		$staffAnswer = $this->db()->fetchRow("
			SELECT xf_post.post_id, xf_post.user_id, xf_post.username
			FROM xf_post 
				INNER JOIN xf_user 
					ON xf_user.user_id = xf_post.user_id
			WHERE xf_post.thread_id = ?
				AND xf_post.message_state = 'visible'
				AND xf_user.is_staff = 1
				AND xf_post.message LIKE '%[prefix=%'
			ORDER BY xf_post.post_date DESC
			LIMIT 1
		", $this->thread_id);
		if (!$staffAnswer)
		{
			return false;
		}

		$this->staff_answer_post_id = $staffAnswer['post_id'];

		return true;
	}

	public function canVote(&$error = null)
	{
		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			return false;
		}

		if ($this->user_id == $visitor->user_id)
		{
			$error = \XF::phraseDeferred('liking_own_content_cheating');
			return false;
		}

		if($this->discussion_open == false)
		{
			return false;
		}

		if(!$this->Forum->uservoice_forum)
		{
			return false;
		}

		return $visitor->hasNodePermission($this->node_id, 'like');
	}

	public function canChangeBaseLikes(&$error = null)
	{
		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			return false;
		}

		if(!$this->Forum->uservoice_forum)
		{
			return false;
		}

		if ($visitor->is_admin || $visitor->is_moderator)
		{
			return true;
		}

		return false;
	}

	public function isVoteInitiator(&$error = null)
	{
		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			return false;
		}

		if($this->Forum->uservoice_forum)
		{
			if ($this->user_id == $visitor->user_id)
			{
				return true;
			}
		}

		return false;
	}

	public function isVoted()
	{
		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			return false;
		}

		return isset($this->Likes[$visitor->user_id]);
	}	

	public function isVotedFor()
	{
		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			return false;
		}

		return isset($this->Likes[$visitor->user_id]) && $this->Likes[$visitor->user_id]->is_dislike == false;
	}

	public function isVotedAgainst()
	{
		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			return false;
		}

		return isset($this->Likes[$visitor->user_id]) && $this->Likes[$visitor->user_id]->is_dislike == true;
	}

	public function getVoteRating()
	{
		return $this->likes + $this->likes_base + $this->dislikes + $this->dislikes_base;
	}

	public static function getStructure(\XF\Mvc\Entity\Structure $structure)
	{
		$structure = parent::getStructure($structure);

		$structure->columns['likes'] = ['type' => self::UINT, 'forced' => true, 'default' => 0];
		$structure->columns['likes_base'] = ['type' => self::UINT, 'forced' => true, 'default' => 1];
		$structure->columns['like_users'] = ['type' => self::SERIALIZED_ARRAY, 'default' => []];
		$structure->columns['dislikes'] = ['type' => self::UINT, 'forced' => true, 'default' => 0];
		$structure->columns['dislikes_base'] = ['type' => self::UINT, 'forced' => true, 'default' => 1];
		$structure->columns['dislike_users'] = ['type' => self::SERIALIZED_ARRAY, 'default' => []];
		$structure->columns['staff_answer_post_id'] = ['type' => self::UINT, 'default' => 0];

		$structure->behaviors['XF:Likeable'] = ['stateField' => 'discussion_state'];

		$structure->relations['Likes'] = [
				'entity' => 'XF:LikedContent',
				'type' => self::TO_MANY,
				'conditions' => [
					['content_type', '=', 'thread'],
					['content_id', '=', '$thread_id']
				],
				'key' => 'like_user_id',
				'order' => 'like_date'
		];

		$structure->relations['StaffPost'] = [
				'entity' => 'XF:Post',
				'type' => self::TO_ONE,
				'conditions' => [['post_id', '=', '$staff_answer_post_id']],
				'primary' => true
		];

		return $structure;
	}
}