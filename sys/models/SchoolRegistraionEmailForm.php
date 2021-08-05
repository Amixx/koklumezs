<?php

namespace app\models;

use Yii;
use yii\base\Model;

class SchoolRegistraionEmailForm extends Model
{
    public $type;
    public $value;

    public function rules()
    {
        return [
            [['type', 'value'], 'required'],
            [['type', 'value'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'type' => Yii::t('app', 'E-mail type'),
            'value' => Yii::t('app', 'E-mail'),
        ];
    }
}
