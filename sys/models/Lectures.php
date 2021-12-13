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
            [['title', 'description', 'file', 'play_along_file', 'lang'], 'string'],
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
            'lang' => Yii::t('app', 'Language'),
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

    public function getLectureDifficulties()
    {
        return $this->hasMany(LecturesDifficulties::class, ['lecture_id' => 'id']);
    }

    public function getAssignmentMessage()
    {
        return $this->hasOne(LessonAssignmentMessages::class, ['lesson_id' => 'id']);
    }

    public function getUserLectureEvaluations()
    {
        return $this->hasMany(Userlectureevaluations::class, ['lecture_id' => 'id']);
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
        $data = self::find()->joinWith("assignmentMessage")->asArray()->all();
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
                    'complexity' => $d['complexity'],
                    'assignmentMessage' => $d['assignmentMessage'],
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

    /**
     * Return status text for lesson, like 'New', 'Play me', 'Rate me'
     * @param integer $lectureId
     * @return array ['text' => ... , 'class' => '']
     */
    public static function getLectureStatus($lectureId): array
    {
        $userId = Yii::$app->user->identity->id;
        $userLecture = UserLectures::findOne(['lecture_id' => $lectureId, 'user_id' => $userId]);

        if ($userLecture->opened) {
            return [
                'text' => 'Rate me',
                'class' => 'bg-blue'
            ];
        }

        $created_at = new \DateTime($userLecture->created);
        $now = new \DateTime();
        $diff = $now->diff($created_at);

        if ($diff->days > 10) {
            return [
                'text' => 'Play me',
                'class' => 'bg-purple'
            ];
        }
        return [
            'text' => 'New',
            'class' => 'bg-yellow'
        ];
    }

    public static function getLessonIdOfSimilarDifficulty($lessonId)
    {
        $userContext = Yii::$app->user->identity;
        $schoolId = $userContext->getSchool()->id;
        $assignedIds = UserLectures::getUserLectures($userContext->id);

        $schoolLessons = SchoolLecture::find()
            ->where(['school_id' => $schoolId])
            ->andWhere(['not in', 'lectures.id', $assignedIds])
            ->andWhere(['>', 'complexity', 0])
            ->orWhere(['lectures.id' => $lessonId])
            ->joinWith("lecture")
            ->orderBy('complexity')->asArray()->all();
        $res = null;

        foreach ($schoolLessons as $key => $schoolLesson) {
            if ($schoolLesson["lecture"]["id"] == $lessonId) {

                $currentComplexity = (int)$schoolLesson["lecture"]["complexity"];
                $lessComplexLesson = isset($schoolLessons[$key - 1]) ? $schoolLessons[$key - 1]["lecture"] : null;
                $moreComplexLesson = isset($schoolLessons[$key + 1]) ? $schoolLessons[$key + 1]["lecture"] : null;

                if ($lessComplexLesson) {
                    if ($moreComplexLesson) {
                        $res = 2 * $currentComplexity > (int)$lessComplexLesson["complexity"] + (int)$moreComplexLesson["complexity"]
                            ? $lessComplexLesson["id"]
                            : $moreComplexLesson["id"];
                    } else {
                        $res = $lessComplexLesson["id"];
                    }
                } else if ($moreComplexLesson) {
                    $res = $moreComplexLesson["id"];
                }

                break;
            }
        }

        return $res;
    }
}
