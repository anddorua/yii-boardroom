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
 * @property Employee $employee
 * @property string|null $oldPassword
 * @property string|null $newPassword1
 * @property string|null $newPassword2
 */
class PasswordChange extends Model
{
    const SCENARIO_NO_PASSWORD = 'no_pass';
    const SCENARIO_HAS_PASSWORD = 'has_pass';
    const SCENARIO_ALIEN_PROFILE = 'alien_profile';

    public $employee;
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
            [['newPassword1', 'newPassword2'], 'required', 'on' => self::SCENARIO_NO_PASSWORD],
            ['newPassword1', 'required', 'when' => function($model){
                return !is_null($model->oldPassword);
            }, /*'whenClient' => "function(attribute, value){ return $('#oldPassword').val()<>''; }"*/ 'enableClientValidation' => false],
            ['newPassword2', 'required', 'when' => function($model){
                return !is_null($model->oldPassword);
            }, 'enableClientValidation' => false],
            ['oldPassword', 'required', 'when' => function(){
                return !is_null($this->newPassword1) || !is_null($this->newPassword2);
            }, 'on' => self::SCENARIO_HAS_PASSWORD, 'enableClientValidation' => false],
            ['oldPassword', function(){
                if (!is_null($this->oldPassword)) {
                    $this->addError('oldPassword', 'Wrong actual password');
                }
            }, 'on' => self::SCENARIO_NO_PASSWORD],
            ['oldPassword', function(){
                if (!$this->employee->validatePassword($this->oldPassword)) {
                    $this->addError('oldPassword', 'Actual password incorrect');
                }
            }, 'when' => function(){
                return !is_null($this->newPassword1) || !is_null($this->newPassword2);
            }, 'on' => self::SCENARIO_HAS_PASSWORD, 'enableClientValidation' => false],
            ['newPassword2', function(){
                if ($this->newPassword1 != $this->newPassword2) {
                    $this->addError('newPassword1', 'Retyped password incorrect');
                }
            }],
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_NO_PASSWORD => ['oldPassword', 'newPassword1', 'newPassword2'],
            self::SCENARIO_HAS_PASSWORD => ['oldPassword', 'newPassword1', 'newPassword2'],
            self::SCENARIO_ALIEN_PROFILE => ['!oldPassword', '!newPassword1', '!newPassword2'],
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

    /**
     * @param Employee $employee
     * @return boolean
     */
    public function setEmployee($employee)
    {
        $this->employee = $employee;
        return true;
    }

}