<?php

namespace frontend\controllers;

use common\models\City;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class CityController extends Controller
{
    public function actionSearch($q = null)
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

    public function actionCreateAjax()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $query = Yii::$app->request->post('query');
        if (!$query) {
            return ['success' => false, 'message' => 'Пустой запрос'];
        }

        $apiKey = Yii::$app->params['dadataApiKey'];

        $ch = curl_init('https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Accept: application/json",
            "Authorization: Token $apiKey",
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'query' => $query,
            'from_bound' => ['value' => 'city'],
            'to_bound' => ['value' => 'city'],
        ]));

        $result = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($result, true);

        if (empty($data['suggestions'])) {
            return ['success' => false, 'message' => 'Город не найден в Dadata'];
        }

        $cityName = $data['suggestions'][0]['data']['city'] ?? null;

        if (!$cityName) {
            return ['success' => false, 'message' => 'Некорректный ответ Dadata'];
        }

        $existing = City::findOne(['name' => $cityName]);
        if ($existing) {
            return [
                'success' => true,
                'id' => $existing->id,
                'text' => $existing->name,
            ];
        }

        $city = new City();
        $city->name = $cityName;
        $city->created_at = time();
        $city->save(false);

        return [
            'success' => true,
            'id' => $city->id,
            'text' => $city->name,
        ];
    }
}