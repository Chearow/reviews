<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\Review;

class ReviewForm extends Model
{
    public $title;
    public $text;
    public $rating;
    public $city_ids = [];
    public $imageFile;

    public function rules()
    {
        return [
            [['title', 'text', 'rating'], 'required'],
            ['title', 'string', 'max' => 100],
            ['text', 'string', 'max' => 255],
            ['rating', 'integer', 'min' => 1, 'max' => 5],
            ['city_ids', 'each', 'rule' => ['integer']],
            ['imageFile', 'file', 'extensions' => 'png, jpg, jpeg', 'skipOnEmpty' => true],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => 'Название',
            'text' => 'Отзыв',
            'rating' => 'Оценка',
            'city_ids' => 'Города',
            'imageFile' => 'Фото',
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $review = new Review();
        $review->title = $this->title;
        $review->text = $this->text;
        $review->rating = $this->rating;
        $review->author_id = Yii::$app->user->id;
        $review->city_ids = $this->city_ids;
        $review->is_for_all = empty($this->city_ids) ? 1 : 0;
        $review->imageFile = $this->imageFile;

        if (!$review->save()) {
            return false;
        }

        $review->uploadImage();
        return true;
    }
}