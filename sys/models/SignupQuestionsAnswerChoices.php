<?php

namespace app\models;

use Yii;

class SignupQuestionsAnswerChoices extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'signup_questions_answer_choices';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['signup_question_id', 'text'], 'required'],
            [['signup_question_id'], 'integer'],
            [['text'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'signup_question_id' => \Yii::t('app',  'Registration question ID'),
            'text' => \Yii::t('app',  'Text'),
        ];
    }

    public static function createFromInputCollection($signupQuestionId, $inputCollection)
    {
        $insertColumns = [];
        foreach ($inputCollection as $input) {
            if ($input) {
                $insertColumns[] = [
                    $signupQuestionId,
                    $input,
                ];
            }
        }

        Yii::$app->db
            ->createCommand()
            ->batchInsert('signup_questions_answer_choices', [
                'signup_question_id',
                'text'
            ], $insertColumns)
            ->execute();
    }
}
