<?php
/** @var \common\models\City[] $cities */
?>


<h3> Выберите Ваш город</h3>
<ul>
    <?php foreach ($cities as $city): ?>
        <li>
            <a href="<?= \yii\helpers\Url::to(['site/set-city', 'id' => $city->id]) ?>">
                <?= $city->name ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>