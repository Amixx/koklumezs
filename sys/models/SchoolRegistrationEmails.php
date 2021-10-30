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

    public static function sendEmail($user, $emailType)
    {
        $school = School::getByStudent($user['id']);
        $email = SchoolRegistrationEmails::getByType($school['id'], $emailType);

        return Yii::$app
            ->mailer
            ->compose(['html' => 'blank-message-html', 'text' => 'blank-message-text'], [
                'message' => $email,
            ])
            ->setFrom([$school['email'] => Yii::$app->name])
            ->setTo($user['email'])
            ->setSubject(SchoolRegistrationEmails::getSubjects()[$emailType])
            ->send();
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
            'quarterly_reminder_email' => Yii::t('app', 'E-mail sent to a student after 3 months if he did not complete any tasks at the scheduled time'),
        ];
    }

    public static function getSubjects()
    {
        return [
            'start_later_planned_email' => self::formatSubject('Congratulations on registering!'),
            'one_day_before_email' => self::formatSubject('Are you ready to start?'),
            'half_hour_before_email' => self::formatSubject('Let\'s get started!'),
            'missed_session_email' => self::formatSubject('Let\'s try? - lessons are waiting for you!'),
            'week_after_missed_email' => self::formatSubject('Hey! - let\'s start?'),
            'quarterly_reminder_email' => self::formatSubject('Want to try? - Start and enjoy 2 weeks for free!'),
        ];
    }

    private static function formatSubject($subject)
    {
        return Yii::t('app', $subject) . ' - ' . Yii::$app->name;
    }

    public static function getMappedForIndex()
    {
        $userContext = Yii::$app->user->identity;
        $schoolId = $userContext->getSchool()->id;
        $schoolRegistrationEmails = self::findOne(['school_id' => $schoolId]);
        $emailLabels = self::getLabels();
        $emailsForIndex = [];

        if ($schoolRegistrationEmails) {
            foreach ($schoolRegistrationEmails as $attr => $val) {
                if (isset($emailLabels[$attr])) {
                    $emailsForIndex[$attr] = [
                        'label' => $emailLabels[$attr],
                        'value' => $val,
                    ];
                }
            }
        }

        return $emailsForIndex;
    }

    public static function getByType($schoolId, $type)
    {
        $schoolRegistrationEmails = self::findOne(['school_id' => $schoolId]);
        if (!$schoolRegistrationEmails) return null;

        return $schoolRegistrationEmails[$type];
    }
}
