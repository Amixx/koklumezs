<?php

namespace app\models;

use yii\helpers\ArrayHelper;

use Yii;

class SignupQuestions extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'signupquestions';
    }

    public function rules()
    {
        return [
            [['school_id', 'text'], 'required'],
            [['school_id'], 'number'],
            [['text'], 'string'],
        ];
    }

    public function getSchool()
    {
        return $this->hasOne(Users::className(), ['id' => 'school_id']);
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'school_id' => \Yii::t('app',  'School ID'),
            'text' => \Yii::t('app',  'Text'),
        ];
    }

    public static function getForSchool($schoolId)
    {
        return self::find()->where(['school_id' => $schoolId])->asArray()->all();
    }
}
