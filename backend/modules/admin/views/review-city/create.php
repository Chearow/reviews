<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\ReviewCity $model */

$this->title = 'Create Review City';
$this->params['breadcrumbs'][] = ['label' => 'Review Cities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="review-city-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
