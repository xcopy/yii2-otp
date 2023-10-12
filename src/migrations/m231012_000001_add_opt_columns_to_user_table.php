<?php

use yii\db\Migration;

/**
 * Class m20231012_000001_add_opt_columns_to_user_table
 */
class m231012_000001_add_opt_columns_to_user_table extends Migration
{
    /**
     * @inheritDoc
     */
    public function safeUp()
    {
        $this->addColumn('user', 'otp', $this->string());
        $this->addColumn('user', 'otp_expiry', $this->dateTime());
        $this->addColumn('user', 'otp_token', $this->string());
    }

    /**
     * @inheritDoc
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'otp_token');
        $this->dropColumn('user', 'otp_expiry');
        $this->dropColumn('user', 'otp');
    }
}
