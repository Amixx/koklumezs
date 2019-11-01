<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lecturesfiles".
 *
 * @property int $id
 * @property string $file Fails
 * @property string $title Virsraksts
 * @property int $lecture_id Lekcija
 *
 * @property Lectures $lecture
 */
class Lecturesfiles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lecturesfiles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file', 'lecture_id'], 'required'],
            [['file','title'], 'string'],
            [['lecture_id'], 'integer'],
            [['lecture_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lectures::className(), 'targetAttribute' => ['lecture_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'file' => 'Fails',
            'lecture_id' => 'Lekcija',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLecture()
    {
        return $this->hasOne(Lectures::className(), ['id' => 'lecture_id']);
    }
}
