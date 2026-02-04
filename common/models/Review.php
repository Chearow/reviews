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
    public $imageFile;
    public $city_ids = [];
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
            ['rating', 'integer', 'min' => 1, 'max' => 5],
            ['is_for_all', 'boolean'],
            [['title'], 'string', 'max' => 100],
            [['text', 'img'], 'string', 'max' => 255],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['author_id' => 'id']],
            ['city_ids', 'each', 'rule' => ['integer']],
            ['imageFile', 'file', 'extensions' => 'png, jpg, jpeg', 'skipOnEmpty' => true],
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

    public function uploadImage()
    {
        if ($this->imageFile) {
            if ($this->img) {
                $oldPath = Yii::getAlias('@frontend/web') . $this->img;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $fileName = uniqid('review_') . '.' . $this->imageFile->extension;
            $path = Yii::getAlias('@frontend/web/uploads/reviews/') . $fileName;

            if ($this->imageFile->saveAs($path)) {
                $this->img = '/uploads/reviews/' . $fileName;
                $this->save(false);
                return true;
            }
            return false;
        }
        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        ReviewCity::deleteAll(['review_id' => $this->id]);

        if(is_array($this->city_ids)) {
            foreach ($this->city_ids as $city_id) {
                $rc = new ReviewCity();
                $rc->review_id = $this->id;
                $rc->city_id = $city_id;
                $rc->save(false);
            }
        }
    }
}
