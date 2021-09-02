<?php

namespace app\models;

use Yii;
use yii\base\Model;

class RegistrationQuestionForm extends Model
{
    public $answer;
    public $custom_answer_selected = false;
    public $custom_answer = '';

    public function rules()
    {
        return [
            [['answer', 'custom_answer_selected'], 'required'],
            [['answer', 'custom_answer'], 'string'],
            [['custom_answer_selected'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'answer' => Yii::t('app', 'Answer'),
            'custom_answer_selected' => Yii::t('app', 'Other') . ': ',
            'custom_answer' => Yii::t('app', 'Custom answer'),
        ];
    }
}
