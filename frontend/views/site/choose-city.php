<?php
/** @var City $city */

use common\models\City;
use yii\helpers\Url;

?>

<h3>Ваш город — <?= $city->name ?>?</h3>
<a href="<?= Url::to(['site/set-city', 'id' => $city->id]) ?>" class="btn btn-success">Да</a>
<a href="<?= Url::to(['site/index', 'forceList' => 1]) ?>" class="btn btn-danger">Нет</a>