<?php

namespace backend\modules\admin\controllers;

use Yii;
use common\models\Review;
use common\models\search\ReviewSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;

class ReviewController extends Controller
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex()
    {
        $searchModel = new ReviewSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new Review();

        if ($model->load($this->request->post())) {
            $model->city_ids = $this->request->post('Review')['city_ids'] ?? [];
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if($model->validate()) {
                if ($model->uploadImage()) {
                    if ($model->save(false)) {
                        Yii::$app->cache->flush();
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                }
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->city_ids = ArrayHelper::getColumn($model->cities, 'id');

        if ($model->load($this->request->post())) {
            $model->city_ids = $this->request->post('Review')['city_ids'] ?? [];
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');

            if($model->validate()) {
                if ($model->uploadImage()) {
                    if ($model->save(false)) {
                        Yii::$app->cache->flush();
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                }
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->cache->flush();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Review::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
