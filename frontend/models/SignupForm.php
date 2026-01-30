<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $fio;
    public $email;
    public $phone;
    public $password;
    public $password_repeat;
    public $verifyCode;

    public function rules()
    {
        return [
            [['fio', 'email', 'phone', 'password', 'password_repeat', 'verifyCode'], 'required'],
            ['fio', 'string', 'max' => 255],
            ['phone', 'string', 'max' => 50],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'Этот email уже зарегистрирован'],
            ['password', 'string', 'min' => 6],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => 'Пароли должны совпадать.'],
            ['verifyCode', 'captcha', 'captchaAction' => '/site/captcha'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'fio' => 'ФИО',
            'email' => 'Email',
            'phone' => 'Номер телефона',
            'password' => 'Пароль',
            'password_repeat' => 'Повтор пароля',
            'verifyCode' => 'Код с картинки'
        ];
    }


    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->fio = $this->fio;
        $user->email = $this->email;
        $user->phone = $this->phone;
        $user->password = $this->password;
        $user->generateEmailConfirmToken();

        if ($user->save()) {
            $this->sendEmail($user);
            return true;
        }
        return false;
    }

    protected function sendEmail($user)
    {
        return Yii::$app->mailer
            ->compose('emailVerify-html', ['user' => $user])
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
}
