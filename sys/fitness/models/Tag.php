<?php

namespace app\fitness\models;

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\models\Users;

class Tag extends \yii\db\ActiveRecord
{
    public const TAG_TYPE_MUSCLE_GROUP = 'MUSCLE_GROUP';
    public const TAG_TYPE_MUSCLE = 'MUSCLE';
    public const TAG_TYPE_EQUIPMENT = 'EQUIPMENT';
    public const TAG_TYPE_EXERCISE_TYPE = 'EXERCISE_TYPE';
    public const TAG_TYPE_LOAD_TYPE = 'LOAD_TYPE';
    public const TAG_TYPE_PAUSE = 'PAUSE';

    public const TAG_TYPE_SELECT_OPTIONS = [
        null => '',
        self::TAG_TYPE_MUSCLE_GROUP => 'Muskuļu grupa',
        self::TAG_TYPE_MUSCLE => 'Muskulis',
        self::TAG_TYPE_EQUIPMENT => 'Piederums',
        self::TAG_TYPE_EXERCISE_TYPE => 'Vingrojuma tips',
        self::TAG_TYPE_LOAD_TYPE => 'Slodzes veids',
        self::TAG_TYPE_PAUSE => 'Pauze',
    ];

    public static function getTagTypeLabel($tagType){
        if(!$tagType) return '';
        return self::TAG_TYPE_SELECT_OPTIONS[$tagType];
    }

    public static function tableName()
    {
        return 'fitness_tags';
    }

    public function rules()
    {
        return [
            [['author_id', 'value'], 'required'],
            [['author_id'], 'integer'],
            [['value', 'description', 'type'], 'string'],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => \Yii::t('app',  'Author ID'),
            'value' => \Yii::t('app',  'Value'),
            'description' => \Yii::t('app', 'Description'),
            'type' => \Yii::t('app', 'Tips'),
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ]
        ];
    }


    public function getAuthor()
    {
        return $this->hasOne(Users::class, ['id' => 'author_id']);
    }

    public static function getForSelect(){
        $tags = self::find()->asArray()->all();
        $res = [];
        foreach($tags as $tag) {
            $res[$tag['id']] = $tag['value'];
        }
        return $res;
    }
}
