<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "employees".
 *
 * @property integer $id
 * @property string $login
 * @property string $email
 * @property string $pwd_hash
 * @property integer $is_admin
 * @property integer $hour_mode
 * @property integer $first_day
 * @property string $name
 */
class Employee extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    const MODE_DAY_24 = 24;
    const MODE_DAY_12 = 12;
    const FIRST_DAY_SUNDAY = 0;
    const FIRST_DAY_MONDAY = 1;

    public static function tableName()
    {
        return 'employees';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login', 'email', 'name'], 'required'],
            [['is_admin', 'hour_mode', 'first_day'], 'integer'],
            [['login'], 'string', 'max' => 64],
            [['email'], 'string', 'max' => 129],
            [['pwd_hash'], 'string', 'max' => 40],
            [['name'], 'string', 'max' => 128],
            [['login'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Login',
            'email' => 'Email',
            'pwd_hash' => 'Pwd Hash',
            'is_admin' => 'Is Admin',
            'hour_mode' => 'Hour Mode',
            'first_day' => 'First Day',
            'name' => 'Name',
        ];
    }
}
