<?php

use yii\db\Migration;

class m260121_225103_update_user_table extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('{{%user}}', 'username');
        $this->dropColumn('{{%user}}', 'status');
        $this->dropColumn('{{%user}}', 'updated_at');
        $this->dropColumn('{{%user}}', 'password_reset_token');

        $this->addColumn('{{%user}}', 'fio', $this->string()->notNull());
        $this->addColumn('{{%user}}', 'phone', $this->string()->notNull());
        $this->addColumn('{{%user}}', 'email_confirm_token', $this->string()->defaultValue(null));
        $this->addColumn('{{%user}}', 'is_email_confirmed', $this->boolean()->defaultValue(false));
    }

    public function safeDown()
    {
        $this->addColumn('{{%user}}', 'username', $this->string()->notNull()->unique());
        $this->addColumn('{{%user}}', 'status', $this->smallInteger()->notNull()->defaultValue(10));
        $this->addColumn('{{%user}}', 'updated_at', $this->integer()->notNull());
        $this->addColumn('{{%user}}', 'password_reset_token', $this->string()->unique());

        $this->dropColumn('{{%user}}', 'fio');
        $this->dropColumn('{{%user}}', 'phone');
        $this->dropColumn('{{%user}}', 'email_confirm_token');
        $this->dropColumn('{{%user}}', 'is_email_confirmed');
    }
}
