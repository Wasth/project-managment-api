<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%comment}}`.
 */
class m190405_211402_create_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%comment}}', [
            'id' => $this->primaryKey(),
            'text' => $this->string(300),
            'user_id' => $this->integer(),
            'task_id' => $this->integer(),
        ]);

        $this->addForeignKey('fk-comment-user_id',
            'comment','user_id',
            'user','id');

        $this->addForeignKey('fk-comment-task_id',
            'comment','task_id',
            'task','id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-comment-task_id','task');
        $this->dropForeignKey('fk-comment-user_id','task');
        $this->dropTable('{{%comment}}');
    }
}
