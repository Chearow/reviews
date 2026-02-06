<?php

namespace frontend\controllers;

use common\models\Review;
use frontend\models\ReviewForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ReviewController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create', 'create-ajax'],
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

    public function actionUpdate($id)
    {
        $model = Review::findone($id);
        if (!$model) {
            throw new NotFoundHttpException('Отзыв не найден');
        }
        if ($model->author_id !== Yii::$app->user->id) {
            throw new ForbiddenHttpException('Вы не можете редактировать этот отзыв');
        }

        $form = new ReviewForm();
        $form->loadFromReview($model);

        if ($form->load(Yii::$app->request->post()) && $form->update()) {
            Yii::$app->session->setFlash('success', 'Отзыв обновлён');
            return $this->redirect(['site/index']);
        }
        return $this->render('update', ['model' => $form]);
    }

    public function actionDelete($id)
    {
        $model = Review::findone($id);
        if (!$model) {
            throw new NotFoundHttpException('отзыв не найден');
        }
        if ($model->author_id !== Yii::$app->user->id) {
            throw new ForbiddenHttpException('Вы не можете удалить этот отзыв');
        }

        if ($model->img) {
            @unlink(Yii::getAlias('@frontend/web/' . $model->img));
        }
        $model->delete();

        Yii::$app->session->setFlash('success', 'Отзыв удалён');
        return $this->redirect(['site/index']);
    }

    public function actionCreateAjax()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new ReviewForm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->cache->flush();
            return ['success' => true];
        }

        return [
            'success' => false,
            'errors' => $model->getErrors()
        ];
    }
}