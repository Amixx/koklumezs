<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

class School extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'schools';
    }

    public function rules()
    {
        return [
            [['instrument'], 'required'],
            [['instrument'], 'string'],
            [['created'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'instrument' => 'Instruments',
            'created' => 'Izveidošanas datums',
        ];
    }

    // public function getTeachers()
    // {
    //     return $this->hasOne(Users::className(), ['id' => 'author']);
    // }

    // public function getStudents()
    // {
    //     return $this->hasOne(Users::className(), ['id' => 'author'])->from(['u2' => Users::tableName()]);
    // }

    // public function getLectures()
    // {
    //     return $this->hasOne(Users::className(), ['id' => 'author'])->from(['u2' => Users::tableName()]);
    // }
}
