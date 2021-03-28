<?php

namespace app\models;

/**
 * This is the model class for table "difficulties".
 *
 * @property int $id
 * @property string $name Parametrs
 */
class SchoolFaqs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'schoolfaqs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['school_id', 'question', 'answer'], 'required'],
            [['question', 'answer'], 'string'],
            [['school_id'], 'number']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'school_id' => \Yii::t('app',  'School ID'),
            'question' => \Yii::t('app',  'Question'),
            'answer' => \Yii::t('app',  'Answer'),
        ];
    }

    public static function getForSchool($schoolId)
    {
        return self::find()->where(['school_id' => $schoolId])->asArray()->all();
    }

    public static function getForCurrentSchool()
    {
        $schoolId = School::getCurrentSchoolId();
        return self::getForSchool($schoolId);
    }
}
