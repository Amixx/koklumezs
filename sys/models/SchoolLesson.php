<?php

namespace app\models;

use yii\helpers\ArrayHelper;
use app\models\Lectures;
use Yii;

class SchoolLesson extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'schoollessons';
    }

    public function rules()
    {
        return [
            [['school_id', 'lesson_id'], 'required'],
            [['school_id', 'lesson_id'], 'integer'],
            [['school_id'], 'exist', 'skipOnError' => true, 'targetClass' => School::className(), 'targetAttribute' => ['school_id' => 'id']],
            [['lesson_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['lesson_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'school_id' => 'Skola',
            'lesson_id' => 'Nodarbība',
        ];
    }

    public function getSchool()
    {
        return $this->hasOne(Schools::className(), ['id' => 'school_id']);
    }

    public function getLessons()
    {
        return $this->hasMany(Users::className(), ['id' => 'lesson_id']);
    }

    // public function getRelations($id): array
    // {
    //     return ArrayHelper::map(self::find()->where(['lecture_id' => $id])->asArray()->all(), 'id', 'related_id');
    // }
}
