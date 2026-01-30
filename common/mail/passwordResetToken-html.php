<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\User $user */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
<div class="password-reset">
    <p>Здравствуйте <?= Html::encode($user->fio) ?>,</p>

    <p>Для сброса пароля перейдите по ссылке:</p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>
