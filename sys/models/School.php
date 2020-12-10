<?php

namespace app\models;

use Yii;

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
            [['instrument', 'background_image', 'registration_background_image', 'logo', 'video_thumbnail', 'email'], 'string'],
            [['created'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'instrument' => \Yii::t('app',  'Instrument'),
            'created' => \Yii::t('app',  'Creation date'),
            'background_image' => \Yii::t('app',  'School background image'),
            'registration_background_image' => \Yii::t('app',  'Registration page background image'),
            'logo' => \Yii::t('app',  'Logo (preferably in SVG format)'),
            'video_thumbnail' => \Yii::t('app',  'Video thumbnail'),
            'email' => \Yii::t('app',  'E-mail'),
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
            \Yii::t('app',  'Registration page background image') => $school->registration_background_image,
            \Yii::t('app',  'Video thumbnail') => $school->video_thumbnail,
            \Yii::t('app',  'Logo') => $school->logo,
            \Yii::t('app',  'E-mail') => $school->email,
        ];
    }

    public function getCurrentSchool()
    {
        $userId = Yii::$app->user->identity->id;
        $isTeacher = Users::isCurrentUserTeacher();
        $school = null;
        if ($isTeacher) {
            $school = self::getByTeacher($userId);
        } else {
            $school = self::getByStudent($userId);
        }

        return $school;
    }

    public function getVideoThumb($userId)
    {
        $isTeacher = Users::isCurrentUserTeacher();
        $school = null;
        if ($isTeacher) {
            $school = self::getByTeacher($userId);
        } else {
            $school = self::getByStudent($userId);
        }

        return $school->video_thumbnail;
    }

    public function getCurrentSchoolId()
    {
        $userId = Yii::$app->user->identity->id;
        if(Users::isCurrentUserTeacher()) return SchoolTeacher::getSchoolTeacher($userId)->school_id;
        else return SchoolStudent::getSchoolStudent($userId)->school_id;
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
