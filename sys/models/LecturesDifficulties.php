<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "lecturesdifficulties".
 *
 * @property int $id
 * @property int $diff_id Parametrs
 * @property int $lecture_id Lekcija
 * @property string $value Vērtība
 *
 * @property Lectures $lecture
 * @property Difficulties $diff
 */
class LecturesDifficulties extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lecturesdifficulties';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['diff_id', 'lecture_id', 'value'], 'required'],
            [['diff_id', 'lecture_id'], 'integer'],
            [['value'], 'string', 'max' => 50],
            [['lecture_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lectures::class, 'targetAttribute' => ['lecture_id' => 'id']],
            [['diff_id'], 'exist', 'skipOnError' => true, 'targetClass' => Difficulties::class, 'targetAttribute' => ['diff_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'diff_id' => \Yii::t('app',  'Parameter'),
            'lecture_id' => \Yii::t('app', 'Lesson'),
            'value' => \Yii::t('app',  'Value'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLecture()
    {
        return $this->hasOne(Lectures::class, ['id' => 'lecture_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiff()
    {
        return $this->hasOne(Difficulties::class, ['id' => 'diff_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getLectureDifficulties($id): array
    {
        return ArrayHelper::map(self::find()->where(['lecture_id' => $id])->asArray()->all(), 'diff_id', 'value');
    }

    /**
     * @return int
     */
    public static function getLectureDifficulty($id): int
    {
        $default = 0;
        $sum = self::find()->where(['lecture_id' => $id])->sum('value');
        return $sum ?? $default;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getLecturesByDifficulty($sum, $returnRandom = false): array
    {
        $sums = self::getLectureSums();
        if ($returnRandom && isset($sums[$sum])) {
            $len = count($sums[$sum]);
            $random = rand(0, $len - 1);
            return [$sums[$sum][$random]];
        }
        return isset($sums[$sum]) ? $sums[$sum] : [];
    }

    public static function getLectureSums()
    {
        $q = 'SELECT DISTINCT lecture_id, SUM(value) as sum FROM `' . self::tableName() . '` GROUP BY lecture_id';
        $data = Yii::$app->db->createCommand($q)->queryAll();
        $sums = [];
        foreach ($data as $d) {
            $sums[$d['sum']][] = $d['lecture_id'];
        }
        return $sums;
    }

    public static function getLecturesByDiff($params = []): array
    {

        $results = [];
        foreach ($params as $diff_id => $value) {
            if ($value) {
                $results[] = ArrayHelper::map(
                    self::find()->where(['diff_id' => $diff_id])
                        ->andWhere(['>=', 'value', $value])
                        ->asArray()->all(),
                    'id',
                    'lecture_id'
                );
            }
        }
        $c = count($results);

        if ($c == 1) {
            $result = $results[0];
        } elseif ($c > 1) {
            $result = call_user_func_array('array_intersect', $results);
        } else {
            $result = [];
        }
        return $result;
    }

    public static function getLecturesDifficulties($ids)
    {
        return ArrayHelper::map(self::find()->where(['in', 'lecture_id', $ids])->asArray()->all(), 'lecture_id', 'diff_id', 'value');
    }

    public static function removeLectureDifficulties($id)
    {
        return static::deleteAll(['lecture_id' => $id]);
    }
}
