<?php

namespace frontend\models;

use common\models\User;
use Yii;
use yii\base\Model;

class ResendVerificationEmailForm extends Model
{
    /**
     * @var string
     */
    public $email;

    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            [
                'email',
                'exist',
                'targetClass' => User::class,
                'filter' => ['is_email_confirmed' => false],
                'message' => 'Пользователь с таким email не найден.'
            ],
        ];
    }

    public function sendEmail()
    {
        $user = User::findOne([
            'email' => $this->email,
            'is_email_confirmed' => false,
        ]);

        if (!$user) {
            return false;
        }

        $user->generateEmailConfirmToken();
        $user->save(false);

        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
}
