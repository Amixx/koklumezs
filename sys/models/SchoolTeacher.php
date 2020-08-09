<?php

namespace app\models;

use yii\helpers\ArrayHelper;
use app\models\Lectures;
use Yii;

class SchoolTeacher extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'schoolteachers';
    }

    public function rules()
    {
        return [
            [['school_id', 'user_id'], 'required'],
            [['school_id', 'user_id'], 'integer'],
            [['school_id'], 'exist', 'skipOnError' => true, 'targetClass' => School::className(), 'targetAttribute' => ['school_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'school_id' => 'Skola',
            'user_id' => 'Skolotājs',
        ];
    }

    public function getSchool()
    {
        return $this->hasOne(Schools::className(), ['id' => 'school_id']);
    }

    public function getTeacher()
    {
        return $this->hasOne(Users::className(), ['id' => 'teacher_id']);
    }

    // public function getRelations($id): array
    // {
    //     return ArrayHelper::map(self::find()->where(['lecture_id' => $id])->asArray()->all(), 'id', 'related_id');
    // }
}
