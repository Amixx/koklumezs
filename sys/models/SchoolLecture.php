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
            'school_id' => \Yii::t('app',  'School'),
            'lecture_id' => \Yii::t('app',  'Lesson'),
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

    public function getForSchool($schoolId){
        return self::find()->where(['school_id' => $schoolId])->asArray()->all();
    }

    public function getSchoolLectureIds($schoolId)
    {
        $schoolLectures = self::getForSchool($schoolId);
        return ArrayHelper::map($schoolLectures, 'id', 'lecture_id');
    }

    public function getSchoolLectureTitles($schoolId){
        $lectureIds = self::getSchoolLectureIds($schoolId);
        return ArrayHelper::map(Lectures::find()->where(['in', 'id', $lectureIds])->asArray()->all(), 'id', 'title');
    }
}
