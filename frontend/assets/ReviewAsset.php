<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class ReviewAsset extends AssetBundle
{
    public $sourcePath = '@frontend/assets/review';
    public $js = ['create.js'];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}