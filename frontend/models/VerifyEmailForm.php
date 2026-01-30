<?php

namespace frontend\models;

use common\models\User;
use yii\base\InvalidArgumentException;
use yii\base\Model;

class VerifyEmailForm extends Model
{
    /**
     * @var string
     */
    public $token;

    /**
     * @var User
     */
    private $_user;

    public function __construct($token, array $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidArgumentException('Токен подтверждения email не может быть пустым.');
        }
        $this->_user = User::findOne(['email_confirm_token' => $token]);
        if (!$this->_user) {
            throw new InvalidArgumentException('Неверный токен подтверждения email.');
        }
        $this->token = $token;
        parent::__construct($config);
    }

    public function verifyEmail()
    {
        $user = $this->_user;
        $user->confirmEmail();
        return $user->save(false) ? $user : null;
    }
}
