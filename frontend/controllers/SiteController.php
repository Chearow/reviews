<?php

namespace frontend\controllers;

use common\models\City;
use common\models\LoginForm;
use common\models\Review;
use common\models\User;
use frontend\models\ContactForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\captcha\CaptchaAction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use frontend\services\ApiService;
use frontend\repositories\CityRepository;

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
                'class' => ErrorAction::class,
            ],
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->request->url === '/site/index' || Yii::$app->request->url === '/index') {
            return $this->redirect(['/']);
        }

        if (Yii::$app->request->get('forceList')) {
            $cities = Yii::$app->db->cache(function () {
                return City::find()
                    ->orderBy(['name' => SORT_ASC])
                    ->all();
            }, 3600);
            return $this->render('choose-city-list', ['cities' => $cities]);
        }

        $session = Yii::$app->session;
        if ($session->has('city_id')) {
            $cityId = $session->get('city_id');
            $reviews = Review::find()
                ->joinWith('cities')
                ->where(['city.id' => $cityId])
                ->orWhere(['review.is_for_all' => 1])
                ->orderBy(['review.created_at' => SORT_DESC])
                ->all();
            return $this->render('index', ['reviews' => $reviews]);
        }

        $ipCity = $this->detectCityByIP();
        if ($ipCity) {
            return $this->render('choose-city', [
                'city' => $ipCity,
            ]);
        }

        $cities = Yii::$app->db->cache(function () {
            return City::find()
                ->orderBy(['name' => SORT_ASC])
                ->all();
        }, 3600);
        return $this->render('choose-city-list', ['cities' => $cities,]);
    }

    private function detectCityByIP(): ?City
    {
        $ip = Yii::$app->request->userIP;
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return null;
        }

        $cityName = $this->apiService->detectCityNameByIP($ip);
        if (!$cityName) {
            return null;
        }

        return $this->cityRepository->findByName($cityName)
            ?? $this->cityRepository->create($cityName);

    }

    public function actionSetCity($id)
    {
        $city = City::findOne($id);
        if ($city) {
            Yii::$app->session->set('city_id', $city->id);
            return $this->redirect(['site/index']);
        }

        throw new NotFoundHttpException('Город не найден');
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
            Yii::$app->session->setFlash(
                'success',
                'Регистрация почти завершена. Для продолжения регистрации проверьте Вашу почту и подтвердите email.'
            );
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
                Yii::$app->session->setFlash(
                    'success',
                    'Инструкция по восстановлению пароля отправлена на Вашу почту.'
                );

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
                Yii::$app->session->setFlash(
                    'success',
                    'Письмо с подтверждением отправлено повторно. Проверьте Вашу почту.'
                );
                return $this->goHome();
            }
            Yii::$app->session->setFlash(
                'error',
                'Не удалось отправить письмо повторно. Проверьте правильность email.'
            );
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }

    public function actionAuthorInfo($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $user = User::findOne($id);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Пользователь не найден',
            ];
        }

        return [
            'success' => true,
            'data' => [
                'fio' => $user->fio,
                'email' => $user->email,
                'phone' => $user->phone,
                'reviewsUrl' => Url::to(['author-reviews', 'id' => $user->id]),
            ],
        ];
    }

    public function actionAuthorReviews($id)
    {
        $user = User::findOne($id);
        if (!$user) {
            throw new NotFoundHttpException('Автор не найден');
        }

        $reviews = Review::find()
            ->where(['author_id' => $id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->render('author-reviews', [
            'user' => $user,
            'reviews' => $reviews,
        ]);
    }
}
