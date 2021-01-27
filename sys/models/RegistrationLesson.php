<?php

namespace app\models;

use yii\helpers\ArrayHelper;

class RegistrationLesson extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'registrationlesson';
    }

    public function rules()
    {
        return [
            [['school_id', 'lesson_id'], 'number'],
            [['school_id', 'lesson_id', 'for_students_with_experience'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'school_id' => \Yii::t('app',  'School ID'),
            'lesson_id' => \Yii::t('app',  'Lesson'),
            'for_students_with_experience' => \Yii::t('app',  'For students with experience'),
        ];
    }

    public function getLesson()
    {
        return $this->hasOne(Lectures::className(), ['id' => 'lesson_id']);
    }

    public function getDifficultiesForSchool($schoolId, $forStudentsWithExperience)
    {
        return ArrayHelper::map(self::find()->where(['school_id' => $schoolId, 'for_students_with_experience' => $forStudentsWithExperience])->joinWith('lesson')->asArray()->all(), 'id', 'lesson.title');
    }

    public function getLessonIds($schoolId, $hasExperience){
        $regLessons = self::find()->where(['school_id' => $schoolId, 'for_students_with_experience' => $hasExperience])->asArray()->all();

        return array_map(function($regLesson){
            return $regLesson['lesson_id'];
        }, $regLessons);
    }
}
