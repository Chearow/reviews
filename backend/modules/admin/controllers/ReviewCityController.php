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
    /**
     * @inheritDoc
     */
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

    /**
     * Lists all ReviewCity models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ReviewCitySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ReviewCity model.
     * @param int $review_id Review ID
     * @param int $city_id City ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($review_id, $city_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($review_id, $city_id),
        ]);
    }

    /**
     * Creates a new ReviewCity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
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

    /**
     * Updates an existing ReviewCity model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $review_id Review ID
     * @param int $city_id City ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
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

    /**
     * Deletes an existing ReviewCity model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $review_id Review ID
     * @param int $city_id City ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($review_id, $city_id)
    {
        $this->findModel($review_id, $city_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ReviewCity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $review_id Review ID
     * @param int $city_id City ID
     * @return ReviewCity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($review_id, $city_id)
    {
        if (($model = ReviewCity::findOne(['review_id' => $review_id, 'city_id' => $city_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
