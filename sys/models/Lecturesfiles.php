<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lecturesfiles".
 *
 * @property int $id
 * @property string $file Fails
 * @property string $thumb Bilde
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
            [['file', 'title', 'thumb'], 'string'],
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
            'title' => \Yii::t('app',  'Title'),
            'file' => \Yii::t('app',  'File'),
            'thumb' => \Yii::t('app',  'Image'),
            'lecture_id' => \Yii::t('app',  'Lecture'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLecture()
    {
        return $this->hasOne(Lectures::className(), ['id' => 'lecture_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function getLectureFiles($id)
    {
        return self::find()->where(['lecture_id' => $id])->asArray()->all();
    }
}
