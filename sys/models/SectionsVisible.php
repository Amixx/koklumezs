<?php

namespace app\models;

use yii\helpers\ArrayHelper;

use Yii;

/**
 * This is the model class for table "sectionsvisible".
 *
 * @property int $id
 * @property string $name Parametrs
 */
class SectionsVisible extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sectionsvisible';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string'],
            [['is_visible'], 'required'],
            [['is_visible'], 'boolean']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => \Yii::t('app',  'Section name'),
            'is_visible' => \Yii::t('app',  'Is visible to users'),
        ];
    }

    public static function getSectionsVisible()
    {
        return ArrayHelper::map(self::find()->asArray()->all(), 'id', 'name', 'is_visible');
    }

    public static function isVisible($name)
    {
        return self::find()->where(['name' => $name])->one()['is_visible'];
    }
}
