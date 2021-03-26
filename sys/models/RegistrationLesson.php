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
            [['school_id', 'lesson_id', 'for_students_with_instrument', 'for_students_with_experience'], 'required'],
            [['for_students_with_instrument', 'for_students_with_experience'], 'boolean']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'school_id' => \Yii::t('app',  'School ID'),
            'lesson_id' => \Yii::t('app',  'Lesson'),
            'for_students_with_instrument' => \Yii::t('app',  'For students with instrument'),
            'for_students_with_experience' => \Yii::t('app',  'For students with experience'),
        ];
    }

    public function getLesson()
    {
        return $this->hasOne(Lectures::className(), ['id' => 'lesson_id']);
    }

    public static function getLessonIds($schoolId, $ownsInstrument, $hasExperience)
    {
        $regLessons = self::find()->where(['school_id' => $schoolId, 'for_students_with_instrument' => $ownsInstrument, 'for_students_with_experience' => $hasExperience])->asArray()->all();

        return array_map(function ($regLesson) {
            return $regLesson['lesson_id'];
        }, $regLessons);
    }

    public static function getLessonsForIndex($schoolId, $withInstrument, $withExperience)
    {
        return static::find()->where(['school_id' => $schoolId, 'for_students_with_instrument' => $withInstrument, 'for_students_with_experience' => $withExperience])->joinWith('lesson');
    }
}
