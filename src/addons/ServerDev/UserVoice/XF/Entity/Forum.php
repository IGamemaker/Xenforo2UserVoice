<?php

namespace ServerDev\UserVoice\XF\Entity;

class Forum extends XFCP_Forum
{
	public static function getStructure(\XF\Mvc\Entity\Structure $structure)
	{
		$structure = parent::getStructure($structure);

		$structure->columns['uservoice_forum'] = ['type' => self::BOOL, 'default' => false];

		return $structure;
	}
}