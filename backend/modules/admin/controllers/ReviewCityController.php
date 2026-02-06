<?php

namespace backend\modules\admin\controllers;

use common\models\ReviewCity;
use common\models\search\ReviewCitySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ReviewCityController implements the CRUD actions for ReviewCity model.
 */
class ReviewCityController extends Controller
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex()
    {
        $searchModel = new ReviewCitySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($review_id, $city_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($review_id, $city_id),
        ]);
    }

    public function actionCreate()
    {
        $model = new ReviewCity();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'review_id' => $model->review_id, 'city_id' => $model->city_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($review_id, $city_id)
    {
        $model = $this->findModel($review_id, $city_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'review_id' => $model->review_id, 'city_id' => $model->city_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($review_id, $city_id)
    {
        $this->findModel($review_id, $city_id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($review_id, $city_id)
    {
        if (($model = ReviewCity::findOne(['review_id' => $review_id, 'city_id' => $city_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
