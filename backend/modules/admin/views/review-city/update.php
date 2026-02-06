<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\ReviewCity $model */

$this->title = 'Update Review City: ' . $model->review_id;
$this->params['breadcrumbs'][] = ['label' => 'Review Cities', 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => $model->review_id,
    'url' => ['view', 'review_id' => $model->review_id, 'city_id' => $model->city_id]
];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="review-city-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
