<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rooms".
 *
 * @property integer $id
 * @property string $room_name
 */
class Room extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rooms';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['room_name'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'room_name' => 'Room Name',
        ];
    }

    /**
     * @return Room
     */
    public static function getDefaultRoom()
    {
        return self::find()->orderBy(['room_name' => SORT_ASC])->limit(1)->one();
    }
}
