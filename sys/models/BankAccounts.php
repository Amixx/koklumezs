<?php

namespace app\models;

class BankAccounts extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'bankaccounts';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'school_id' => \Yii::t('app',  'School ID'),
            'supplier' => \Yii::t('app',  'Supplier'),
            'registration_number' => \Yii::t('app',  'Registration number'),
            'pvn_registration_number' => \Yii::t('app',  'PVN registration number'),
            'legal_address' => \Yii::t('app',  'Legal address'),
            'bank' => \Yii::t('app',  'Bank'),
            'account_number' => \Yii::t('app',  'Account number')
        ];
    }

    public function rules()
    {
        return [
            [['school_id'], 'required'],
            [['school_id', 'id', 'registration_number'], 'number'],
            [['supplier', 'pvn_registration_number', 'legal_address', 'bank', 'account_number'], 'string'],
        ];
    }

    public function getBankAccount()
    {
        return $this->hasOne(School::class, ['id' => 'school_id']);
    }

    public static function getCurrentSchoolsBankAccount($schoolId)
    {
        return self::findOne(['school_id' => $schoolId]);
    }
}
