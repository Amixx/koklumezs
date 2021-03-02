<?php
namespace app\models;

class SuggestSong extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'schoolsongsuggestions';
    }
    
    public function rules()
    {
        return [
            [['student_id', 'school_id', 'song'], 'required'],
            [['student_id', 'school_id'], 'number'],
            [['song'], 'string'],
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
            'song' => \Yii::t('app',  'Song'),
            'times_suggested' => \Yii::t('app', 'Times suggested'),
        ];
    }

    public function getForSchool($schoolId)
    {
        return self::find()->where(['school_id' => $schoolId])->joinWith('student')->asArray()->all();
    }
}