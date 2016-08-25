<?php

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use app\models\Employee;
use yii\helpers\ArrayHelper;
use app\assets\DateTimePickerAsset;
//todo make crossings message
/* @var $this yii\web\View */
/* @var $model app\models\BookingForm */
/* @var $form ActiveForm */
/* @var $room app\models\Room */
/* @var $hourMode integer */
/* @var $firstDay integer */
/* @var $crossings array of Appointment */
DateTimePickerAsset::register($this);
?>
<?php
$alertContent = '';
$alertClass = ['alert', 'alert-danger'];
if (isset($crossings)) {
    $crossingsPresentation = array_map(function($crossing) use ($hourMode){
        /* @var $crossing \app\models\Appointment */
        $presentation = Html::encode(Employee::findOne($crossing->emp_id)->name);
        $presentation .= ' ' . $crossing->getTimeStart()->format('Y-m-d');
        $presentation .= ' ' . \app\utility\DateHelper::FormatTimeAccordingRule($crossing->getTimeStart(), $hourMode);
        $presentation .= ' - ';
        $presentation .= ' ' . \app\utility\DateHelper::FormatTimeAccordingRule($crossing->getTimeStart(), $hourMode);
        return $presentation;
    }, $crossings);
    $alertContent = "Can't add appointment, it crosses existing appointments: " . implode(', ', $crossingsPresentation);
} else {
    $alertClass[] = 'hidden';
}
echo Html::tag('div', $alertContent, ['class' => $alertClass]);
?>

<div class="appointment-book">

    <?php $form = ActiveForm::begin(); ?>

    <fieldset>
        <legend><?= $room->room_name ?> booking</legend>

        <div class="row">
            <?= $form->field($model, 'employeeCode',[
                'options' => ['class' => 'col-md-12'],
            ])->dropDownList(
                ArrayHelper::map(Employee::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                [
                    'class' => ['form-control'],
                ]
            )?>
        </div>

        <div class="row">
            <?= $form->field($model, 'date', [
                'inputTemplate' => '<div class="input-group date" id="bookingform-date-wrapper">{input}<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span></div>',
                'options' => ['class' => 'col-md-4'],
            ])?>
            <?php
            $dow = $firstDay == Employee::FIRST_DAY_SUNDAY ? 0 : 1;
            $this->registerJs('jQuery(function () {$(\'#bookingform-date-wrapper\').datetimepicker({format:\'Y-MM-DD\'}); moment.updateLocale($(\'#bookingform-date-wrapper\').data("DateTimePicker").locale(), { week : {dow : ' . $dow . '} }); });', \yii\web\View::POS_READY);
            ?>
        </div>

        <div class="row">
            <?= $form->field($model, 'timeBegin', [
                'inputTemplate' => '<div class="input-group date" id="bookingform-timebegin-wrapper">{input}<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span></div>',
                'options' => ['class' => 'col-md-2'],
            ]) ?>
            <?php
            $timeFormat = $hourMode == app\models\Employee::MODE_HOUR_12 ? 'LT' : 'HH:mm';
            //Yii::trace('!!hour mode ' . $hourMode);
            $this->registerJs('jQuery(function () {$(\'#bookingform-timebegin-wrapper\').datetimepicker({format:\'' . $timeFormat . '\'});});', \yii\web\View::POS_READY);
            ?>
            <?= $form->field($model, 'timeEnd', [
                'inputTemplate' => '<div class="input-group date" id="bookingform-timeend-wrapper">{input}<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span></div>',
                'options' => ['class' => 'col-md-2'],
            ]) ?>
            <?php
            $this->registerJs('jQuery(function () {$(\'#bookingform-timeend-wrapper\').datetimepicker({format:\'' . $timeFormat . '\'});});', \yii\web\View::POS_READY);
            ?>
        </div>

        <div class="row">
            <?= $form->field($model, 'comment', ['options' => ['class' => 'col-md-12']])->textarea([['wrap' => 'soft', 'rows' => '3', 'class' => ['form-control']]])?>
        </div>
        <div class="row">
            <?= $form->field($model, 'recurring', ['options' => ['class' => 'col-md-12']])->radioList(['1' => 'no', '2' => 'yes'])?>
        </div>
        <div class="row">
            <?= $form->field($model, 'repeatInterval', [
                'options' => ['class' => 'col-md-12'],
                'enableClientValidation' => false,
            ])->radioList(['1' => 'weekly', '2' => 'bi-weekly', '3' => 'monthly'])?>
        </div>

        <?= $form->field($model, 'duration', [
            'inputTemplate' => '<div class="row"><div class="col-md-2">{input}</div></div><div class="row"><div class="col-md-12"><span class="help-block">If weekly or bi-weekly, specify the number of weeks for it to keep recurring. If monthly, specify the number of months. (If you choose "bi-weekly" and put in an odd number of weeks, the computer will round down.)</span></div></div>',
            'inputOptions' => ['min' => '0'],
            'enableClientValidation' => false,
        ])->input('number')?>

        <hr>
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    </fieldset>

    <?php ActiveForm::end(); ?>

</div><!-- appointment-book -->
