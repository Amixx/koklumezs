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
            [['user_id', 'studentsubplan_id', 'invoice_number', 'plan_name', 'plan_price', 'plan_start_date'], 'required'],
            [['user_id', 'studentsubplan_id', 'invoice_number', 'plan_price'], 'number'],
            [['plan_name', 'plan_start_date', 'sent_date'], 'string'],
            [['is_advance'], 'boolean']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => \Yii::t('app',  'Student ID'),
            'studentsubplan_id' => \Yii::t('app',  'Student subscription plan ID'),
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

    public function getStudentSubplan()
    {
        return $this->hasOne(StudentSubPlans::className(), ['id' => 'studentsubplan_id'])->joinWith('plan');
    }

    public static function getForCurrentSchool()
    {
        $schoolId = School::getCurrentSchoolId();
        $schoolStudentIds = SchoolStudent::getSchoolStudentIds($schoolId);

        return self::find()->andWhere(['and', ['in', 'sentinvoices.user_id', $schoolStudentIds]])->joinWith('student');
    }

    public static function getLatestForStudent($studentId)
    {
        $studentInvoices = self::find()->where(['user_id' => $studentId])->orderBy(['sent_date' => SORT_DESC])->asArray()->all();
        if ($studentInvoices && count($studentInvoices) > 0) return $studentInvoices[0];
        else return null;
    }

    public static function getUnpaidForStudent($studentId)
    {
        $query = "SELECT invoice_number FROM sentinvoices
            WHERE is_advance = true
            AND user_id = $studentId
            AND invoice_number
            NOT IN ( SELECT invoice_number FROM sentinvoices WHERE is_advance = false )";
        $data = Yii::$app->db->createCommand($query)->queryAll();

        if ($data && count($data) > 0) return $data;
        else return null;
    }

    public static function getRealInvoice($invoiceNumber)
    {
        return self::find()->where(['invoice_number' => $invoiceNumber, 'is_advance' => false])->one();
    }

    public static function createAdvance($userId, $invoiceNumber, $schoolSubplan, $studentSubplan)
    {
        $invoice = new SentInvoices;
        $invoice->user_id = $userId;
        $invoice->studentsubplan_id = $studentSubplan['id'];
        $invoice->invoice_number = $invoiceNumber;
        $invoice->is_advance = true;
        $invoice->plan_name = $schoolSubplan['name'];
        $invoice->plan_price = SchoolSubplanParts::getPlanTotalCost($schoolSubplan['id']);
        $invoice->plan_start_date = $studentSubplan['start_date'];
        $invoice->save();
    }

    public static function createReal($studentId, $invoiceNumber, $schoolSubplan, $studentSubplan, $paidDate)
    {
        $invoice = new SentInvoices;
        $invoice->user_id = $studentId;
        $invoice->studentsubplan_id = $studentSubplan['id'];
        $invoice->invoice_number = $invoiceNumber;
        $invoice->is_advance = false;
        $invoice->plan_name = $schoolSubplan['name'];
        $invoice->plan_price = SchoolSubplanParts::getPlanTotalCost($schoolSubplan['id']);
        $invoice->plan_start_date = $studentSubplan['start_date'];
        $invoice->sent_date = $paidDate;
        $invoice->save();
    }
}
