<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

class School extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'schools';
    }

    public function rules()
    {
        return [
            [['instrument'], 'required'],
            [[
                'instrument',
                'background_image',
                'registration_background_image',
                'logo',
                'video_thumbnail',
                'email',
                'registration_title',
                'login_title',
                'registration_message',
                'renter_message',
                'trial_ended_message',
                'registration_image',
                'teacher_portrait',
                'rent_text',
            ], 'string'],
            [['rent_schoolsubplan_id'], 'number'],
            [['created'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'instrument' => \Yii::t('app',  'Instrument'),
            'created' => \Yii::t('app',  'Creation date'),
            'background_image' => \Yii::t('app',  'School background image'),
            'registration_background_image' => \Yii::t('app',  'Registration page background image'),
            'logo' => \Yii::t('app',  'Logo (preferably in SVG format)'),
            'video_thumbnail' => \Yii::t('app',  'Video thumbnail'),
            'email' => \Yii::t('app',  'E-mail'),
            'rent_schoolsubplan_id' => \Yii::t('app',  'Subscription plan used to generate invoice for renters'),
            'registration_title' => \Yii::t('app', 'Registration title'),
            'login_title' => \Yii::t('app', 'Log in title'),
            'registration_message' => \Yii::t('app',  'Registration message'),
            'renter_message' => \Yii::t('app',  'Message for students who want to rent an instrument'),
            'trial_ended_message' => \Yii::t('app',  'Message to send after trial period has ended'),
            'registration_image' => \Yii::t('app',  'Image in the first page of registration'),
            'teacher_portrait' => \Yii::t('app',  'A portrait of the teacher'),
            'rent_text' => \Yii::t('app',  'Text to show in the rent page'),
        ];
    }

    public static function getByStudent($studentId)
    {
        $schoolId = SchoolStudent::getSchoolStudent($studentId)->school_id;
        return self::findOne(['id' => $schoolId]);
    }

    public static function getSettings()
    {
        $userContext = Yii::$app->user->identity;
        $school = $userContext->getSchool();
        $rentSubplanName = $school->rent_schoolsubplan_id
            ? SchoolSubPlans::find()->where(['id' => $school->rent_schoolsubplan_id])->one()['name']
            : null;

        return [
            \Yii::t('app', 'School background image') => $school->background_image,
            \Yii::t('app', 'Registration page background image') => $school->registration_background_image,
            \Yii::t('app', 'Video thumbnail') => $school->video_thumbnail,
            \Yii::t('app', 'Logo') => $school->logo,
            \Yii::t('app', 'E-mail') => $school->email,
            \Yii::t('app', 'Registration title') => $school->registration_title,
            \Yii::t('app', 'Log in title') => $school->login_title,
            \Yii::t('app', 'Subscription plan used to generate invoice for renters') => $rentSubplanName,
            \Yii::t('app', 'Registration message') => $school->registration_message,
            \Yii::t('app', 'Message for students who want to rent an instrument') => $school->renter_message,
            \Yii::t('app', 'Message to send after trial period has ended') => $school->trial_ended_message,
            \Yii::t('app', 'Image in the first page of registration') => $school->registration_image,
            \Yii::t('app', 'A portrait of the teacher') => $school->teacher_portrait,
            \Yii::t('app', 'Text to show in the rent page') => $school->rent_text,
        ];
    }

    public static function getBankAccount($schoolId)
    {
        $schoolReqs = BankAccounts::find()->where(['school_id' => $schoolId])->one();
        if (!empty($schoolReqs)) {
            return [
                \Yii::t('app', 'Supplier') => $schoolReqs->supplier,
                \Yii::t('app', 'Registration number') => $schoolReqs->registration_number,
                \Yii::t('app', 'PVN registration number') => $schoolReqs->pvn_registration_number,
                \Yii::t('app', 'Legal address') => $schoolReqs->legal_address,
                \Yii::t('app', 'Bank') => $schoolReqs->bank,
                \Yii::t('app', 'Account number') => $schoolReqs->account_number
            ];
        } else {
            return NULL;
        }
    }

    public static function getSchoolStudentIds()
    {
        $userContext = Yii::$app->user->identity;
        $school = $userContext->getSchool();
        $students = SchoolStudent::getSchoolStudents($school['id']);
        return ArrayHelper::map($students, 'id', 'user_id');
    }
}
