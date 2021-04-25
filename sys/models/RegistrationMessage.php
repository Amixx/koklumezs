<?php

namespace app\models;

class RegistrationMessage extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'registrationmessages';
    }

    public function rules()
    {
        return [
            [['school_id'], 'number'],
            [['body'], 'string'],
            [['school_id', 'body', 'for_students_with_instrument', 'for_students_with_experience'], 'required'],
            [['for_students_with_instrument', 'for_students_with_experience'], 'boolean']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'school_id' => \Yii::t('app',  'School ID'),
            'body' => \Yii::t('app',  'Message text'),
            'for_students_with_instrument' => \Yii::t('app',  'For students with instrument'),
            'for_students_with_experience' => \Yii::t('app',  'For students with experience'),
        ];
    }

    public static function getBody($schoolId, $ownsInstrument, $hasExperience)
    {
        $msg = self::find()->where([
            'school_id' => $schoolId,
            'for_students_with_instrument' => $ownsInstrument,
            'for_students_with_experience' => $hasExperience
        ])->one();

        return $msg ? $msg->body : null;
    }

    public static function getForIndex($schoolId, $withInstrument, $withExperience)
    {
        return static::find()->where([
            'school_id' => $schoolId,
            'for_students_with_instrument' => $withInstrument,
            'for_students_with_experience' => $withExperience
        ])->one();
    }
}
