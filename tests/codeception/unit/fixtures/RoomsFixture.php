<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 10.07.16
 * Time: 23:33
 */

namespace app\tests\codeception\unit\fixtures;

use yii\test\ActiveFixture;

class RoomsFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Room';
    public $dataFile = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'room.php';
}