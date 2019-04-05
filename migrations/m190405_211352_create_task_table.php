<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%task}}`.
 */
class m190405_211352_create_task_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%task}}', [
            'id' => $this->primaryKey(),
            'text' => $this->string(300),
            'status' => $this->string(),
            'worker_id' => $this->integer(),
            'project_id' => $this->integer(),
        ]);

        $this->addForeignKey('fk-task-worker_id',
            'task','worker_id',
            'user','id');

        $this->addForeignKey('fk-task-project_id',
            'task','project_id',
            'project','id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-task-worker_id','task');
        $this->dropForeignKey('fk-task-project-id','task');
        $this->dropTable('{{%task}}');
    }
}
