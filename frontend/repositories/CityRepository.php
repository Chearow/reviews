<?php

namespace frontend\repositories;

use common\models\City;

class CityRepository
{
    public function findByName(string $name): ?City
    {
        return City::findOne(['name' => $name]);
    }

    public function create(string $name): City
    {
        $city = new City();
        $city->name = $name;
        $city->created_at = time();
        $city->save(false);

        return $city;
    }

    public function searchByName(string $query, int $limit = 20): array
    {
        return City::find()
            ->where(['like', 'name', $query])
            ->limit($limit)
            ->all();
    }
}