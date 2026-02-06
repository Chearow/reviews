<?php

namespace frontend\models;

use common\models\Review;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class ReviewForm extends Model
{
    public $title;
    public $text;
    public $rating;
    public $city_ids = [];
    public $imageFile;

    /** @var Review */
    public $review;

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

    public function loadFromReview(Review $review)
    {
        $this->review = $review;
        $this->title = $review->title;
        $this->text = $review->text;
        $this->rating = $review->rating;
        $this->city_ids = $review->city_ids;
    }

    public function update()
    {
        if (!$this->validate()) {
            return false;
        }

        $review = $this->review;
        $oldImage = $review->img;
        $review->title = $this->title;
        $review->text = $this->text;
        $review->rating = $this->rating;
        $review->city_ids = $this->city_ids;
        $review->is_for_all = empty($this->city_ids) ? 1 : 0;
        $this->imageFile = UploadedFile::getInstance($this, 'imageFile');
        $review->imageFile = $this->imageFile;

        if (!$review->save()) {
            return false;
        }

        $review->uploadImage();

        if ($this->imageFile && $oldImage !== $review->img) {
            @unlink(Yii::getAlias('@frontend/web/' . $oldImage));
        }
        return true;
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->imageFile = UploadedFile::getInstance($this, 'imageFile');

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