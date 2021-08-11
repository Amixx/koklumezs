<?php

namespace app\models;

use Yii;

class SchoolAfterEvaluationMessages extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'schoolafterevaluationmessages';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'message' => 'Message',
            'school_id' => 'School ID',
            'evaluation' => 'Evaluation',
        ];
    }

    public function rules()
    {
        return [
            [['message'], 'required'],
            [['school_id', 'evaluation'], 'integer'],
            [['evaluation'], 'safe']
        ];
    }

    public function getMessages()
    {
        return $this->hasMany(School::class, ['id' => 'school_id']);
    }

    public static function getMessagesBySchoolId($schoolId)
    {
        return self::find()->where(['school_id' => $schoolId])->orderBy('id asc')->all();
    }

    public static function getMessagesBySchoolIdGrouped($schoolId)
    {
        $messages = [];

        for ($i = 2; $i <= 10; $i += 2) {
            $messages[$i] = self::getSchoolMessagesByEvaluation($schoolId, $i);
        }

        return $messages;
    }



    public static function getSchoolMessagesByEvaluation($schoolId, $evaluation)
    {
        return self::find()
            ->where(['school_id' => $schoolId])
            ->andWhere(['evaluation' => $evaluation])
            ->orderBy('id asc')
            ->all();
    }

    public static function getRandomMessage($schoolId, $evaluation)
    {

        $messages = self::getSchoolMessagesByEvaluation($schoolId, $evaluation);

        if (empty($messages) || Chat::isChatCooldown()) return null;

        $messageTexts = [];

        foreach ($messages as $message) {
            $messageTexts[] = $message->message;
        }

        return $messageTexts[rand(0, count($messageTexts) - 1)];
    }
}
