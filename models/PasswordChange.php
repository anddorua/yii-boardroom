<?php
/**
 * Created by PhpStorm.
 * User: aahom_000
 * Date: 07.05.2016
 * Time: 18:31
 */

namespace app\models;

use \yii\base\Model;

/**
 * Class PasswordChange
 * accepts user input for password change purposes
 * @package app\models
 *
 * @property string|null $oldPassword
 * @property string|null $newPassword1
 * @property string|null $newPassword2
 */
class PasswordChange extends Model
{
    const NO_PASSWORD = 'no_pass';
    const HAS_PASSWORD = 'has_pass';

    public $oldPassword;
    public $newPassword1;
    public $newPassword2;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['oldPassword', 'newPassword1', 'newPassword2'], 'string'],
            [['oldPassword', 'newPassword1', 'newPassword2'], 'default', 'value' => null],
            [['newPassword1', 'newPassword2'], 'required', 'on' => self::NO_PASSWORD],
            ['newPassword1', 'required', 'when' => function($model){
                return !empty($model->oldPassword) || !empty($model->newPassword2);
            }],
            ['newPassword2', 'required', 'when' => function($model){
                return !empty($model->oldPassword) || !empty($model->newPassword1);
            }],
            ['oldPassword', 'required', 'on' => self::HAS_PASSWORD],
        ];
    }

    public function scenarios()
    {
        return [
            self::NO_PASSWORD => ['oldPassword', 'newPassword1', 'newPassword2'],
            self::HAS_PASSWORD => ['oldPassword', 'newPassword1', 'newPassword2'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'oldPassword' => 'Actual password',
            'newPassword1' => 'New password',
            'newPassword2' => 'Retype password',
        ];
    }

    /**
     *
     */
    public function hasNewPassword()
    {
        return !empty($this->newPassword1) || !empty($this->newPassword2);
    }

}