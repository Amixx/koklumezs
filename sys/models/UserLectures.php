<?php

namespace app\models;

use app\models\Lectures;
use app\models\Users;
use app\models\School;
use app\models\SchoolLecture;
use Yii;
use yii\helpers\ArrayHelper;
use app\helpers\EmailSender;

class UserLectures extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'userlectures';
    }

    public function rules()
    {
        return [
            [['lecture_id', 'user_id', 'assigned'], 'required'],
            [['lecture_id', 'user_id', 'assigned', 'opened', 'sent', 'weight'], 'integer'],
            [['created', 'opentime', 'sent', 'open_times', 'user_difficulty'], 'safe'],
            [['assigned'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['assigned' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
            [['lecture_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lectures::class, 'targetAttribute' => ['lecture_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lecture_id' => \Yii::t('app',  'Lesson'),
            'user_id' => \Yii::t('app',  'Student'),
            'assigned' => 'Administrators',
            'created' => \Yii::t('app',  'Created'),
            'opened' => \Yii::t('app',  'Opened'),
            'opentime' => \Yii::t('app',  'Opening time'),
            'sent' => \Yii::t('app',  'E-mail sent'),
            'evaluated' => \Yii::t('app',  'Evaluated'),
            'user_difficulty' => \Yii::t('app',  'Abilities'),
            'weight' => \Yii::t('app',  'Weight'),
        ];
    }

    public function getStudent()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id'])
            ->from(['student' => Users::tableName()]);
    }

    public function getAdmin()
    {
        return $this->hasOne(Users::class, ['id' => 'assigned'])
            ->from(['admin' => Users::tableName()]);
    }

    public function getLecture()
    {
        return $this->hasOne(Lectures::class, ['id' => 'lecture_id']);
    }

    public function getRelatedLectures()
    {
        return $this->hasMany(RelatedLectures::class, ['lecture_id' => 'lecture_id']);
    }

    public static function getUserLectures($id, $sent = 1): array
    {
        //not anymore , 'sent' => $sent
        $results = self::find()->where(['user_id' => $id])->asArray()->all(); //, 'evaluated' => 0
        return $results ? ArrayHelper::map($results, 'id', 'lecture_id') : [];
    }

    public static function getSentUserLectures($id, $sent = 1): array
    {
        $results = self::find()->where(['user_id' => $id, 'evaluated' => 0, 'sent' => $sent])->asArray()->orderBy(['created' => SORT_DESC])->all();
        return $results ? ArrayHelper::map($results, 'id', 'lecture_id') : [];
    }

    public static function getEvaluatedUserLectures($id, $sent = 1): array
    {
        $results = self::find()->where(['user_id' => $id, 'evaluated' => 1, 'sent' => $sent])->asArray()->all();
        return $results ? ArrayHelper::map($results, 'id', 'lecture_id') : [];
    }

    public static function getEvaluatedLectures($id): array
    {
        $results = self::find()->where(['user_id' => $id, 'evaluated' => 1])->asArray()->all();
        return $results ? ArrayHelper::map($results, 'id', 'lecture_id') : [];
    }

    public static function getLastEvaluatedLecture($id)
    {
        return self::find()->where(['user_id' => $id, 'evaluated' => 1])->orderBy(['id' => SORT_DESC])->one();
    }

    public static function getLastTenEvaluatedLectures($id, $limit = 10)
    {
        return self::find()->where(['user_id' => $id, 'evaluated' => 1])->orderBy(['id' => SORT_DESC])->limit($limit)->all();
    }

    public static function getLastTenLectures($id, $limit = 10)
    {
        return self::find()->where(['user_id' => $id])->orderBy(['id' => SORT_DESC])->limit($limit)->all();
    }

    public static function getAllLectures($id)
    {
        return self::find()->where(['userlectures.user_id' => $id])
            ->orderBy(['id' => SORT_DESC])
            ->all();
    }

    public static function getDayResult($id, $days = 7)
    {
        $result = 0;
        $data = self::find()->where(['user_id' => $id])->andWhere('created >= DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY)')->orderBy(['id' => SORT_DESC])->all();
        foreach ($data as $d) {
            $result += (int) $d->open_times;
        }
        return $result;
    }

    public static function getLastEvaluatedLectures($ids)
    {
        return self::find()->where(['in', 'user_id', $ids])->andWhere(['evaluated' => 1])->orderBy(['id' => SORT_DESC])->asArray()->all();
    }

    public static function getUserLectureTimes($id): array
    {
        $results = self::find()->where(['user_id' => $id])->asArray()->all();
        return $results ? ArrayHelper::map($results, 'lecture_id', 'created') : [];
    }

    public static function getLastLecturesForUser($id, $limit = 5): array
    {
        $results = self::find()->where(['user_id' => $id])->orderBy(['id' => SORT_DESC])->limit($limit)->asArray()->all();
        return $results ? ArrayHelper::map($results, 'id', 'lecture_id') : [];
    }

    public static function getLectures($id, $orderBy = null, $evaluated = 0)
    {
        $result = self::find()->where(['user_id' => $id, 'evaluated' => $evaluated])->joinWith('lecture');
        if ($orderBy) $result->orderBy($orderBy);
        return $result->all();
    }

    private static function getLessonsOfTypeQuery($userId, $type)
    {
        $query = self::find()->where(['user_id' => $userId, 'sent' => true]);
        $typeCondition = null;

        if ($type == "new") {
            $typeCondition = ['evaluated' => false];
            $query = $query->andWhere(self::getFilterRelatedLecturesCondition($userId, "evaluated = false"));
        } else if ($type == "favourite") {
            $typeCondition = ['is_favourite' => true];
            $query = $query->andWhere(self::getFilterRelatedLecturesCondition($userId, "is_favourite = true"));
        }

        $query = $query->andFilterWhere($typeCondition);
        return $query;
    }

    private static function getFilterRelatedLecturesCondition($userId, $conditionText)
    {
        return "lecture_id NOT IN (SELECT relatedlectures.related_id FROM relatedlectures WHERE relatedlectures.lecture_id IN (SELECT lecture_id FROM userlectures WHERE user_id = $userId AND sent = true AND $conditionText))";
    }

    public static function getLessonsOfType($id, $type, $orderBy)
    {
        return self::getLessonsOfTypeQuery($id, $type)->joinWith("lecture")->orderBy($orderBy);
    }

    public static function getLatestLessonsOfType($id, $type, $condition = null)
    {
        $query = self::getLessonsOfType($id, $type, ['id' => SORT_DESC]);
        if ($condition) $query = $query->andWhere($condition);
        return array_slice($query->all(), 0, 8);
    }

    public static function getUnsentLectures($id, $evaluated = 0, $sent = 0)
    {
        return self::find()->where(['user_id' => $id, 'evaluated' => $evaluated, 'sent' => $sent])->orderBy(['id' => SORT_DESC])->all();
    }

    public static function setSeenByUser($user_id, $id)
    {
        $setOpenTime = false;
        if (($user = Users::findOne($user_id)) !== null) {
            $model = self::find()->where(['opened' => 0, 'user_id' => $user_id, 'lecture_id' => $id])->one();
            if ($model) {
                $model->opened = 1;
                if (!$setOpenTime) {
                    $model->open_times = $model->open_times + 1;
                }
                $model->opentime = date('Y-m-d H:i:s', time());
                $setOpenTime = $model->update();
            } else {
                $model = self::find()->where(['opened' => 1, 'user_id' => $user_id, 'lecture_id' => $id])->one();
                if ($model && !$setOpenTime) {
                    $model->open_times = (int) $model->open_times + 1;
                    $setOpenTime = $model->update();
                }
            }
            $user->last_lecture = $id;
            $user->update();
        }
        return true;
    }

    public static function getOpened($id)
    {
        $results = self::find()->where(['user_id' => $id, 'opened' => 1])->asArray()->all();
        return $results ? ArrayHelper::map($results, 'lecture_id', 'id') : [];
    }

    public static function getNewLectures($user_id, $ids = []): array
    {
        $ids = array_map('intval', $ids);
        $results = [];
        $r = self::find()->where(['user_id' => $user_id])->orderBy(['lecture_id' => SORT_ASC])->all();
        $current = $r ? ArrayHelper::map($r, 'id', 'lecture_id') : $results;
        if ($current) {
            $results = array_diff($ids, $current);
        } else {
            $results = $ids;
        }
        return $results;
    }

    public static function getUserLectureByLectureId($lecture_id)
    {
        $user_id = Yii::$app->user->identity->id;

        return self::findOne(['user_id' => $user_id, 'lecture_id' => $lecture_id]);
    }

    public static function getFirstOpentime($user_id)
    {
        $userLectures = self::find()->where(['user_id' => $user_id])->asArray()->all();
        $opentimes = array_map(function ($ulecture) {
            return $ulecture['opentime'];
        }, $userLectures);

        $firstOpenTime = null;
        foreach ($opentimes as $time) {
            if ($firstOpenTime == null || ($time !== null && $time < $firstOpenTime)) {
                $firstOpenTime = $time;
            }
        }
        return $firstOpenTime;
    }


    public static function getNextLessonId($studentId, $currentUserLecture, $type)
    {
        $userContext = Yii::$app->user->identity;
        $userLectures = self::getLessonsOfType($studentId, $type, ['id' => SORT_ASC])->all();
        if (empty($userLectures)) {
            return null;
        }

        $nextLessonSource = $userLectures;

        $takeNext = false;
        foreach ($nextLessonSource as $userLecture) {
            if ($takeNext) {
                return $userLecture['lecture_id'];
            }

            if ($userLecture["lecture_id"] == $currentUserLecture["lecture_id"]) {
                $takeNext = true;
            }
        }

        if (
            count($nextLessonSource) > 1 && $takeNext
            || $nextLessonSource[0]['lecture_id'] !== $currentUserLecture["lecture_id"]
        ) {
            return $nextLessonSource[0]['lecture_id'];
        }
    }

    public static function sendEmail($id, $subject, $teacherMessage = null)
    {
        $user = Users::findOne([
            'id' => $id,
            'status' => Users::STATUS_ACTIVE,
        ]);
        if ($user === null) {
            return false;
        }

        $school = School::getByStudent($id);

        return EmailSender::sendLessonNotification($user, $teacherMessage, $school['email'], $subject);
    }

    public static function sendAdminEmail($id, $lecture_id, $x)
    {
        $user = Users::findOne([
            'id' => $id,
            'status' => Users::STATUS_ACTIVE,
        ]);
        if ($user === null) {
            return false;
        }

        $school = School::getByStudent($id);
        $lecture = Lectures::findOne($lecture_id);

        return EmailSender::sendReminderToTeacher($user, $lecture, $x, $school['email']);
    }

    public static function getUnassignedLecturesForUser($user)
    {
        $school = School::getByStudent($user->id);
        $assignableSchoolLectures = SchoolLecture::getAssignableSchoolLectureIds($school->id, $user->language);
        $userLectures = self::getUserLectures($user->id);
        $unassigned = [];
        foreach ($assignableSchoolLectures as $slecture) {
            $new = true;
            foreach ($userLectures as $ulecture) {
                if ($slecture == $ulecture) {
                    $new = false;
                    break;
                }
            }
            if ($new) array_push($unassigned, Lectures::findOne($slecture));
        }
        return $unassigned;
    }

    public static function getLastThreeComplexityAverage($id)
    {
        $lastLectureQuery = self::find()
            ->where(['user_id' => $id])
            ->joinWith('lecture')
            ->andWhere(['>', 'complexity', '1'])
            ->orderBy(['id' => SORT_DESC])
            ->limit(3)->all();

        if (count($lastLectureQuery) === 0) return 0;

        $avg = 0;
        foreach ($lastLectureQuery as $lectureQuery) {
            $avg += $lectureQuery['lecture']->complexity;
        }
        $avg = (int)round($avg / count($lastLectureQuery), 0);
        return $avg;
    }

    public static function getNextLessons($userId)
    {
        $allowedToAssign = self::allowedToAssignLessonsToSelf($userId);
        $user = User::findIdentity($userId);
        if (!$user || $user['status'] !== 10 || !$allowedToAssign) {
            return null;
        }

        $unassignedLectures = self::getUnassignedLecturesForUser($user);
        $avg = self::getLastThreeComplexityAverage($userId);
        $nextLessons = ['easy' => null, 'medium' => null, 'hard' => null];

        $similar = 1;
        $challange = 2;
        $rest = 4;

        $easiestMin = $avg - $similar - $rest;
        $hardestMax = $avg + $similar + $challange;
        $avgMin = $avg - $similar;
        $avgMax = $avg + $similar;

        foreach ($unassignedLectures as $lecture) {
            $complexity = $lecture['complexity'];
            if ($complexity === NULL) continue;

            $harderThanEasiest = $complexity > $easiestMin;
            $easierThanSimilar = $complexity < $avgMin;

            $easiestSimilar = $complexity >= $avgMin;
            $hardestSimilar = $complexity <= $avgMax;

            $harderThanSimilar = $complexity > $avgMax;
            $easierThanHardest = $complexity < $hardestMax;

            if (!$easierThanHardest && !$harderThanEasiest) {
                continue;
            }

            $fitsEasier = $harderThanEasiest && $easierThanSimilar;
            $fitsSame = $easiestSimilar && $hardestSimilar;
            $fitsHarder = $harderThanSimilar && $easierThanHardest;

            if ($fitsEasier) {
                if (isset($nextLessons['easy'])) {
                    if ($nextLessons['easy']->complexity < $complexity) $nextLessons['easy'] = $lecture;
                } else $nextLessons['easy'] = $lecture;
            } else if ($fitsSame) {
                if (isset($nextLessons['medium'])) {
                    if ($complexity == $avg) $nextLessons['medium'] = $lecture;
                } else $nextLessons['medium'] = $lecture;
            } else if ($fitsHarder) {
                if (isset($nextLessons['hard'])) {
                    if ($nextLessons['hard']->complexity > $complexity) $nextLessons['hard'] = $lecture;
                } else $nextLessons['hard'] = $lecture;
            }
        }

        return $nextLessons;
    }

    public static function getIsNextLesson($userId)
    {
        $allowedToAssign = self::allowedToAssignLessonsToSelf($userId);
        if (!$allowedToAssign) return false;

        $nextLessons = self::getNextLessons($userId);
        return $nextLessons && ($nextLessons['easy'] != NULL
            || $nextLessons['medium'] != NULL
            || $nextLessons['hard'] != NULL);
    }

    public static function allowedToAssignLessonsToSelf($userId)
    {
        $lastAssignedLessons = UserLectures::find()->where(['user_id' => $userId])->orderBy(['id' => SORT_DESC])->limit(3)->all();
        foreach ($lastAssignedLessons as $lesson) {
            if ($lesson['assigned'] != $userId) {
                return true;
            }
        }

        return false;
    }
}
