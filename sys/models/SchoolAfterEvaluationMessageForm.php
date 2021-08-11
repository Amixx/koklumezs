<?php

namespace app\models;

use Yii;
use yii\base\Model;

class SchoolAfterEvaluationMessageForm extends Model
{
    public $message;

    public function rules()
    {
        return [
            [['message'], 'required'],
            [['message'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'message' => Yii::t('app', 'Message'),
        ];
    }
}
