<?php

namespace ServerDev\UserVoice\XF\Entity;

class LikedContent extends XFCP_LikedContent
{
	public static function getStructure(\XF\Mvc\Entity\Structure $structure)
	{
		$structure = parent::getStructure($structure);

		$structure->columns['is_dislike'] = ['type' => self::BOOL, 'default' => false];

		return $structure;
	}
}