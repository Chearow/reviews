<?php

/** @var yii\web\View $this */

/** @var yii\bootstrap5\ActiveForm $form */

/** @var PasswordResetRequestForm $model */

use frontend\models\PasswordResetRequestForm;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Запрос на сброс пароля';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-request-password-reset">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Введите Ваш Email. На него будет отправлено письмо для сброса пароля.</p>

    <div class="row">
        <div class="col-lg-5">
            <?php
            $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

            <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

            <div class="mb-3">
                <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
            </div>

            <?php
            ActiveForm::end(); ?>
        </div>
    </div>
</div>
