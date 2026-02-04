<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use common\models\City;

class CityController extends Controller
{
    public function ActionSearch($q = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$q) {
            return [];
        }

        $cities = City::find()
            ->where(['like', 'name', $q])
            ->limit(20)
            ->all();
        $results = [];

        foreach ($cities as $city) {
            $results[] = [
                'id' => $city->id,
                'text' => $city->name,
            ];
        }
        return $results;
    }
}