<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\ReviewCity $model */

$this->title = $model->review_id;
$this->params['breadcrumbs'][] = ['label' => 'Review Cities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="review-city-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'review_id' => $model->review_id, 'city_id' => $model->city_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'review_id' => $model->review_id, 'city_id' => $model->city_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'review_id',
            'city_id',
        ],
    ]) ?>

</div>
