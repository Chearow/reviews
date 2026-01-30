<?php

namespace frontend\models;

use yii\base\InvalidArgumentException;
use yii\base\Model;
use Yii;
use common\models\User;

/**
 * Password reset form
 */
class ResetPasswordForm extends Model
{
    public $password;

    /**
     * @var \common\models\User
     */
    private $_user;

    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidArgumentException('Код сброса пароля не может быть пустым.');
        }
        $this->_user = User::findByPasswordResetToken($token);
        if (!$this->_user) {
            throw new InvalidArgumentException('Неверный код сброса пароля.');
        }
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
        ];
    }

    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();
        $user->generateAuthKey();

        return $user->save(false);
    }
}
