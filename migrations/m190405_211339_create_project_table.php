<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%project}}`.
 */
class m190405_211339_create_project_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%project}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'manager_id' => $this->integer(),
        ]);

        $this->addForeignKey('fk-project-manager_id',
            'project', 'manager_id',
            'user', 'id',
            'SET NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-project-manager_id', 'project');
        $this->dropTable('{{%project}}');
    }
}
