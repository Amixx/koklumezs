<?php

namespace app\models;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "lectureshanddifficulties".
 *
 * @property int $id
 * @property int $lecture_id Lekcija
 * @property int $category_id Kategorija
 *
 * @property Lectures $lecture
 * @property Handdifficulties $category
 */
class Lectureshanddifficulties extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lectureshanddifficulties';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lecture_id', 'category_id'], 'required'],
            [['lecture_id', 'category_id'], 'integer'],
            [['lecture_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lectures::className(), 'targetAttribute' => ['lecture_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Handdifficulties::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lecture_id' => 'Lekcija',
            'category_id' => 'Kategorija',
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
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Handdifficulties::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLectureDifficulties($id)
    {
        return ArrayHelper::map(self::find()->where(['lecture_id' => $id])->asArray()->all(), 'category_id', 'id');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function removeLectureDifficulties($id)
    {
        return self::deleteAll(['lecture_id' => $id]);        
    }
}