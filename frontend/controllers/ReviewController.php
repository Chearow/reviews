<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use frontend\models\ReviewForm;

class ReviewController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' =>['create', 'create-ajax'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionCreate()
    {
        $model = new ReviewForm();

        return $this->render('create', ['model' => $model]);
    }

    public function actionCreateAjax()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new ReviewForm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return ['success' => true];
        }

        return [
            'success' => false,
            'errors' => $model->getErrors()
        ];
    }
}