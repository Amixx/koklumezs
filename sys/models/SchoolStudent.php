<?php

namespace app\models;

use yii\helpers\ArrayHelper;
use app\models\Lectures;
use Yii;

class SchoolStudent extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'schoolstudents';
    }

    public function rules()
    {
        return [
            [['school_id', 'user_id'], 'required'],
            [['school_id', 'user_id'], 'integer'],
            [['school_id'], 'exist', 'skipOnError' => true, 'targetClass' => School::className(), 'targetAttribute' => ['school_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'school_id' => 'Skola',
            'user_id' => 'Students',
        ];
    }

    public function getSchool()
    {
        return $this->hasOne(School::className(), ['id' => 'school_id']);
    }

    public function getStudents()
    {
        return $this->hasMany(Users::className(), ['id' => 'user_id']);
    }

    public function getSchoolStudentIds($schoolId)
    {
        $students = self::find(['school_id' => $schoolId])->asArray()->all();
        return ArrayHelper::map($students, 'id', 'user_id');
    }

    // public function getRelations($id): array
    // {
    //     return ArrayHelper::map(self::find()->where(['lecture_id' => $id])->asArray()->all(), 'id', 'related_id');
    // }
}
