<?php


namespace eagle\assets;


class JuiAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@yii/jui/assets';
    public $js = [
        'jquery.ui.core.js',
        'jquery.ui.widget.js',
        'jquery.ui.position.js',
        'jquery.ui.mouse.js',
        'jquery.ui.sortable.js',
        'jquery.ui.effect-all.js',
        'jquery.ui.datepicker.js',
        'jquery.ui.datepicker-i18n.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\jui\ThemeAsset',
    ];
}