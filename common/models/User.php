<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property int $id
 * @property string $fio
 * @property string $email
 * @property string $phone
 * @property string $password_hash
 * @property string|null $email_confirm_token
 * @property bool $is_email_confirmed
 * @property int $created_at
 * @property string $auth_key
 */
class User extends ActiveRecord implements IdentityInterface
{
    public $password;
    public static function tableName()
    {
        return '{{%user}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
            ],
        ];
    }

    public function rules()
    {
        return [
            [['fio', 'email', 'phone'], 'required'],
            [['fio'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 50],
            [['email_confirm_token'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['email'], 'unique'],
            ['is_email_confirmed', 'boolean'],
            ['password', 'required', 'on' => 'create'],
            ['password', 'string', 'min' => 6],
        ];
    }

    public  function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->password) {
                $this->setPassword($this->password);
            }
            if ($insert) {
                $this->generateAuthKey();
            }
            return true;
        }
        return false;
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generateEmailConfirmToken()
    {
        $this->email_confirm_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function confirmEmail()
    {
        $this->is_email_confirmed = true;
        $this->email_confirm_token = null;
    }
}
