<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 12.06.16
 * Time: 22:41
 */

namespace app\components;

use \yii\base\Component;
use \app\models\Room;

class CurrentRoom extends Component
{
    const SESSION_ROOM = 'room';

    public static function getId()
    {
        $result = null;
        if (!\Yii::$app->session->has(self::SESSION_ROOM)) {
            $room = Room::getDefaultRoom();
            self::setId($room->id);
            return $room->id;
        } else {
            return \Yii::$app->session->get(self::SESSION_ROOM);
        }
    }

    public static function setId($id)
    {
        \Yii::$app->session->set(self::SESSION_ROOM, $id);
    }

}