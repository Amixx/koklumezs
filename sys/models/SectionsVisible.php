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
            'name' => 'Sekcijas nosaukums',
            'is_visible' => 'Vai redzams lietotājiem',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionsVisible()
    {
        return ArrayHelper::map(self::find()->asArray()->all(), 'id', 'name', 'is_visible');
    }

    /**
     * {@inheritdoc}
     */
    public function isVisible($name)
    {
        return self::find()->where(['name' => $name])->one()['is_visible'];
    }
}
