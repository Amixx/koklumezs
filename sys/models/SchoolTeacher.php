<?php

namespace app\models;

use yii\helpers\ArrayHelper;
use app\models\Lectures;
use Yii;

class SchoolTeacher extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'schoolteachers';
    }

    public function rules()
    {
        return [
            [['school_id', 'user_id', 'instrument'], 'required'],
            [['school_id', 'user_id'], 'integer'],
            [['instrument'], 'string'],
            [['school_id'], 'exist', 'skipOnError' => true, 'targetClass' => School::class, 'targetAttribute' => ['school_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'school_id' => \Yii::t('app',  'School'),
            'user_id' => \Yii::t('app',  'Teacher'),
        ];
    }

    public function getSchool()
    {
        return $this->hasOne(School::class, ['id' => 'school_id']);
    }

    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }

    public static function getBySchoolId($schoolId)
    {
        return self::find()->where(['school_id' => $schoolId])->joinWith('school')->joinWith('user')->one();
    }

    public static function getByCurrentStudent()
    {
        $studentId = Yii::$app->user->identity->id;
        $schoolId = SchoolStudent::getSchoolStudent($studentId)->school_id;
        return self::getBySchoolId($schoolId);
    }
}
