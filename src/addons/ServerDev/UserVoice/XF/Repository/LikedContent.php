<?php

namespace ServerDev\UserVoice\XF\Repository;

class LikedContent extends XFCP_LikedContent
{
	public function getLikeByContentAndLikerForVote($contentType, $contentId, $userId)
	{
		return $this->finder('XF:LikedContent')->where([
			'content_type' => $contentType,
			'content_id' => $contentId,
			'like_user_id' => $userId,
			'is_dislike' => 0,
		])->fetchOne();
	}

	public function getLikeByContentAndLikerForUnvote($contentType, $contentId, $userId)
	{
		return $this->finder('XF:LikedContent')->where([
			'content_type' => $contentType,
			'content_id' => $contentId,
			'like_user_id' => $userId,
			'is_dislike' => 1,
		])->fetchOne();
	}

	public function toggleLikeVote($contentType, $contentId, \XF\Entity\User $likeUser, $publish = true)
	{
		$like = $this->getLikeByContentAndLiker($contentType, $contentId, $likeUser->user_id);
		if (!$like)
		{
			$this->insertLike($contentType, $contentId, $likeUser, $publish);
			return 1;
		}
		else
		{
			if($like->is_dislike)
			{
				$like->is_dislike = false;
				$like->save();

				$this->rebuildContentLikeCache($contentType, $contentId);
				return -1;
			}
			else
			{
				$like->delete();
				return 0;
			}
		}
	}

	public function toggleLikeUnvote($contentType, $contentId, \XF\Entity\User $likeUser, $publish = true)
	{
		$like = $this->getLikeByContentAndLiker($contentType, $contentId, $likeUser->user_id);
		if (!$like)
		{
			$like = $this->insertLike($contentType, $contentId, $likeUser, $publish);
			$like->is_dislike = true;
			$like->save();

			$this->rebuildContentLikeCache($contentType, $contentId);
			return 1;
		}
		else
		{
			if($like->is_dislike)
			{
				$like->delete();

				$this->rebuildContentLikeCache($contentType, $contentId);
				return 0;
			}
			else
			{
				$like->is_dislike = true;
				$like->save();

				$this->rebuildContentLikeCache($contentType, $contentId);
				return -1;
			}
		}
	}

	//support dislike for threads for uservoice
	public function rebuildContentLikeCache($contentType, $contentId, $throw = true)
	{
		if($contentType != 'thread')
		{
			parent::rebuildContentLikeCache($contentType, $contentId, $throw);
		}
		else
		{
			$likeHandler = $this->getLikeHandler($contentType, $throw);
			if (!$likeHandler)
			{
				if ($throw)
					throw new \InvalidArgumentException("No like handler found for '$contentType'");
				return false;
			}

			$entity = $likeHandler->getContent($contentId);
			if (!$entity)
			{
				if ($throw)
					throw new \InvalidArgumentException("No entity found for '$contentType' with ID $contentId");
				return false;
			}

			$countLikes = $this->db()->fetchOne("
				SELECT COUNT(*)
				FROM xf_liked_content
				WHERE content_type = ? AND content_id = ? AND is_dislike = 0
			", [$contentType, $contentId]);

			$countDislikes = $this->db()->fetchOne("
				SELECT COUNT(*)
				FROM xf_liked_content
				WHERE content_type = ? AND content_id = ? AND is_dislike = 1
			", [$contentType, $contentId]);

			if ($countLikes)
			{
				$latestLikes = $this->db()->fetchAll("
					SELECT user.user_id, user.username
					FROM xf_liked_content AS liked
					INNER JOIN xf_user AS user ON (liked.like_user_id = user.user_id)
					WHERE liked.content_type = ? AND liked.content_id = ? AND liked.is_dislike = 0
					ORDER BY liked.like_date DESC
					LIMIT 5
				", [$contentType, $contentId]);
			}
			else
			{
				$latestLikes = [];
			}

			if ($countDislikes)
			{
				$latestDislikes = $this->db()->fetchAll("
					SELECT user.user_id, user.username
					FROM xf_liked_content AS liked
					INNER JOIN xf_user AS user ON (liked.like_user_id = user.user_id)
					WHERE liked.content_type = ? AND liked.content_id = ? AND liked.is_dislike = 1
					ORDER BY liked.like_date DESC
					LIMIT 5
				", [$contentType, $contentId]);
			}
			else
			{
				$latestDislikes = [];
			}

			//updateContentLikes simulate
			if (!$countLikes && !$latestLikes && !$countDislikes && !$latestDislikes)
			{
				return true;
			}

			$entity->likes = $countLikes;
			$entity->like_users = $latestLikes;
			$entity->dislikes = $countDislikes;
			$entity->dislike_users = $latestDislikes;

			$entity->save();

			return true;
		}
	}
}