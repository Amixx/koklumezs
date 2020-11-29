<?php

namespace app\models;

use yii\helpers\ArrayHelper;

use Yii;

class StudentQuestions extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'studentquestions';
    }

    public function rules()
    {
        return [
            [['student_id', 'school_id', 'text'], 'required'],
            [['student_id', 'school_id'], 'number'],
            [['text'], 'string'],
        ];
    }

    public function getStudent()
    {
        return $this->hasOne(Users::className(), ['id' => 'student_id']);
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'student_id' => \Yii::t('app',  'Student ID'),
            'school_id' => \Yii::t('app',  'School ID'),
            'text' => \Yii::t('app',  'Text'),
        ];
    }

    public function getForSchool($schoolId)
    {
        return self::find()->where(['school_id' => $schoolId])->joinWith('student')->asArray()->all();
    }
}
