<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

class Schools extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'schools';
    }

    public function rules()
    {
        return [
            [['id', 'instrument'], 'required'], //'complexity',
            [['instrument'], 'string'],
            [['created'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'instrument' => 'Instruments',
            'created' => 'Izveidošanas datums',
        ];
    }

    public function getTeacher()
    {
        return $this->hasOne(Users::className(), ['id' => 'author']);
    }

    public function getUsers()
    {
        return $this->hasOne(Users::className(), ['id' => 'author'])->from(['u2' => Users::tableName()]);
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
