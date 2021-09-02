<?php

namespace app\models;

use yii\helpers\ArrayHelper;

class SignupQuestions extends \yii\db\ActiveRecord
{
    const INSTRUMENT_QUESTON_ID = "instrument_question";
    const EXPERIENCE_QUESTION_ID = "experience_question";

    public static function tableName()
    {
        return 'signupquestions';
    }

    public function rules()
    {
        return [
            [['school_id', 'text'], 'required'],
            [['school_id'], 'number'],
            [['text'], 'string'],
            [['multiple_choice', 'allow_custom_answer'], 'boolean']
        ];
    }

    public function getSchool()
    {
        return $this->hasOne(Users::class, ['id' => 'school_id']);
    }

    public function getAnswerChoices()
    {
        return $this->hasMany(SignupQuestionsAnswerChoices::class, ['signup_question_id' => 'id']);
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'school_id' => \Yii::t('app',  'School ID'),
            'text' => \Yii::t('app',  'Text'),
            'multiple_choice' => \Yii::t('app',  'Multiple choice question') . "?",
            'allow_custom_answer' => \Yii::t('app',  'Allow custom answer') . "? (" . \Yii::t('app',  'Applies only to multiple-choice questions') . ")",
        ];
    }

    public static function isInstrumentQuestion($id)
    {
        return $id === self::INSTRUMENT_QUESTON_ID;
    }

    public static function isExperienceQuestion($id)
    {
        return $id === self::EXPERIENCE_QUESTION_ID;
    }

    public static function isAnswerPositive($answer)
    {
        return $answer === \Yii::t('app', 'Yes');
    }

    public static function getQuestionOfIndexForSchool($school, $index)
    {
        $questions = self::getForSchool($school);
        return isset($questions[$index])
            ? $questions[$index]
            : null;
    }

    private static function getForSchool($school)
    {
        $data = self::find()->where(['school_id' => $school['id']])->joinWith("answerChoices")->asArray()->all();
        $data[] = self::getQuestionAboutInstrument($school);
        $data[] = self::getQuestionAboutExperience();

        return $data;
    }

    private static function getQuestionAboutInstrument($school)
    {
        return [
            "id" => self::INSTRUMENT_QUESTON_ID,
            "text" => \Yii::t('app', 'Do you have your own') . ' ' . $school['instrument'] . '?',
            "multiple_choice" => true,
            "allow_custom_answer" => false,
            "answerChoices" => [
                [
                    'text' => \Yii::t('app', 'Yes'),
                ],
                [
                    'text' => \Yii::t('app', 'No')
                ]
            ]
        ];
    }

    private static function getQuestionAboutExperience()
    {
        return [
            "id" => self::EXPERIENCE_QUESTION_ID,
            "text" => \Yii::t('app', 'Have you played this instrument before?'),
            "multiple_choice" => true,
            "allow_custom_answer" => false,
            "answerChoices" => [
                [
                    'text' => \Yii::t('app', 'Yes'),
                ],
                [
                    'text' => \Yii::t('app', 'No')
                ]
            ]
        ];
    }
}
