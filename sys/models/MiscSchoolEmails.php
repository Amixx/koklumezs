<?php

namespace app\models;

use Yii;

class MiscSchoolEmails extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'misc_school_emails';
    }

    public function rules()
    {
        return [
            [['school_id'], 'required'],
            [[
                'after_failed_first_rent_payment_email'
            ], 'string'],
            [['school_id'], 'integer']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'school_id' => \Yii::t('app',  'School ID'),
            'after_failed_first_rent_payment_email' => self::getLabel('after_failed_first_rent_payment_email'),
        ];
    }

    public static function sendEmail($user, $emailType)
    {
        $school = School::getByStudent($user['id']);
        $email = self::getByType($school['id'], $emailType);

        return Yii::$app
            ->mailer
            ->compose(['html' => 'blank-message-html', 'text' => 'blank-message-text'], [
                'message' => $email,
            ])
            ->setFrom([$school['email'] => Yii::$app->name])
            ->setTo($user['email'])
            ->setSubject(self::getSubjects()[$emailType])
            ->send();
    }

    public static function getLabel($email)
    {
        return self::getLabels()[$email];
    }

    public static function getLabels()
    {
        return [
            'after_failed_first_rent_payment_email' => Yii::t('app', 'An e-mail sent to a student when he has not paid the first rental invoice within 10 days and his profile is deleted'),
        ];
    }

    public static function getSubjects()
    {
        return [
            'after_failed_first_rent_payment_email' => self::formatSubject('Your profile is being deleted!'),
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
        $miscSchoolEmails = self::findOne(['school_id' => $schoolId]);
        $emailLabels = self::getLabels();
        $emailsForIndex = [];

        if ($miscSchoolEmails) {
            foreach ($miscSchoolEmails as $attr => $val) {
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
        $miscSchoolEmails = self::findOne(['school_id' => $schoolId]);
        if (!$miscSchoolEmails) return null;

        return $miscSchoolEmails[$type];
    }
}
