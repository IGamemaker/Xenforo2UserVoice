<?php

namespace ServerDev\UserVoice\XF\Service\Thread;

class Editor extends XFCP_Editor
{
    protected $likes_base;

    public function setBaseLikes($value)
    {
        $this->thread->likes_base = $value;
    }
}