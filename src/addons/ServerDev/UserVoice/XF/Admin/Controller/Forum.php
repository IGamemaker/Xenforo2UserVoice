<?php

namespace ServerDev\UserVoice\XF\Admin\Controller;

class Forum extends XFCP_Forum
{
	protected function saveTypeDataExtend(\XF\Mvc\FormAction $form, \XF\Entity\Node $node, \XF\Entity\AbstractNode $data)
	{
		$form->setup(function() use ($data)
	    {
	    	$input = $this->filter([
				'uservoice' => 'bool'
			]);

	        $data->uservoice_forum = $input['uservoice'];
	    });
	}

	protected function saveTypeData(\XF\Mvc\FormAction $form, \XF\Entity\Node $node, \XF\Entity\AbstractNode $data)
	{
	    parent::saveTypeData($form, $node, $data);
		$this->saveTypeDataExtend($form, $node, $data);
	}
}