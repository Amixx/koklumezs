<?php

namespace app\models;
use app\models\SchoolTeacher;

use Yii;

class SentInvoices extends \yii\db\ActiveRecord
{
    public $paid_date = null;
    public $paid_months = null;

    public static function tableName()
    {
        return 'sentinvoices';
    }

    public function rules()
    {
        return [
            [['user_id', 'invoice_number', 'plan_name', 'plan_price', 'plan_start_date'], 'required'],
            [['user_id', 'invoice_number', 'plan_price'], 'number'],
            [['plan_name', 'plan_start_date', 'sent_date'], 'string'],
            [['is_advance'], 'boolean']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => \Yii::t('app',  'Student ID'),
            'invoice_number' => \Yii::t('app',  'Invoice number'),
            'is_advance' => \Yii::t('app',  'Is advance invoice'),
            'plan_name' => \Yii::t('app',  'Plan title'),
            'plan_price' => \Yii::t('app',  'Plan price (monthly)'),
            'plan_start_date' => \Yii::t('app',  'Plan start date'),
            'sent_date' => \Yii::t('app',  'Sent/Paid date'),
        ];
    }

    public function getStudent()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id'])->joinWith('payer');
    }

    public static function getForCurrentSchool(){
        $schoolId = School::getCurrentSchoolId();
        $schoolStudentIds = SchoolStudent::getSchoolStudentIds($schoolId);

        return self::find()->andWhere(['and', ['in', 'sentinvoices.user_id', $schoolStudentIds]])->joinWith('student');
    }

    public function getLatestForStudent($studentId){
        $studentInvoices = self::find()->where(['user_id' => $studentId])->orderBy(['sent_date' => SORT_DESC])->asArray()->all();
        if($studentInvoices && count($studentInvoices) > 0) return $studentInvoices[0];
        else return null;
    }

    public function getUnpaidForStudent($studentId){
        $query = "SELECT invoice_number FROM sentinvoices
            WHERE is_advance = true
            AND user_id = $studentId
            AND invoice_number
            NOT IN ( SELECT invoice_number FROM sentinvoices WHERE is_advance = false )";
        $data = Yii::$app->db->createCommand($query)->queryAll();

        if($data && count($data) > 0) return $data;
        else return null;
    }
    
    public static function getRealInvoice($invoiceNumber){
        return self::find()->where(['invoice_number' => $invoiceNumber, 'is_advance' => false])->one();
    }

    public static function getInvoiceCss(){
        return '
            body {
                font-family: Arial, serif;
                color: rgb(0, 0, 0);
                font-weight: normal;
                font-style: normal;
                text-decoration: none
            }

            .bordered-table {
                width: 100%; border: 1px solid black;
                border-collapse:collapse;
            }

            .bordered-table td, th {
                border: 1px solid black;
                text-align:center;
            }

            .bordered-table th {
                font-weight:normal;
                padding:8px 4px;
            }

            .bordered-table td {
                padding: 32px 4px;
            }

            .font-l {
                font-size: 18px;
            }

            .font-m {
                font-size: 15px;
            }

            .font-s {
                font-size: 14px;
            }

            .font-xs {
                font-size: 13px;
            }

            .align-center {
                text-align:center;
            }

            .align-right {
                text-align:right;
            }

            .lh-2 {
                line-height:2;
            }

            .leftcol {
                width:140px;
            }

            .info {
                line-height:unset;
                margin-top:16px;
            }
        ';
    }
}
