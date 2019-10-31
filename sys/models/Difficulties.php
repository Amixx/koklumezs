<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "difficulties".
 *
 * @property int $id
 * @property string $name Parametrs
 */
class Difficulties extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'difficulties';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Parametrs',
        ];
    }
}
