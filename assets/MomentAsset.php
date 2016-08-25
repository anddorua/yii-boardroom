<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 14.06.16
 * Time: 23:01
 */

namespace app\assets;


use yii\web\AssetBundle;

class MomentAsset extends AssetBundle
{
    public $sourcePath = '@bower/moment/min';
    public $css = [
    ];
    public $js = [
        'moment.min.js',
    ];
}