<?php

namespace app\models;

use yii\helpers\ArrayHelper;

use Yii;

class SchoolSubPlans extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'schoolsubplans';
    }

    public function rules()
    {
        return [
            [['school_id', 'name', 'months', 'max_pause_weeks'], 'required'],
            [['school_id', 'months', 'max_pause_weeks', 'pvn_percent', 'days_for_payment'], 'number'],
            [['name', 'description', 'files', 'message', 'type'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'school_id' => \Yii::t('app',  'School ID'),
            'name' => \Yii::t('app',  'Title'),
            'description' => \Yii::t('app',  'Description'),
            'type' => \Yii::t('app',  'Tips'),
            'pvn_percent' => \Yii::t('app',  'PVN (percentage)'),
            'months' => \Yii::t('app',  'Months (0 - unlimited)'),
            'max_pause_weeks' => \Yii::t('app',  'Pause weeks'),
            'files' => \Yii::t('app',  'Files'),
            'message' => \Yii::t('app',  'Message to send with the invoice'),
            'days_for_payment' => \Yii::t('app', 'How many days to pay the bill'),
        ];
    }

    public function typeText()
    {
        return $this->type === 'lesson' ? \Yii::t('app', 'subscription') : \Yii::t('app', 'rent');
    }

    public static function getForSchool($schoolId)
    {
        return self::find()->where(['school_id' => $schoolId]);
    }

    public static function getForCurrentSchool()
    {
        $userContext = Yii::$app->user->identity;
        $schoolId = $userContext->getSchool()->id;
        return self::getForSchool($schoolId);
    }

    public static function getMappedForSelection()
    {
        return ArrayHelper::map(self::getForCurrentSchool()->asArray()->all(), 'id', 'name');
    }

    public static function getPrices()
    {
        $isAdmin = Yii::$app->user->identity->user_level == 'Admin';
        $query = $isAdmin ? self::find() : self::getForCurrentSchool();
        $data = $query->asArray()->all();

        $res = [];

        foreach ($data as $item) {
            $price = SchoolSubplanParts::getPlanTotalCost($item['id']);
            $res[] = $price;
        }

        return $res;
    }
}
