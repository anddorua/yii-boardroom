<?php

namespace app\models;

use Yii;

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
class Appointment extends \yii\db\ActiveRecord
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
}
