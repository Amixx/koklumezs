<?php

namespace app\models;

use yii\helpers\ArrayHelper;
use app\models\Lectures;
use Yii;

/**
 * This is the model class for table "relatedlectures".
 *
 * @property int $id
 * @property int $lecture_id Nodarbība
 * @property int $related_id Saistītā nodarbība
 *
 * @property Lectures $lecture
 */
class RelatedLectures extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'relatedlectures';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lecture_id', 'related_id'], 'required'],
            [['lecture_id', 'related_id'], 'integer'],
            [['lecture_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lectures::className(), 'targetAttribute' => ['lecture_id' => 'id']],
            [['related_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lectures::className(), 'targetAttribute' => ['related_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lecture_id' => \Yii::t('app',  'Lesson'),
            'related_id' => \Yii::t('app',  'Related lesson'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRel()
    {
        return $this->hasMany(Lectures::className(), ['id' => 'related_id']);
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
    public static function getRelations($id): array
    {
        return ArrayHelper::map(self::find()->where(['lecture_id' => $id])->asArray()->all(), 'id', 'related_id');
    }

    public static function getRelatedParents($id): array
    {
        return ArrayHelper::map(self::find()->where(['related_id' => $id])->asArray()->all(), 'id', 'lecture_id');
    }

    public static function getLectures($id)
    {
        return self::find()->where(['user_id' => $id])->orderBy(['lecture_id' => SORT_ASC])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function removeLectureRelations($id)
    {
        return self::deleteAll(['lecture_id' => $id]);
    }
}
