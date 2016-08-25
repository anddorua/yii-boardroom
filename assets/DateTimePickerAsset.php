<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 14.06.16
 * Time: 22:31
 */

namespace app\assets;


use yii\web\AssetBundle;

class DateTimePickerAsset extends AssetBundle
{
    public $sourcePath = '@bower/eonasdan-bootstrap-datetimepicker/build';
    public $css = [
        'css/bootstrap-datetimepicker.min.css',
    ];
    public $js = [
        'js/bootstrap-datetimepicker.min.js',
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'yii\web\JqueryAsset',
        'app\assets\MomentAsset',
    ];
}