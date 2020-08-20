<?php

namespace app\models;

use yii\helpers\ArrayHelper;
use app\models\Lectures;
use Yii;

class SchoolLecture extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'schoollectures';
    }

    public function rules()
    {
        return [
            [['school_id', 'lecture_id'], 'required'],
            [['school_id', 'lecture_id'], 'integer'],
            [['school_id'], 'exist', 'skipOnError' => true, 'targetClass' => School::className(), 'targetAttribute' => ['school_id' => 'id']],
            [['lecture_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lectures::className(), 'targetAttribute' => ['lecture_id' => 'id']],
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
            'lecture_id' => 'Nodarbība',
        ];
    }

    public function getSchool()
    {
        return $this->hasOne(School::className(), ['id' => 'school_id']);
    }

    public function getLessons()
    {
        return $this->hasMany(Users::className(), ['id' => 'lecture_id']);
    }

    public function getSchoolLectureIds($schoolId)
    {
        $lectures = self::find()->where(['school_id' => $schoolId])->asArray()->all();
        return ArrayHelper::map($lectures, 'id', 'lecture_id');
    }

    // public function getRelations($id): array
    // {
    //     return ArrayHelper::map(self::find()->where(['lecture_id' => $id])->asArray()->all(), 'id', 'related_id');
    // }
}
