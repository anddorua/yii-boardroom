<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 18.08.16
 * Time: 23:40
 */

namespace app\assets;


use yii\web\AssetBundle;

class AppChangeControllerAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/AppChangeController.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}