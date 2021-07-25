<?php

namespace app\models;

use Yii;

class SchoolRegistrationEmails extends \yii\db\ActiveRecord
{




    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'school_registration_emails';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['school_id'], 'required'],
            [[
                'start_later_planned_email',
                'one_day_before_email',
                'half_hour_before_email',
                'missed_session_email',
                'week_after_missed_email',
                'quarterly_reminder_email'
            ], 'string'],
            [['school_id'], 'integer']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'school_id' => \Yii::t('app',  'School ID'),
            'start_later_planned_email' => self::getLabel('start_later_planned_email'),
            'one_day_before_email' => self::getLabel('one_day_before_email'),
            'half_hour_before_email' => self::getLabel('half_hour_before_email'),
            'missed_session_email' => self::getLabel('missed_session_email'),
            'week_after_missed_email' => self::getLabel('week_after_missed_email'),
            'quarterly_reminder_email' => self::getLabel('quarterly_reminder_email'),
        ];
    }

    public static function getLabel($email)
    {
        return self::getLabels()[$email];
    }

    public static function getLabels()
    {
        return [
            'start_later_planned_email' => Yii::t('app',  'E-mail that is sent after the student has scheduled a time to start playing'),
            'one_day_before_email' => Yii::t('app', 'E-mail sent when there is one day left until the scheduled time to start playing'),
            'half_hour_before_email' => Yii::t('app', 'E-mail sent half an hour before the scheduled start time'),
            'missed_session_email' => Yii::t('app', 'E-mail sent to a student if he or she has not completed any tasks at the scheduled time'),
            'week_after_missed_email' => Yii::t('app', 'E-mail sent to a student after a week if he did not complete any tasks at the scheduled time'),
            'quarterly_reminder_email' => Yii::t('app', 'E-mail sent to those who have not started playing, on a quarterly basis'),
        ];
    }

    public static function getMappedForIndex()
    {
        $schoolId = School::getCurrentSchoolId();
        $schoolRegistrationEmails = self::findOne(['school_id' => $schoolId]);
        $emailLabels = self::getLabels();
        $emailsForIndex = [];

        foreach ($schoolRegistrationEmails as $attr => $val) {
            if (isset($emailLabels[$attr])) {
                $emailsForIndex[$attr] = [
                    'label' => $emailLabels[$attr],
                    'value' => $val,
                ];
            }
        }

        return $emailsForIndex;
    }
}
