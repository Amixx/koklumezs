<?php

namespace app\models;

use yii\helpers\ArrayHelper;

class SchoolStudent extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'schoolstudents';
    }

    public function rules()
    {
        return [
            [['school_id', 'user_id'], 'required'],
            [['school_id', 'user_id'], 'integer'],
            [['show_real_lessons'], 'boolean'],
            ['show_real_lessons', 'default', 'value' => false],
            [['school_id'], 'exist', 'skipOnError' => true, 'targetClass' => School::class, 'targetAttribute' => ['school_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'school_id' => \Yii::t('app',  'School'),
            'user_id' => \Yii::t('app',  'Student'),
            'show_real_lessons' => \Yii::t('app',  'Does the user see lessons with difficulty larger than 1'),
        ];
    }

    public function getSchool()
    {
        return $this->hasOne(School::class, ['id' => 'school_id']);
    }

    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }

    public function getStudents()
    {
        return $this->hasMany(Users::class, ['id' => 'user_id']);
    }

    public static function getSchoolStudentCommitments($schoolId)
    {
        $students = self::getSchoolStudents($schoolId);
        $studentIds = ArrayHelper::getColumn($students, 'user_id');
        $startLaterCommitments = StartLaterCommitments::find()->where(['in', 'user_id', $studentIds])->joinWith('user');

        return $startLaterCommitments;
    }

    public static function getSchoolStudentIds($schoolId)
    {
        $students = self::getSchoolStudents($schoolId);
        return ArrayHelper::map($students, 'id', 'user_id');
    }

    public static function getSchoolStudents($schoolId)
    {
        return self::find()->where(['school_id' => $schoolId])->asArray()->all();
    }

    public static function getSchoolStudent($studentId)
    {
        return self::find()->where(['user_id' => $studentId])->joinWith('school')->joinWith('user')->one();
    }

    public static function createNew($schoolId, $userId)
    {
        $schoolStudent = new SchoolStudent;
        $schoolStudent->school_id = $schoolId;
        $schoolStudent->user_id = $userId;

        return $schoolStudent->save();
    }
}
