<?php

use common\models\ReviewCity;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\search\ReviewCitySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Review Cities';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="review-city-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Review City', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php
    // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'review_id',
            'city_id',
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, ReviewCity $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'review_id' => $model->review_id, 'city_id' => $model->city_id]);
                }
            ],
        ],
    ]); ?>


</div>
