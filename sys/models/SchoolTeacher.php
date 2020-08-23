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
            [['school_id'], 'exist', 'skipOnError' => true, 'targetClass' => School::className(), 'targetAttribute' => ['school_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
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
        return $this->hasOne(School::className(), ['id' => 'school_id']);
    }

    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    public function getSchoolTeacher($teacherId)
    {
        return self::find()->where(['user_id' => $teacherId])->joinWith('school')->joinWith('user')->one();
    }

    // public function getRelations($id): array
    // {
    //     return ArrayHelper::map(self::find()->where(['lecture_id' => $id])->asArray()->all(), 'id', 'related_id');
    // }
}
