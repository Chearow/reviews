<?php

namespace common\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $email;
    public $password;
    public $rememberMe = true;

    private $_user;

    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            ['email', 'email'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => 'Email',
            'password' => 'Пароль',
            'rememberMe' => 'Запомнить меня',
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            return;
        }
        $user = $this->getUser();
        if (!$user || !$user->validatePassword($this->password)) {
            $this->addError($attribute, 'Неверный email или пароль');
            return;
        }
        if (!$user->is_email_confirmed) {
            $this->addError($attribute, 'Email не подтверждён. Проверьте почту.');
            return;
        }
    }

    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }

        return false;
    }

    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByEmail($this->email);
        }

        return $this->_user;
    }
}
