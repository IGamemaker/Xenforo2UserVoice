<?php

namespace ServerDev\UserVoice\BbCode;

class Renderer
{
	//used for show status on post body
	public static function renderThreadTitle($tagChildren, $tagOption, $tag, array $options, \XF\BbCode\Renderer\AbstractRenderer $renderer)
	{
		if (!$tag['option'])
		{
			return $renderer->renderUnparsedTag($tag, $options);
		}

		$id = intval($tag['option']);

		$templater = \XF::app()->templater();
		$prefix = $templater->fnPrefix($templater,$escape, 'thread', intval($id), 'html' );

		if(array_key_exists('children', $tag))
		{
			$prefix.=$renderer->renderSubTreePlain($tag['children']);
		}

		return $prefix;
	}
}