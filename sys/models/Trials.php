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

    public static function displayTrialEndedMessage($studentId)
    {
        $studentSubplans = StudentSubPlans::getActivePlansForStudent($studentId);

        return empty($studentSubplans) && self::trialEnded($studentId);
    }

    public static function shouldSendTrialEndedEmail($studentId)
    {
        if (self::trialEnded($studentId)) {
            $trial = self::getByUserId($studentId);
            return !(bool)$trial['end_email_sent'];
        }
    }

    public static function markEndMessageSent($studentId)
    {
        $trial = self::getByUserId($studentId);
        $trial->end_email_sent = true;
        return $trial->save();
    }

    private static function trialEnded($studentId)
    {
        $trial = self::getByUserId($studentId);
        if (!$trial) {
            return false;
        }
        $trialEndDate = strtotime("+2 weeks", strtotime($trial['start_date']));
        $time = time();

        return $time > $trialEndDate;
    }

    private static function getByUserId($userId)
    {
        return self::find()->where(['user_id' => $userId])->one();
    }
}
