<?php

namespace frontend\services;

use Yii;

class ApiService
{
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = Yii::$app->params['dadataApiKey'];
    }

    public function suggestCity(string $query): ?string
    {
        $payload = [
            'query' => $query,
            'from_bound' => ['value' => 'city'],
            'to_bound' => ['value' => 'city'],
        ];

        $response = $this->sendRequest($payload);

        return $response['suggestions'][0]['data']['city'] ?? null;
    }

    private function sendRequest(array $payload): array
    {
        $curl = curl_init('https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Accept: application/json",
            "Authorization: Token {$this->apiKey}",
        ]);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));

        $result = curl_exec($curl);
        curl_close($curl);

        return json_decode($result, true);
    }

    public function detectCityNameByIP(string $ip): ?string
    {
        $url = "http://ip-api.com/json/{$ip}?lang=ru";

        $response = @file_get_contents($url);
        if (!$response) {
            return null;
        }

        $data = json_decode($response, true);
        if (!isset($data['status']) || $data['status'] !== 'success') {
            return null;
        }
        return $data['city'] ?? null;
    }
}