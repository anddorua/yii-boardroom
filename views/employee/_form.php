<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Employee;

/* @var $this yii\web\View */
/* @var $model app\models\Employee */
/* @var $passChange app\models\PasswordChange */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employee-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'login')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_admin')->checkbox() ?>

    <?= $form->field($model, 'hour_mode')->radioList([
            12 => 'AM/PM',
            24 => '24 hrs',
    ]) ?>

    <?= $form->field($model, 'first_day')->radioList([
        Employee::FIRST_DAY_SUNDAY => 'Sunday',
        Employee::FIRST_DAY_MONDAY => 'Monday',
    ]) ?>

    <?= $form->field($passChange, 'oldPassword')->textInput() ?>
    <?= $form->field($passChange, 'newPassword1')->textInput() ?>
    <?= $form->field($passChange, 'newPassword2')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
