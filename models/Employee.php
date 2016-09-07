<?php

namespace app\models;

use Yii;
use \yii\web\IdentityInterface;
use \yii\db\ActiveRecord;

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
 * @property string $auth_key
 */
class Employee extends ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    const MODE_HOUR_24 = 24;
    const MODE_HOUR_12 = 12;
    const FIRST_DAY_SUNDAY = 0;
    const FIRST_DAY_MONDAY = 1;
    const FIRST_DAY_NAMES = [
        self::FIRST_DAY_SUNDAY => 'Sunday',
        self::FIRST_DAY_MONDAY => 'Monday',
    ];
    const HOUR_MODE_NAMES = [
        self::MODE_HOUR_12 => '12 am/pm',
        self::MODE_HOUR_24 => '24 /day',
    ];


    const SCENARIO_NEW_FROM_SCHEMA = 'new from schema';

    public static function tableName()
    {
        return 'employees';
    }

    public function init()
    {
        parent::init();
        if ($this->scenario == self::SCENARIO_NEW_FROM_SCHEMA) {
            $schema = $this->getDb()->getTableSchema($this->tableName());
            $this->hour_mode = $schema->columns['hour_mode']->defaultValue;
            $this->first_day = $schema->columns['first_day']->defaultValue;
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login', 'email', 'name', 'hour_mode', 'first_day'], 'required'],
            [['login', 'email', 'name', 'hour_mode', 'first_day'], 'required', 'on' => self::SCENARIO_NEW_FROM_SCHEMA],
            [['is_admin', 'hour_mode', 'first_day'], 'integer'],
            [['login'], 'string', 'max' => 64],
            [['email'], 'string', 'max' => 129],
            [['pwd_hash'], 'string', 'max' => 40],
            [['name'], 'string', 'max' => 128],
            [['auth_key'], 'string', 'max' => 255],
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

    public function getId()
    {
        return $this->id;
    }

    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    /**
     * @param $username string login name
     * @return null|Employee
     */
    public static function findByUsername($username)
    {
        return self::findOne(['login' => $username]);
    }

    /**
     * Validates password
     * @param $password string|null
     * @return bool
     */
    public function validatePassword($password)
    {
        return self::hashPassword($password) === $this->pwd_hash;
    }

    public function setPassword($password)
    {
        $this->pwd_hash = self::hashPassword($password);
    }

    public static function hashPassword($password)
    {
        if (empty($password)) {
            return null;
        } else {
            return sha1('kjndvlkjadnvadv' . $password);
        }
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = Yii::$app->security->generateRandomString();
            }
            return true;
        } else {
            return false;
        }
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public function isEmptyPassword()
    {
        return empty($this->pwd_hash);
    }

    public function isAdmin()
    {
        return $this->is_admin == 1;
    }
}
