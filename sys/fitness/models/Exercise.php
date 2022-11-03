<?php

namespace app\fitness\models;

use app\models\Users;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class Exercise extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fitness_exercises';
    }

    public function rules()
    {
        return [
            [['author_id', 'name', 'popularity_type'], 'required'],
            [['author_id'], 'integer'],
            [['is_pause'], 'boolean'],
            [['name', 'description', 'video', 'technique_video', 'popularity_type'], 'string'],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => \Yii::t('app',  'Author ID'),
            'name' => \Yii::t('app',  'Title'),
            'description' => \Yii::t('app',  'Apraksts'),
            'video' => \Yii::t('app', 'Video'),
            'technique_video' => \Yii::t('app', 'Technique video'),
            'is_pause' => \Yii::t('app', 'Is pause'),
            'popularity_type' => \Yii::t('app', 'Popularity type'),
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ]
        ];
    }


    public function getAuthor()
    {
        return $this->hasOne(Users::class, ['id' => 'author_id']);
    }

    public function getSets()
    {
        return $this->hasMany(ExerciseVideo::class, ['exercise_id' => 'id']);
    }

    public function getExerciseTags()
    {
        return $this->hasMany(ExerciseTag::class, ['exercise_id' => 'id'])->joinWith('tag');
    }

    public function getVideos(){
        return $this->hasMany(ExerciseVideo::class, ['exercise_id' => 'id']);
    }
}
