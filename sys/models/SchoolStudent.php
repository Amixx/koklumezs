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

    public static function getSchoolStudentIds($schoolId)
    {
        $students = self::find()->where(['school_id' => $schoolId])->asArray()->all();
        return ArrayHelper::map($students, 'id', 'user_id');
    }

    public static function getSchoolStudent($studentId)
    {
        return self::find()->where(['user_id' => $studentId])->joinWith('school')->joinWith('user')->one();
    }
}
