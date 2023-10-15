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
        $table = $this->getTable();

        $this->addColumn($table, 'otp', $this->string());
        $this->addColumn($table, 'otp_expiry', $this->dateTime());
        $this->addColumn($table, 'otp_token', $this->string());
    }

    /**
     * @inheritDoc
     */
    public function safeDown()
    {
        $table = $this->getTable();
        
        $this->dropColumn($table, 'otp_token');
        $this->dropColumn($table, 'otp_expiry');
        $this->dropColumn($table, 'otp');
    }

    /**
     * @return string
     */
    private function getTable(): string
    {
        /** @see \yii\db\ActiveRecord::tableName() */
        return call_user_func([Yii::$app->user->identityClass, 'tableName']);
    }
}
