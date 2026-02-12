<?php

namespace frontend\controllers;

use frontend\repositories\CityRepository;
use frontend\services\ApiService;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class CityController extends Controller
{
    public ApiService $apiService;
    public CityRepository $cityRepository;

    public function __construct($id, $module, ApiService $apiService, CityRepository $cityRepository, $config = [])
    {
        $this->apiService = $apiService;
        $this->cityRepository = $cityRepository;
        parent::__construct($id, $module, $config);
    }

    public function actionSearch($cityQuery = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$cityQuery) {
            return [];
        }

        $cities = $this->cityRepository->searchByname($cityQuery);

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

        $cityName = $this->apiService->suggestCity($query);

        if (!$cityName) {
            return ['success' => false, 'message' => 'Город не найден в Dadata'];
        }

        $existing = $this->cityRepository->findByName($cityName);

        if ($existing) {
            return [
                'success' => true,
                'id' => $existing->id,
                'text' => $existing->name,
            ];
        }

        $city = $this->cityRepository->create($cityName);

        return [
            'success' => true,
            'id' => $city->id,
            'text' => $city->name,
        ];
    }
}