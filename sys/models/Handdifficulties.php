<?php

namespace app\models;

use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "handdifficulties".
 *
 * @property int $id
 * @property string $hand Roka
 * @property string $category Kategorija
 *
 * @property Handdifficulties[] $lectureshanddifficulties
 */
class Handdifficulties extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'handdifficulties';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hand', 'category'], 'string'],
            [['category'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hand' => \Yii::t('app',  'Hand'),
            'category' => \Yii::t('app',  'Category'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHanddifficulties()
    {
        return $this->hasMany(Handdifficulties::className(), ['category_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     */
    public static function getDifficulties()
    {
        $result = $maps = [];
        $maps['categories'] = ArrayHelper::map(self::find()->asArray()->all(), 'id', 'category');
        $maps['hands'] = ArrayHelper::map(self::find()->asArray()->all(), 'id', 'hand');
        foreach ($maps['categories'] as $id => $cat) {
            $result[$maps['hands'][$id]][$id] =  $cat;
        }
        return $result;
    }
}
