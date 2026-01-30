<?php

use yii\db\Migration;

class m260130_091300_create_add_password_reset_token_to_user extends Migration
{

    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'password_reset_token', $this->string()->unique()->defaultValue(null));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'password_reset_token');
    }

}
