<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "city".
 *
 * @property int $id
 * @property string $name
 * @property int $created_at
 *
 * @property ReviewCity[] $reviewCities
 * @property Review[] $reviews
 */
class City extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'city';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
            ],
        ];
    }

    public function rules()
    {
        return [
            [['name',], 'required'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'created_at' => 'Дата создания',
        ];
    }

    public function getReviewCities()
    {
        return $this->hasMany(ReviewCity::class, ['city_id' => 'id']);
    }

    public function getReviews()
    {
        return $this->hasMany(Review::class, ['id' => 'review_id'])->viaTable('review_city', ['city_id' => 'id']);
    }
}
