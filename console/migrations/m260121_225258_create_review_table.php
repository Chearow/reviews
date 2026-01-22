<?php

use yii\db\Migration;

class m260121_225258_create_review_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%review}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(100)->notNull(),
            'text' => $this->string(255)->notNull(),
            'rating' => $this->integer()->notNull(),
            'img' => $this->string()->defaultValue(null),
            'author_id' => $this->integer()->notNull(),
            'is_for_all' => $this->boolean()->defaultValue(false),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk_review_author',
            '{{%review}}',
            'author_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_review_author', '{{%review}}');
        $this->dropTable('{{%review}}');
    }
}
