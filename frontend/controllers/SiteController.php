<?php

namespace frontend\controllers;

use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;

/**
 * Site controller
 */
class SiteController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
            'captcha' => [
                'class' => \yii\captcha\CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->request->get('forceList')) {
            $cities = \common\models\City::find()->orderBy(['name' => SORT_ASC])->all();
            return $this->render('choose-city-list', ['cities' => $cities]);
        }
        $session = Yii::$app->session;
        if($session->has('city_id')) {
            $cityId = $session->get('city_id');
            return $this->render('index', [
                'cityId' => $cityId,
            ]);
        }
        $ipCity = $this->detectCityByIP();
        if ($ipCity) {
            return $this->render('choose-city', [
                'city' => $ipCity,
            ]);
        }
        $cities = \common\models\City::find()->orderBy(['name' => SORT_ASC])->all();
        return $this->render('choose-city-list', [
            'cities' => $cities,
        ]);
    }

    public function actionSetCity($id)
    {
        $city = \common\models\City::findOne($id);
        if ($city) {
            Yii::$app->session->set('city_id', $city->id);
            return $this->redirect(['site/index']);
        }

        throw new \yii\web\NotFoundHttpException('Город не найден');
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Благодарим за обращение. Мы ответим Вам в ближайшее время.');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось отправить сообщение. Попробуйте позже.');
            }

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Регистрация почти завершена. Для продолжения регистрации проверьте Вашу почту и подтвердите email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Инструкция по восстановлению пароля отправлена на Вашу почту.');

                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'Не удалось отправить письмо для восстановления пароля.');
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Новый пароль успешно сохранён.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($model->verifyEmail()) {
            Yii::$app->session->setFlash('success', 'Ваш email успешно подтверждён!');
            return $this->goHome();
        }

        Yii::$app->session->setFlash('error', 'Не удалось подтвердить email. Возможно, ссылка устарела.');
        return $this->goHome();
    }

    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Письмо с подтверждением отправлено повторно. Проверьте Вашу почту.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Не удалось отправить письмо повторно. Проверьте правильность email.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }

    private function detectCityByIP()
    {
        $ip = Yii::$app->request->userIP;
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return null;
        }

        $url = "http://ip-api.com/json/{$ip}?lang=ru";

        $response = @file_get_contents($url);
        if (!$response) {
            return null;
        }

        $data = json_decode($response, true);
        if (!isset($data['status']) || $data['status'] !== 'success') {
            return null;
        }

        $cityName = $data['city'] ?? null;
        if (!$cityName) {
            return null;
        }

        $city = \common\models\City::findOne(['name' => $cityName]);
        if ($city) {
            return $city;
        }

        $city = new \common\models\City();
        $city->name = $cityName;
        $city->created_at = time();
        $city->save(false);

        return $city;
    }
}
