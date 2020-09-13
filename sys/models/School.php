<?php

namespace app\models;

class School extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'schools';
    }

    public function rules()
    {
        return [
            [['instrument'], 'required'],
            [['instrument'], 'string'],
            [['created'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'instrument' => \Yii::t('app',  'Instrument'),
            'created' => \Yii::t('app',  'Creation date'),
            'background_image' => \Yii::t('app',  'Page background image'),
            'video_thumbnail' => \Yii::t('app',  'Video thumbnail'),
        ];
    }

    public function getByTeacher($teacherId)
    {
        $schoolId = SchoolTeacher::getSchoolTeacher($teacherId)->school_id;
        return self::findOne(['id' => $schoolId]);
    }

    public function getByStudent($studentId)
    {
        $schoolId = SchoolStudent::getSchoolStudent($studentId)->school_id;
        return self::findOne(['id' => $schoolId]);
    }

    public function getSettings($teacherId)
    {
        $school = self::getByTeacher($teacherId);
        return [
            \Yii::t('app',  'School background image') => $school->background_image,
            \Yii::t('app',  'Video thumbnail') => $school->video_thumbnail,
        ];
    }

    public function getVideoThumb($studentId)
    {
        $school = self::getByStudent($studentId);
        return $school->video_thumbnail;
    }

    // public function getTeachers()
    // {
    //     return $this->hasOne(Users::className(), ['id' => 'author']);
    // }

    // public function getStudents()
    // {
    //     return $this->hasOne(Users::className(), ['id' => 'author'])->from(['u2' => Users::tableName()]);
    // }

    // public function getLectures()
    // {
    //     return $this->hasOne(Users::className(), ['id' => 'author'])->from(['u2' => Users::tableName()]);
    // }
}
