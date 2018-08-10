<?php

namespace ServerDev\UserVoice;

use XF\AddOn\AbstractSetup;

class Setup extends AbstractSetup
{
	public function install(array $stepParams = [])
	{
		$this->schemaManager()->alterTable('xf_forum', function (\XF\Db\Schema\Alter $table)
        {
			$table->addColumn('uservoice_forum', 'tinyint')->nullable(false)->setDefault(0)
				  ->after('allow_poll');
        });

        $this->schemaManager()->alterTable('xf_thread', function (\XF\Db\Schema\Alter $table)
        {
			$table->addColumn('likes', 'int')->nullable(false)->setDefault(0);
			$table->addColumn('likes_base', 'int')->nullable(false)->setDefault(1);
			$table->addColumn('like_users', 'blob');
            $table->addColumn('dislikes', 'int')->nullable(false)->setDefault(0);
            $table->addColumn('dislikes_base', 'int')->nullable(false)->setDefault(0);
            $table->addColumn('dislike_users', 'blob');
            $table->addColumn('staff_answer_post_id', 'int')->nullable(false)->unsigned()->setDefault(0);
        });

        $this->schemaManager()->alterTable('xf_liked_content', function (\XF\Db\Schema\Alter $table)
        {
            $table->addColumn('is_dislike', 'tinyint')->setDefault(0);
        });
	}

	public function upgrade(array $stepParams = [])
	{
		if($this->addOn->version_id < 1001002)
        {
            $this->schemaManager()->alterTable('xf_thread', function (\XF\Db\Schema\Alter $table)
            {
               $table->addColumn('likes_base', 'int')->nullable(false)->setDefault(1)->after('likes');
            });
        }

        if($this->addOn->version_id < 1001003)
        {
            $this->schemaManager()->alterTable('xf_thread', function (\XF\Db\Schema\Alter $table)
            {
               $table->addColumn('staff_answer_post_id', 'int')->nullable(false)->unsigned()->setDefault(0)->after('like_users');
            });
        }

        if($this->addOn->version_id < 100104)
        {
            $this->schemaManager()->alterTable('xf_thread', function (\XF\Db\Schema\Alter $table)
            {
                $table->addColumn('dislikes', 'int')->nullable(false)->setDefault(0);
                $table->addColumn('dislikes_base', 'int')->nullable(false)->setDefault(0);
                $table->addColumn('dislike_users', 'blob')->nullable(false);
            });

            $this->schemaManager()->alterTable('xf_liked_content', function (\XF\Db\Schema\Alter $table)
            {
                $table->addColumn('is_dislike', 'tinyint')->setDefault(0);
            });
        }
	}

	public function uninstall(array $stepParams = [])
	{
		$this->schemaManager()->alterTable('xf_forum', function (\XF\Db\Schema\Alter $table)
        {
            $table->dropColumns(['uservoice_forum']);
        });

        $this->schemaManager()->alterTable('xf_thread', function (\XF\Db\Schema\Alter $table)
        {
            $table->dropColumns(['likes', 'like_users', 'likes_base', 'staff_answer_post_id', 'dislikes', 'dislike_users', 'dislikes_base']);
        });
	}
}