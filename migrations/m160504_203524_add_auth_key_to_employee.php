<?php

use yii\db\Migration;

/**
 * Handles adding auth_key to table `employee`.
 */
class m160504_203524_add_auth_key_to_employee extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('employees', 'auth_key', $this->string(32));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('employees', 'auth_key');
    }
}
