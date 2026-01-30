<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\User $user */

$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->email_confirm_token]);
?>
<div class="verify-email">
    <p>Здравствуйте <?= Html::encode($user->fio) ?>,</p>

    <p>Для подтверждения регистрации перейдите по ссылке:</p>

    <p><?= Html::a(Html::encode($verifyLink), $verifyLink) ?></p>
</div>
