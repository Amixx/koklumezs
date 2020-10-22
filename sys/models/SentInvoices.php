<?php

namespace app\models;

use Yii;

class SentInvoices extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sentinvoices';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'invoice_number', 'plan_name', 'plan_price', 'plan_start_date'], 'required'],
            [['user_id', 'invoice_number', 'plan_price'], 'number'],
            [['plan_name', 'plan_start_date', 'sent_date'], 'string']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => \Yii::t('app',  'Student ID'),
            'invoice_number' => \Yii::t('app',  'Invoice number'),
            'plan_name' => \Yii::t('app',  'Plan title'),
            'plan_price' => \Yii::t('app',  'Plan price (monthly)'),
            'plan_start_date' => \Yii::t('app',  'Plan start date'),
            'sent_date' => \Yii::t('app',  'Sent date'),
        ];
    }

    public function getStudent()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommentResponses($id)
    {
        return self::find()->where(['userlectureevaluation_id' => $id])->joinWith('author')->asArray()->all();
    }
}
