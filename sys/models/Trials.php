<?php

namespace app\models;

use yii\helpers\ArrayHelper;

use Yii;

/**
 * This is the model class for table "difficulties".
 *
 * @property int $id
 * @property string $name Parametrs
 */
class Trials extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trials';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => \Yii::t('app',  'User ID'),
            'start_date' => \Yii::t('app',  'Start date'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDifficulties()
    {
        return ArrayHelper::map(self::find()->asArray()->all(), 'id', 'name');
    }

    public static function getDifficultiesForSchool($schoolId)
    {
        return ArrayHelper::map(self::find()->where(['school_id' => $schoolId])->asArray()->all(), 'id', 'name');
    }

    public static function displayTrialEndedMessage($userId)
    {
        $student = Users::findOne(['id' => $userId]);
        $studentSubplans = StudentSubPlans::getActivePlansForStudent($student['id']);
        $trial = self::find()->where(['user_id' => $userId])->one();

        if (!empty($studentSubplans) || !$trial) {
            return false;
        }

        $trialEndDate = strtotime("+2 weeks", strtotime($trial['start_date']));
        $time = time();

        return $time > $trialEndDate;
    }
}
