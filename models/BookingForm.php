<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 13.06.16
 * Time: 22:50
 */

namespace app\models;


use app\utility\DateHelper;
use yii\base\Model;
use Yii;
/**
 * Class BookingForm
 * @package app\models
 * @property integer $employeeCode
 * @property string $date
 * @property string $timeBegin
 * @property string $timeEnd
 * @property string $comment
 * @property integer $recurring
 * @property integer $repeatInterval
 * @property integer $duration
 * @property integer $timeFormat see Employee MODE_HOUR
 * @property integer $appId appointment id, used in "modify" scenario
 * @property integer $applyToAll used when browsing existing appointment
 * @property string $submitted timestamp when appointment was submitted
 */
class BookingForm extends Model
{
    const RECURRING_NO = 1;
    const RECURRING_YES = 2;

    const NOT_RECURRING = 0;
    const WEEKLY = 1;
    const BIWEEKLY = 2;
    const MONTHLY = 3;

    const SCENARIO_MODIFY = 'modify';

    public $employeeCode;
    public $date;
    public $timeBegin;
    public $timeEnd;
    public $comment;
    public $recurring;
    public $repeatInterval;
    public $duration;
    public $timeFormat;
    public $appId;
    public $applyToAll;
    public $submitted;

    public function init()
    {
        if (!isset($this->timeFormat)) {
            $this->timeFormat = Employee::MODE_HOUR_12;
        }
        if (!isset($this->recurring)) {
            $this->recurring = self::RECURRING_NO;
        }
        if (!isset($this->repeatInterval)) {
            $this->repeatInterval = self::NOT_RECURRING;
        }
        parent::init();
    }

    public function afterValidate()
    {
        if (!$this->hasErrors()) {
            if ($this->isRecurring()) {
                if ($this->repeatInterval == self::WEEKLY) {
                    $this->duration = min($this->duration, 4);
                }
                if ($this->repeatInterval == self::BIWEEKLY) {
                    $this->duration = min($this->duration, 4);
                    $this->duration = ((integer)$this->duration / 2) * 2;
                }
            }
        }
        parent::afterValidate();
    }

    public function isRecurring()
    {
        return $this->recurring == self::RECURRING_YES;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'employeeCode' => 'Booked for',
            'date' => 'I would like to book this meeting',
            'timeBegin' => 'Beginning time',
            'timeEnd' => 'Ending time',
            'comment' => 'Specifics for the meeting',
            'recurring' => 'Is this going to be a recurring event?',
            'repeatInterval' => 'Recurring interval',
            'duration' => 'Duration (max 4 weeks)'
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['employeeCode', 'date', 'timeBegin', 'timeEnd', 'comment', 'recurring', 'repeatInterval', 'duration', 'employeeCode'],
            self::SCENARIO_MODIFY => ['employeeCode', 'date', 'timeBegin', 'timeEnd', 'comment', 'appId', 'applyToAll'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['employeeCode', 'date', 'timeBegin', 'timeEnd', 'comment', 'recurring'], 'required', 'on' => [self::SCENARIO_DEFAULT]],
            [['employeeCode', 'date', 'timeBegin', 'timeEnd', 'comment', 'appId', 'applyToAll'], 'required', 'on' => [self::SCENARIO_MODIFY]],
            [['recurring', 'repeatInterval', 'duration', 'employeeCode', 'appId', 'applyToAll'], 'integer'],
            ['comment', 'string', 'max' => 1024],
            ['recurring', 'default', 'value' => 1],
            [['repeatInterval', 'duration'], 'required', 'on' => [self::SCENARIO_DEFAULT], 'when' => function($model){
                return $model->recurring == 2;
            }],
            [['timeEnd'], 'validateTime', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_MODIFY]],
        ];
    }

    public function validateTime($attribute)
    {
        $tb = $this->getTimeBeginAsDateTime();
        $te = $this->getTimeEndAsDateTime();
        if (!(is_object($tb) && is_object($te))) {
            $this->addError($attribute, "Can`t validate time.");
            return;
        }
        if ($tb->getTimestamp() >= $te->getTimestamp()) {
            $this->addError($attribute, "End time should be greater then begin time.");
        }
    }

    private function getTimeConvertPattern()
    {
        return $this->timeFormat == Employee::MODE_HOUR_12 ? 'Y-m-d h:i a' : 'Y-m-d H:i';
    }

    public function getTimeBeginAsDateTime()
    {
        return \DateTime::createFromFormat($this->getTimeConvertPattern(), $this->date . ' ' . $this->timeBegin);
    }

    public function getTimeEndAsDateTime()
    {
        return \DateTime::createFromFormat($this->getTimeConvertPattern(), $this->date . ' ' . $this->timeEnd);
    }

    public function fillFromAppointment(Appointment $app)
    {
        $this->employeeCode = $app->emp_id;
        $this->date = $app->getTimeStart()->format('Y-m-d');
        $this->timeBegin = DateHelper::FormatTimeAccordingRule($app->getTimeStart(), $this->timeFormat);
        $this->timeEnd = DateHelper::FormatTimeAccordingRule($app->getTimeEnd(), $this->timeFormat);
        $this->comment = $app->notes;
        $this->appId = $app->id;
        $this->submitted = $app->submitted;
    }

}