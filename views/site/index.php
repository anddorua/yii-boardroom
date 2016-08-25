<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\BootstrapPluginAsset;
use app\assets\DateTimePickerAsset;
use app\assets\AppChangeControllerAsset;
use yii\helpers\ArrayHelper;
use app\models\Employee;
/**
 * @var $tpl_browse_first_day integer the first day of week in calendar
 * @var $tpl_browse_calendar array of array of \app\models\Appointment
 * @var $tpl_browse_hour_mode integer hour mode of showing time
 * @var Yii::$app->currentPeriod \app\components\CurrentPeriod shows current period
 * @var $room \app\models\Room
 * @var $successfulBooking array
 * */

$this->title = 'Boardroom Application';
$day_names = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
BootstrapPluginAsset::register($this);
DateTimePickerAsset::register($this);
AppChangeControllerAsset::register($this);
$timeFormat = $tpl_browse_hour_mode == app\models\Employee::MODE_HOUR_12 ? 'LT' : 'HH:mm';
?>
<script type="text/javascript">
    var bookViewUrlTemplate = "<?= Url::to(['appointment/book-info', 'appId' => 'placeholder'])?>";
    var allowedOptionsUrlTemplate = "<?= Url::to(['app-rest/allowed-options', 'appId' => 'placeholder'])?>";
    var submitUrlTemplate = "<?= Url::to(['appointment/modify'])?>";
</script>

<!-- Modal -->
<div class="modal fade" id="appChangeModal" tabindex="-1" role="dialog" aria-labelledby="appChangeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="appChangeModalLabel">B.B. Details</h4>
            </div>
            <div class="modal-body">
                <?php $form = \yii\bootstrap\ActiveForm::begin([
                    'id' => 'app-info-form',
                    'options' => [
                        'class' => ["form-horizontal"],
                    ],
                    'action' => '',
                ]); ?>
                <?php $model = new \app\models\BookingForm(); ?>
                <?= $form->field($model, 'appId', ['template' => '{input}'])->hiddenInput() ?>
                <?= $form->errorSummary([$model])?>

                <div class="form-group">
                    <label class="control-label col-md-2">When</label>
                    <?= $form->field($model, 'timeBegin', [
                        'inputTemplate' => '<div class="input-group date" id="bookingform-timebegin-wrapper">{input}<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span></div>',
                        'options' => ['class' => 'col-md-4'],
                        'enableLabel' => false,
                    ]) ?>
                    <?php
                    $this->registerJs('jQuery(function () {$(\'#bookingform-timebegin-wrapper\').datetimepicker({format:\'' . $timeFormat . '\'});});', \yii\web\View::POS_READY);
                    ?>
                    <div class="col-md-1">&#8212;</div>
                    <?= $form->field($model, 'timeEnd', [
                        'inputTemplate' => '<div class="input-group date" id="bookingform-timeend-wrapper">{input}<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span></div>',
                        'options' => ['class' => 'col-md-4'],
                        'enableLabel' => false
                    ]) ?>
                    <?php
                    $this->registerJs('jQuery(function () {$(\'#bookingform-timeend-wrapper\').datetimepicker({format:\'' . $timeFormat . '\'});});', \yii\web\View::POS_READY);
                    ?>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">Notes</label>
                    <?= $form->field($model, 'comment', ['options' => ['class' => 'col-md-9'], 'enableLabel' => false])->textarea([['wrap' => 'soft', 'rows' => '3', 'class' => ['form-control']]])?>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">Who</label>
                    <?= $form->field($model, 'employeeCode',[
                        'options' => ['class' => 'col-md-9'],
                        'enableLabel' => false,
                    ])->dropDownList(
                        ArrayHelper::map(Employee::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                        [
                            'class' => ['form-control'],
                        ]
                    )?>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">Submitted</label>
                    <div class="col-md-9">
                        <p id="app-submitted" class="form-control-static">####-##-## ##:##:##</p>
                    </div>
                </div>
                <div class="form-group" id="applytoall-wrapper">
                    <?= $form->field($model, 'applyToAll', ['options' => ['class' => 'col-md-9 col-md-offset-2']])->checkbox()->label('Apply to all occurencies?');?>
                </div>

                <?php \yii\bootstrap\ActiveForm::end();?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save-button">Save changes</button>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs('jQuery(function () { var appFormCtrl = new AppChangeController("appChangeModal", "app-info-form", "save-button", "nc-app-item", bookViewUrlTemplate, allowedOptionsUrlTemplate, submitUrlTemplate);  });', \yii\web\View::POS_READY);
?>
<?php
    $alertContent = '';
    $alertClass = ['alert', 'alert-success'];
    if (!is_null($successfulBooking)) {
        $booking = new \app\models\BookingForm($successfulBooking);
        $timeToShow =
            \app\utility\DateHelper::FormatTimeAccordingRule($booking->getTimeBeginAsDateTime(), $tpl_browse_hour_mode)
            . ' - '
            . \app\utility\DateHelper::FormatTimeAccordingRule($booking->getTimeEndAsDateTime(), $tpl_browse_hour_mode);
        $bookNote = Html::encode($booking->comment);
        $alertContent = "The event <strong>$timeToShow</strong> has been added. The text for this event is: <strong>$bookNote</strong>";
    } else {
        $alertClass[] = 'hidden';
    }
    echo Html::tag('div', $alertContent, ['class' => $alertClass]);
?>
<div class="month-pager  clearfix">
    <nav>
        <?=
        Html::ul([
            Html::a(Html::tag('span', '&laquo;', ['aria-hidden' => true]), Url::to(['site/period', 'month' => 'prev']), ['aria-label' => 'Previous month']),
            Html::tag('span', Yii::$app->formatter->asDate(Yii::$app->currentPeriod->getCurrent(), 'MMMM y')),
            Html::a(Html::tag('span', '&raquo;', ['aria-hidden' => true]), Url::to(['site/period', 'month' => 'next']), ['aria-label' => 'Next month']),
        ], ['class' => 'pagination', 'encode' => false]);
        ?>
    </nav>
    <div class="book-control pull-right">
        <?= Html::a('Book ' . $room->room_name . ' &hellip;', Url::to(['appointment/book']), ['class' => ['btn', 'btn-primary']])?>
    </div>
</div>
<div class="new-calendar">
    <div class="nc-row">
        <?php
        for ($i = 0; $i < 7; $i++) {
            $day_index = $i;
            if ($tpl_browse_first_day == \app\models\Employee::FIRST_DAY_MONDAY) {
                $day_index++;
            }
            if ($day_index == 7) {
                $day_index = 0;
            }
            echo Html::tag('div', $day_names[$day_index], ['class' => ['nc-head']]);
        }
        ?>
    </div>
    <?php
    // 1) looking the date of first table cell
    $period_first_date = \app\utility\DateHelper::GetFirstDateInMonth(Yii::$app->currentPeriod->getCurrent());
    $period_last_date = \app\utility\DateHelper::GetLastDateInMonth(Yii::$app->currentPeriod->getCurrent());
    $first_day_dow = $period_first_date->format('N');
    if ($tpl_browse_first_day == \app\models\Employee::FIRST_DAY_MONDAY) {
        $days_back = $first_day_dow - 1;
    } else {
        $days_back = $first_day_dow == 7 ? 0 : $first_day_dow;
    }
    $first_cell_date = $period_first_date->sub(new DateInterval('P' . $days_back . 'D'));
    $cell_date = $first_cell_date;
    $cell_in_row = 0;
    $one_day = new \DateInterval('P1D');
    while ($cell_date->diff($period_last_date)->format('%R%a') >= 0 || $cell_in_row != 7) {
        $cell_in_row = $cell_in_row == 7 ? 0 : $cell_in_row;
        if ($cell_in_row == 0) {
            echo "<div class=\"nc-row\">"; // make new row
        }
        echo "<div class='nc-cell'>"; // make new cell
        if (\app\utility\DateHelper::IsDateInSamePeriod(Yii::$app->currentPeriod->getCurrent(), $cell_date)) {
            $day_index = $cell_date->format('j');
            echo Html::tag('div', $day_index, ['class' => ['day-badge']]);
            if (isset($tpl_browse_calendar[$day_index])) {
                foreach($tpl_browse_calendar[$day_index] as $appItem) {
                    /* @var $appItem \app\models\Appointment */
                    $timeInterval = \app\utility\DateHelper::FormatTimeAccordingRule($appItem->getTimeStart(), $tpl_browse_hour_mode)
                        . " - "
                        . \app\utility\DateHelper::FormatTimeAccordingRule($appItem->getTimeEnd(), $tpl_browse_hour_mode);
                    $timeIntervalTag = Html::tag('div', $timeInterval, ['class' => ['nc-app-item'], 'data' => ['app-id' => $appItem->id]]);
                    echo $timeIntervalTag;
                }
            }
        }
        echo "</div>"; // finish cell
        if ($cell_in_row == 6) {
            echo "</div>"; // finish row
        }
        $cell_in_row++;
        $cell_date->add($one_day);
    }
    ?>
</div>
