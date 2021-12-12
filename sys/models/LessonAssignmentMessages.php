<?php

namespace app\models;

use Yii;

class LessonAssignmentMessages extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'lesson_assignment_messages';
    }

    public function rules()
    {
        return [
            [['title', 'text'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lesson_id' => Yii::t('app',  'Lesson ID'),
            'title' => Yii::t('app',  'Title'),
            'text' => Yii::t('app',  'Text'),
        ];
    }

    public static function createFrom($lessonId, $assignPost)
    {
        $model = new LessonAssignmentMessages;
        $model->lesson_id = $lessonId;
        $model->title = $assignPost['subject'];
        $model->text = $assignPost['teacherMessage'];
        return $model->save();
    }

    public static function updateWith($lessonId, $assignPost)
    {
        $model = self::findOne(['lesson_id' => $lessonId]);
        $model->title = $assignPost['subject'];
        $model->text = $assignPost['teacherMessage'];
        return $model->update();
    }
}
