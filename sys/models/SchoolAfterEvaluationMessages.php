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
        $messages[2] = self::getSchoolMessagesByEvaluation($schoolId, 2);
        $messages[4] = self::getSchoolMessagesByEvaluation($schoolId, 4);
        $messages[6] = self::getSchoolMessagesByEvaluation($schoolId, 6);
        $messages[8] = self::getSchoolMessagesByEvaluation($schoolId, 8);
        $messages[10] = self::getSchoolMessagesByEvaluation($schoolId, 10);
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
