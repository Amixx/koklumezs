<?php

namespace app\models;

use Yii;

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
        return $this->hasOne(Lectures::class, ['id' => 'lesson_id']);
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

    public static function assignToStudent($schoolId, $user)
    {
        $schoolTeacher = SchoolTeacher::getBySchoolId($schoolId)["user"];
        $firstLectureIds = RegistrationLesson::getLessonIds($schoolId, $user->ownsInstrument, $user->hasExperience);
        $insertDate = date('Y-m-d H:i:s', time());
        $insertColumns = [];

        foreach ($firstLectureIds as $lid) {
            $insertColumns[] = [$schoolTeacher["id"], $user->id, $lid, $insertDate, 0, 0, 1];
        }

        Yii::$app->db
            ->createCommand()
            ->batchInsert('userlectures', [
                'assigned',
                'user_id',
                'lecture_id',
                'created',
                'user_difficulty',
                'open_times',
                'sent'
            ], $insertColumns)
            ->execute();
    }
}
