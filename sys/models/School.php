<?php

namespace app\models;

use Yii;

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
        ];
    }

    public static function getByTeacher($teacherId)
    {
        $schoolId = SchoolTeacher::getSchoolTeacher($teacherId)->school_id;
        return self::findOne(['id' => $schoolId]);
    }

    public static function getByStudent($studentId)
    {
        $schoolId = SchoolStudent::getSchoolStudent($studentId)->school_id;
        return self::findOne(['id' => $schoolId]);
    }

    public static function getSettings($teacherId)
    {
        $school = self::getByTeacher($teacherId);
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
        ];
    }

    public static function getCurrentSchool()
    {
        $userId = Yii::$app->user->identity->id;
        $isTeacher = Users::isCurrentUserTeacher();
        $isStudent = Users::isCurrentUserStudent();
        $school = null;

        if ($isTeacher) {
            $school = self::getByTeacher($userId);
        } else if ($isStudent) {
            $school = self::getByStudent($userId);
        }

        return $school;
    }

    public static function getCurrentSchoolId()
    {
        $currentSchool = self::getCurrentSchool();
        return $currentSchool ? $currentSchool->id : null;
    }
}
