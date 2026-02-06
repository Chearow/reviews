<?php

namespace common\models;

/**
 * This is the model class for table "review_city".
 *
 * @property int $review_id
 * @property int $city_id
 *
 * @property City $city
 * @property Review $review
 */
class ReviewCity extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'review_city';
    }

    public function rules()
    {
        return [
            [['review_id', 'city_id'], 'required'],
            [['review_id', 'city_id'], 'integer'],
            [['review_id', 'city_id'], 'unique', 'targetAttribute' => ['review_id', 'city_id']],
            [
                ['city_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => City::class,
                'targetAttribute' => ['city_id' => 'id']
            ],
            [
                ['review_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Review::class,
                'targetAttribute' => ['review_id' => 'id']
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'review_id' => 'ID отзыва',
            'city_id' => 'ID города',
        ];
    }

    public function getCity()
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    public function getReview()
    {
        return $this->hasOne(Review::class, ['id' => 'review_id']);
    }
}
