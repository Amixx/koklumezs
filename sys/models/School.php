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
            'instrument' => 'Instruments',
            'created' => 'Izveidošanas datums',
        ];
    }

    public function getByTeacher($teacherId)
    {
        $schoolId = SchoolTeacher::getSchoolTeacher($teacherId);
        return self::findOne(['id' => $schoolId]);
    }

    public function getByStudent($studentId)
    {
        $schoolId = SchoolStudent::getSchoolStudent($studentId);
        return self::findOne(['id' => $schoolId]);
    }

    public function getSettings($teacherId)
    {
        $school = self::getByTeacher($teacherId);
        return ["Skolas fona bilde" => $school->background_image];
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
