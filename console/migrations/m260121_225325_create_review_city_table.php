<?php

use yii\db\Migration;

class m260121_225325_create_review_city_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%review_city}}', [
            'review_id' => $this->integer()->notNull(),
            'city_id' => $this->integer()->notNull(),
        ]);

        $this->addPrimaryKey('pk_review_city', '{{%review_city}}', ['review_id', 'city_id']);

        $this->addForeignKey(
            'fk_review_city_review',
            '{{%review_city}}',
            'review_id',
            '{{%review}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_review_city_city',
            '{{%review_city}}',
            'city_id',
            '{{%city}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_review_city_review', '{{%review_city}}');
        $this->dropForeignKey('fk_review_city_city', '{{%review_city}}');
        $this->dropTable('{{%review_city}}');
    }
}
