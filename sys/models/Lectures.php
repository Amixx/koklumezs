<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "lectures".
 *
 * @property int $id
 * @property string $title Nosaukums
 * @property string $description Apraksts
 * @property string $created Izveidota
 * @property string $updated Atjaunota
 * @property int $author Autors
 * @property string $complexity Sarežģītība
 *
 * @property Users $author
 */
class Lectures extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lectures';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'author', 'complexity'], 'required'],
            [['title', 'description', 'complexity'], 'string'],
            [['created', 'updated'], 'safe'],
            [['author'], 'integer'],
            [['author'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['author' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Nosaukums',
            'description' => 'Apraksts',
            'created' => 'Izveidota',
            'updated' => 'Atjaunota',
            'author' => 'Autors',
            'complexity' => 'Sarežģītība',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getComplexity()
    {
        $complex = [];
        for($x = 1;$x <=10;$x++){
            $complex[$x] = $x;
        }
       return $complex;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Users::className(), ['id' => 'author']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasOne(Users::className(), ['id' => 'author'])
        ->from(['u2' => Users::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLectures()
    {    
        return ArrayHelper::map(self::find()->asArray()->all(), 'id', 'title');        
    }


}