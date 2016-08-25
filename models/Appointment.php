<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\utility\DateHelper;

/**
 * This is the model class for table "appointments".
 *
 * @property integer $id
 * @property integer $emp_id
 * @property string $time_start
 * @property string $time_end
 * @property string $notes
 * @property integer $creator_id
 * @property integer $chain
 * @property integer $room_id
 * @property string $submitted
 *
 * @property Employee $employee
 * @property Employee $creator
 * @property Room $room
 */
class Appointment extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'appointments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['emp_id', 'time_start', 'time_end', 'notes', 'creator_id', 'chain', 'room_id'], 'required'],
            [['emp_id', 'creator_id', 'chain', 'room_id'], 'integer'],
            [['time_start', 'time_end', 'submitted'], 'safe'],
            [['notes'], 'string', 'max' => 1024],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'emp_id' => 'Emp ID',
            'time_start' => 'Time Start',
            'time_end' => 'Time End',
            'notes' => 'Notes',
            'creator_id' => 'Creator ID',
            'chain' => 'Chain',
            'room_id' => 'Room ID',
            'submitted' => 'Submitted',
        ];
    }

    public function getEmployee()
    {
        return $this->hasOne(Employee::class, ['id' => 'emp_id']);
    }

    public function getCreator()
    {
        return $this->hasOne(Employee::class, ['id' => 'creator_id']);
    }

    public function getRoom()
    {
        return $this->hasOne(Room::class, ['id' => 'room_id']);
    }

    /**
     * @return \DateTime
     */
    public function getTimeStart()
    {
        return \DateTime::createFromFormat('Y-m-d H:i:s', $this->time_start);
    }

    public function setTimeStart(\DateTime $value)
    {
        $this->time_start = $value->format('Y-m-d H:i:s');
    }

    /**
     * @return \DateTime
     */
    public function getTimeEnd()
    {
        return \DateTime::createFromFormat('Y-m-d H:i:s', $this->time_end);
    }

    public function setTimeEnd(\DateTime $value)
    {
        $this->time_end = $value->format('Y-m-d H:i:s');
    }

    /**
     * returns all appointments in specified date
     * @param $roomId
     * @param \DateTime $date
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getDayAppointments($roomId, \DateTime $date)
    {
        $ds = (new \DateTime())->setDate($date->format('Y'), $date->format('n'), $date->format('j'))->setTime(0,0,0);
        $de = DateHelper::GetNextDay($ds);
        $ds_str = $ds->format('Y-m-d H:i:s');
        $de_str = $de->format('Y-m-d H:i:s');
        return Appointment::find()
            ->where(':ds <= time_start', [':ds' => $ds_str])
            ->andWhere('time_start < :de', [':de' => $de_str])
            ->andWhere('room_id = :rid', [':rid' => $roomId])
            ->orderBy(['time_start' => SORT_ASC])
            ->all();
    }

    /**
     * returns all appointments in month specified date belongs to
     * @param $roomId
     * @param \DateTime $date
     * @return array|\yii\db\ActiveRecord[][]
     */
    public static function getMonthAppointments($roomId, \DateTime $date)
    {
        $ds = DateHelper::GetFirstDateInMonth($date);
        $current = clone $ds;
        $result = [];
        $day = 0;
        while (DateHelper::IsDateInSamePeriod($ds, $current)) {
            $result[++$day] = self::getDayAppointments($roomId, $current);
            $current = DateHelper::GetNextDay($current);
        }
        return $result;
    }

    public static function getMaxChainId()
    {
        $result = self::find()->max('chain');
        if (is_null($result)) {
            $result = 0;
        }
        return $result;
    }

    /**
     * Shows if object's period crossing with test period
     * right side not includes to period
     * @param \DateTime $testTimeStart
     * @param \DateTime $testTimeEnd
     * @return bool
     */
    public function isCrossing(\DateTime $testTimeStart, \DateTime $testTimeEnd)
    {
        $myStartStamp = $this->getTimeStart()->getTimestamp();
        $myEndStamp = $this->getTimeEnd()->getTimestamp();
        $testStartStamp = $testTimeStart->getTimestamp();
        $testEndStamp = $testTimeEnd->getTimestamp();
        return $testStartStamp < $myEndStamp && $myStartStamp < $testEndStamp;
    }

    /**
     * sets new time, leaving date the same
     * @param $timeStart \DateTime
     * @param $timeEnd \DateTime
     */
    public function setNewTime(\DateTime $timeStart, \DateTime $timeEnd)
    {
        $new_date = $this->getTimeStart();
        $new_date->setTime($timeStart->format('H'), $timeStart->format('i'));
        $this->setTimeStart($new_date);
        $new_date->setTime($timeEnd->format('H'), $timeEnd->format('i'));
        if ($this->getTimeStart()->getTimestamp() > $new_date->getTimestamp()) {
            $new_date->add(new \DateInterval('P1D'));
        }
        $this->setTimeEnd($new_date);
    }

}
