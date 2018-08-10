<?php

namespace ServerDev\UserVoice\XF\Pub\Controller;

class Thread extends XFCP_Thread
{
	public function setupThreadEdit(\XF\Entity\Thread $thread)
    {
        $editor = parent::setupThreadEdit($thread);

        if($thread->canChangeBaseLikes())
        {
        	$editor->setBaseLikes($this->filter('likes_base', 'str'));
        }

        return $editor;
    }

	public function actionVote(\XF\Mvc\ParameterBag $params)
	{
		$thread = $this->assertViewableThread($params->thread_id);
		if (!$thread->canVote($error))
		{
			return $this->noPermission($error);
		}

		/** @var \XF\ControllerPlugin\Like $likePlugin */
		$likePlugin = $this->plugin('XF:Like');
		return $likePlugin->actionToggleLikeUservoice(
			$thread,
			$this->buildLink('threads/vote', $thread),
			$this->buildLink('threads', $thread),
			$this->buildLink('threads/votes', $thread)
		);
	}

	public function actionVotes(\XF\Mvc\ParameterBag $params)
	{
		$thread = $this->assertViewableThread($params->thread_id);

		$breadcrumbs = $thread->getBreadcrumbs();
		$title = \XF::phrase('members_who_voted_x', ['title' => $thread->title]);

		/** @var \XF\ControllerPlugin\Like $likePlugin */
		$likePlugin = $this->plugin('XF:Like');
		return $likePlugin->actionLikesUservoice(
			$thread,
			['threads/votes', $thread],
			$title, $breadcrumbs
		);
	}

	public function actionVoteAgainst(\XF\Mvc\ParameterBag $params)
	{
		$thread = $this->assertViewableThread($params->thread_id);
		if (!$thread->canVote($error))
		{
			return $this->noPermission($error);
		}

		/** @var \XF\ControllerPlugin\Like $likePlugin */
		$likePlugin = $this->plugin('XF:Like');
		return $likePlugin->actionToggleDislikeUservoice(
			$thread,
			$this->buildLink('threads/vote-against', $thread),
			$this->buildLink('threads', $thread),
			$this->buildLink('threads/votes-against', $thread)
		);
	}

	public function actionVotesAgainst(\XF\Mvc\ParameterBag $params)
	{
		$thread = $this->assertViewableThread($params->thread_id);

		$breadcrumbs = $thread->getBreadcrumbs();
		$title = \XF::phrase('members_who_voted_against_x', ['title' => $thread->title]);

		/** @var \XF\ControllerPlugin\Like $likePlugin */
		$likePlugin = $this->plugin('XF:Like');
		return $likePlugin->actionDislikesUservoice(
			$thread,
			['threads/votes-against', $thread],
			$title, $breadcrumbs
		);
	}
}