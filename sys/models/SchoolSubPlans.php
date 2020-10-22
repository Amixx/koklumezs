<?php

namespace app\models;

use yii\helpers\ArrayHelper;

use Yii;

class SchoolSubPlans extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'schoolsubplans';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['school_id', 'name', 'monthly_cost', 'months'], 'required'],
            [['monthly_cost'], 'double'],
            [['school_id', 'months'], 'number'],
            [['name', 'description'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'school_id' => \Yii::t('app',  'School ID'),
            'name' => \Yii::t('app',  'Title'),
            'description' => \Yii::t('app',  'Description'),
            'monthly_cost' => \Yii::t('app',  'Monthly cost'),
            'months' => \Yii::t('app',  'Months'),
        ];
    }

    public function getForSchool($schoolId)
    {
        return self::find()->where(['school_id' => $schoolId]);
    }

    public function getForCurrentSchool()
    {
        $schoolId = SchoolTeacher::getCurrentSchoolId();
        return self::getForSchool($schoolId);
    }

    public function getMappedForSelection()
    {
        return ArrayHelper::map(self::getForCurrentSchool()->asArray()->all(), 'id', 'name');
    }
}
