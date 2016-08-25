<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 02.06.16
 * Time: 0:11
 */

namespace app\assets;

use  \yii\web\AssetBundle;

class BoardroomAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/boardroom.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}