<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

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
            [['title', 'author'], 'required'], //'complexity',
            [['title', 'description', 'file'], 'string'],
            [['created', 'updated'], 'safe'],
            [['author', 'complexity'], 'integer'],
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
            'title' => \Yii::t('app',  'Title'),
            'description' => \Yii::t('app',  'Description'),
            'created' => \Yii::t('app',  'Created'),
            'updated' => \Yii::t('app',  'Updated'),
            'author' => \Yii::t('app',  'Author'),
            'complexity' => \Yii::t('app',  'Difficulty'),
            'season' => \Yii::t('app',  'Season'),
            'file' => \Yii::t('app',  'Video (not required)'),
            'thumb' => \Yii::t('app',  'Video thumbnail (not required)'),
        ];
    }

    public function getSeasons()
    {
        return [
            'Visas' => \Yii::t('app',  'All'),
            'Vasara' => \Yii::t('app',  'Summer'),
            'Rudens' => \Yii::t('app',  'Autumn'),
            'Ziema' => \Yii::t('app',  'Winter'),
            'Pavasaris' => \Yii::t('app',  'Spring'),
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function getComplexity()
    {
        $complex = [];
        for ($x = 1; $x <= 101; $x++) {
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

    public function getLecturesObjects()
    {
        return self::find()->asArray()->all();
    }

    public function getLecturesForUser($ids)
    {
        return ArrayHelper::map(self::find()->where(['not in', 'id', $ids])->asArray()->all(), 'id', 'title');
    }

    public function getLecturesObjectsForUser($ids)
    {
        $data = self::find()->where(['not in', 'id', $ids])->asArray()->all();
        $returnArray = [];
        foreach ($data as $d) {
            $returnArray[$d['id']] = [
                'id' => $d['id'],
                'title' => $d['title'],
                'complexity' => $d['complexity']
            ];
        }
        return $returnArray;
    }

    public function getLecturesByIds($ids, $asArray = false)
    {
        if ($asArray) {
            return ArrayHelper::map(self::find()->where(['in', 'id', $ids])->all(), 'id', 'title');
        } else {
            return self::find()->where(['in', 'id', $ids])->all();
        }
    }

    public function getLecturesBySeasonAndIds($ids, $season, $asArray = false)
    {
        if ($asArray) {
            return ArrayHelper::map(self::find()->where(['in', 'id', $ids])->andWhere(['season' => $season])->all(), 'id', 'title');
        } else {
            return self::find()->where(['in', 'id', $ids])->andWhere(['season' => $season])->all();
        }
    }


    public function getLecturesForRelations($id)
    {
        return ArrayHelper::map(self::find()->where(['not in', 'id', [$id]])->asArray()->all(), 'id', 'title');
    }
}
