<?php
/** @var City[] $cities */

use common\models\City;
use yii\helpers\Url;

?>


<h3> Выберите Ваш город</h3>
<ul>
    <?php
    foreach ($cities as $city): ?>
        <li>
            <a href="<?= Url::to(['site/set-city', 'id' => $city->id]) ?>">
                <?= $city->name ?>
            </a>
        </li>
    <?php
    endforeach; ?>
</ul>