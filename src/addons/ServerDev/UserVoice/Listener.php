<?php

namespace ServerDev\UserVoice;

use XF\Mvc\Entity\Entity;

class Listener
{
    public static function forumEntityStructure(\XF\Mvc\Entity\Manager $em, \XF\Mvc\Entity\Structure &$structure)
    {
    	$structure->columns['uservoice_forum'] = ['type' => Entity::INT, 'nullable' => false, 'default' => 0];
    }

    public static function threadItemRender(\XF\Template\Templater $templater, &$type, &$template, &$name, array &$arguments, array &$globalVars)
    {
    	$forum = isset($arguments['forum'])?$arguments['forum']:false;

    	if($forum && $forum->uservoice_forum)
    	{
    		$template = 'uservoice_thread_list_macros';
    	}
    }
}