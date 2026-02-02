<?php
/** @var \common\models\City $city */
?>

<h3>Ваш город — <?= $city->name ?>?</h3>
<a href="<?= \yii\helpers\Url::to(['site/set-city', 'id' => $city->id]) ?>" class="btn btn-success">Да</a>
<a href="<?= \yii\helpers\Url::to(['site/index', 'forceList' => 1]) ?>" class="btn btn-danger">Нет</a>