<?php

namespace app\models;

class Payer extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'payer';
    }

    public function rules()
    {
        return [
            [['user_id', 'name', 'address'], 'required'],
            [['name', 'personal_code', 'address', 'pvn_registration_number', 'bank', 'swift', 'account_number'], 'string'],
            [['user_id', 'registration_number'], 'number'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => \Yii::t('app', 'Student'),
            'name' => \Yii::t('app', 'Name/Title'),
            'address' => \Yii::t('app', 'Legal address'),
            'personal_code' => \Yii::t('app', 'Personal code'),
            'registration_number' => \Yii::t('app', 'Registration number'),
            'pvn_registration_number' => \Yii::t('app', 'PVN registration number'),
            'bank' => \Yii::t('app', 'Bank'),
            'swift' => 'SWIFT',
            'account_number' => \Yii::t('app', 'Account number'),
        ];
    }

    public function getStudent()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    public static function getForStudent($studentId)
    {
        return self::find()->where(['user_id' => $studentId])->one();
    }
}
