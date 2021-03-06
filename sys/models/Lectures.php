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
            [['title', 'description', 'file', 'play_along_file'], 'string'],
            [['created', 'updated'], 'safe'],
            [['author', 'complexity'], 'integer'],
            [['author'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['author' => 'id']],
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
            'play_along_file' => \Yii::t('app',  'Play along video (not required)'),
            'thumb' => \Yii::t('app',  'Video thumbnail (not required)'),
        ];
    }

    public static function getSeasons()
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
    public static function getComplexity()
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
        return $this->hasOne(Users::class, ['id' => 'author']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasOne(Users::class, ['id' => 'author'])
            ->from(['u2' => Users::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getLectures()
    {
        return ArrayHelper::map(self::find()->asArray()->all(), 'id', 'title');
    }

    public static function getLecturesObjects()
    {
        return self::find()->asArray()->all();
    }

    public static function getLecturesForUser($ids)
    {
        return ArrayHelper::map(self::find()->where(['not in', 'id', $ids])->asArray()->all(), 'id', 'title');
    }

    public static function getLecturesObjectsForUser($assignedIds)
    {
        $idsToRemove = $assignedIds;
        $data = self::find()->asArray()->all();
        $returnArray = [];

        foreach ($data as $d) {
            if (in_array($d['id'], $assignedIds)) {
                $relatedLessonIds = RelatedLectures::getRelations($d['id']);

                foreach ($relatedLessonIds as $relId) {
                    $idsToRemove[] = $relId;
                }
            }
        }

        foreach ($data as $d) {
            if (!in_array($d['id'], $idsToRemove)) {
                $returnArray[$d['id']] = [
                    'id' => $d['id'],
                    'title' => $d['title'],
                    'complexity' => $d['complexity']
                ];
            }
        }

        return $returnArray;
    }

    public static function getLecturesByIds($ids, $asArray = false)
    {
        if ($asArray) {
            return ArrayHelper::map(self::find()->where(['in', 'id', $ids])->all(), 'id', 'title');
        } else {
            return self::find()->where(['in', 'id', $ids])->all();
        }
    }

    public static function getLecturesBySeasonAndIds($ids, $season, $asArray = false)
    {
        if ($asArray) {
            return ArrayHelper::map(self::find()->where(['in', 'id', $ids])->andWhere(['season' => $season])->all(), 'id', 'title');
        } else {
            return self::find()->where(['in', 'id', $ids])->andWhere(['season' => $season])->all();
        }
    }


    public static function getLecturesForRelations($id)
    {
        return ArrayHelper::map(self::find()->where(['not in', 'id', [$id]])->asArray()->all(), 'id', 'title');
    }


    public static function getLikesCount($lectureId)
    {
        $favUserLectures = UserLectures::find()->where(['lecture_id' => $lectureId, 'is_favourite' => true])->asArray()->all();

        return count($favUserLectures);
    }
}
