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
                'difficulties_color',
                'email',
                'registration_message',
                'registration_title',
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
            'difficulties_color' => \Yii::t('app',  'difficulties color'),
            'email' => \Yii::t('app',  'E-mail'),
            'registration_message' => \Yii::t('app',  'Registration message'),
            'renter_message' => \Yii::t('app',  'Message for students who want to rent an instrument'),
            'rent_schoolsubplan_id' => \Yii::t('app',  'Subscription plan used to generate invoice for renters'),
            'registration_title' => \Yii::t('app', 'Registration title'),
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
            \Yii::t('app', 'difficulties color') => $school->difficulties_color,
            \Yii::t('app', 'E-mail') => $school->email,
            \Yii::t('app', 'Registration message') => $school->registration_message,
            \Yii::t('app', 'Message for students who want to rent an instrument') => $school->renter_message,
            \Yii::t('app', 'Subscription plan used to generate invoice for renters') => $rentSubplanName,
        ];
    }

    public static function getCurrentSchool()
    {
        $userId = Yii::$app->user->identity->id;
        $isTeacher = Users::isCurrentUserTeacher();
        $school = null;
        if ($isTeacher) {
            $school = self::getByTeacher($userId);
        } else {
            $school = self::getByStudent($userId);
        }

        return $school;
    }

    public static function getCurrentSchoolId()
    {
        $userId = Yii::$app->user->identity->id;
        if(Users::isCurrentUserTeacher()) return SchoolTeacher::getSchoolTeacher($userId)->school_id;
        else return SchoolStudent::getSchoolStudent($userId)->school_id;
    }
}
