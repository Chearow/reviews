<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "review".
 *
 * @property int $id
 * @property string $title
 * @property string $text
 * @property int $rating
 * @property string|null $img
 * @property int $author_id
 * @property int|null $is_for_all
 * @property int $created_at
 *
 * @property City[] $cities
 * @property ReviewCity[] $reviewCities
 * @property User $user
 */
class Review extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'review';
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
            [['title', 'text', 'rating', 'author_id'], 'required'],
            [['rating', 'author_id'], 'integer'],
            ['is_for_all', 'boolean'],
            [['title'], 'string', 'max' => 100],
            [['text', 'img'], 'string', 'max' => 255],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'text' => 'Text',
            'rating' => 'Rating',
            'img' => 'Image',
            'author_id' => 'Author',
            'is_for_all' => 'For All Cities',
            'created_at' => 'Created At',
        ];
    }

    public function getCities()
    {
        return $this->hasMany(City::class, ['id' => 'city_id'])->viaTable('review_city', ['review_id' => 'id']);
    }

    public function getReviewCities()
    {
        return $this->hasMany(ReviewCity::class, ['review_id' => 'id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }
}
