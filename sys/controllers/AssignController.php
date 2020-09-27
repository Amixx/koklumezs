<?php

namespace app\controllers;

use app\models\Evaluations;
use app\models\Lectures;
use app\models\LectureAssignment;
use app\models\LecturesDifficulties;
use app\models\Studentgoals;
use app\models\Userlectureevaluations;
use app\models\UserLectures;
use app\models\Users;
use app\models\SchoolLecture;
use app\models\SchoolTeacher;
use app\models\SchoolStudent;
use app\models\School;
use app\models\LectureViews;
use Yii;
use yii\web\Controller;

class AssignController extends \yii\web\Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    // allow authenticated users
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Users::isAdminOrTeacher(Yii::$app->user->identity->username);
                        },
                    ],
                    // everything else is denied
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $isGuest = Yii::$app->user->isGuest;
        $isTeacher = !$isGuest && Yii::$app->user->identity->user_level == 'Teacher';
        $isStudent = !$isGuest && Yii::$app->user->identity->user_level == 'Student';

        $school = null;
        if ($isTeacher) {
            $school = School::getByTeacher(Yii::$app->user->identity->id);
        } else if ($isStudent) {
            $school = School::getByStudent(Yii::$app->user->identity->id);
        }
        Yii::$app->view->params['school'] = $school;
        if (!$isGuest) {
            $currentUser = Users::getByUsername(Yii::$app->user->identity->username);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $options = [];
        $onlyThoseWithoutDontBother = true;
        if (Users::isCurrentUserTeacher()) {
            $users = Users::getStudentsForSchool($onlyThoseWithoutDontBother);
        } else {
            $users = Users::getStudents($onlyThoseWithoutDontBother);
        }

        $evaluations = [];
        $lectureDifficulties = [];
        $goals = [];
        $lastlectures = [];
        $videoParam = Evaluations::getVideoParam();
        $evaluationsTitles = Evaluations::getEvaluationsTitles();
        $evaluationsValues = Evaluations::getEvaluationsValueTexts();
        foreach ($users as $id => $e) {
            $lastlectures[$id] = UserLectures::getLastEvaluatedLecture($id);
            if ($lastlectures[$id]) {
                if (!isset($evaluations[$id])) {
                    $evaluations[$id] = Userlectureevaluations::getLectureEvaluations($id, $lastlectures[$id]['lecture_id']);
                }
                if (!isset($lectureDifficulties[$lastlectures[$id]['lecture_id']])) {
                    $lectureDifficulties[$lastlectures[$id]['lecture_id']] = LecturesDifficulties::getLectureDifficulties($lastlectures[$id]['lecture_id']);
                }
            }
            if (!isset($goals[$id])) {
                $goals[$id] = StudentGoals::getUserGoals($id);
            }
        }

        $videotexts = unserialize($videoParam->star_text);
        $options['videoparamtexts'] = $videotexts;
        $options['goalsnow'] = StudentGoals::NOW;
        $options['users'] = $users;
        $options['goals'] = $goals;
        $options['videoParam'] = $videoParam;
        $options['evaluations'] = $evaluations;
        $options['lastlectures'] = $lastlectures;
        $options['lectureDifficulties'] = $lectureDifficulties;
        $options['evaluationsTitles'] = $evaluationsTitles;
        $options['evaluationsValues'] = $evaluationsValues;
        return $this->render('index', $options);
    }

    public function actionUserlectures($id = null)
    {

        $isGuest = Yii::$app->user->isGuest;
        $isTeacher = !$isGuest && Yii::$app->user->identity->user_level == 'Teacher';
        $isStudent = !$isGuest && Yii::$app->user->identity->user_level == 'Student';

        $school = null;
        if ($isTeacher) {
            $school = School::getByTeacher(Yii::$app->user->identity->id);
        } else if ($isStudent) {
            $school = School::getByStudent(Yii::$app->user->identity->id);
        }
        Yii::$app->view->params['school'] = $school;
        if (!$isGuest) {
            $currentUser = Users::getByUsername(Yii::$app->user->identity->username);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        if ($id == null) {
            $onlyThoseWithoutDontBother = true;
            if (Users::isCurrentUserTeacher()) {
                $users = Users::getStudentsForSchool($onlyThoseWithoutDontBother);
            } else {
                $users = Users::getStudents($onlyThoseWithoutDontBother);
            }
            $id = key($users);
        }

        $post = Yii::$app->request->post();
        $get = Yii::$app->request->get();
        $user = Users::findOne($id);
        $goals = StudentGoals::getUserGoals($id);
        $diff = Studentgoals::getUserDifficulty($id);
        $goalsnow = StudentGoals::NOW;
        $goalsum = isset($goals[$goalsnow]) ? array_sum($goals[$goalsnow]) : 0;
        if ($post) {
            $model = new UserLectures();
            $model->assigned = Yii::$app->user->identity->id;
            $model->created = date('Y-m-d H:i:s', time());
            $model->user_difficulty = $goalsum;
            if (isset($post['UserLectures']['lecture_id']) && $model->load($post) && $model->save()) {
                $lectureName = Lectures::findOne($model->lecture_id)->title;
                $teacherMessage = $post['teacherMessage'];
                $sent = UserLectures::sendEmail($model->user_id, $lectureName, $teacherMessage);
                // $model->sent = (int) $sent;
                $model->sent = 1;
                $model->update();
                return $this->refresh();
            }
        }
        if (isset($get['assign']) and is_numeric($get['assign'])) {
            $model = new UserLectures();
            $model->assigned = Yii::$app->user->identity->id;
            $model->created = date('Y-m-d H:i:s', time());
            $model->user_id = $id;
            $model->lecture_id = $get['assign'];
            $model->user_difficulty = $goalsum;
            $saved = $model->save();
            if ($saved) {
                $lectureName = Lectures::findOne($model->lecture_id)->title;
                $teacherMessage = $post['teacherMessage'];
                $sent = UserLectures::sendEmail($model->user_id, $lectureName, $teacherMessage);
                // $model->sent = (int) $sent;
                $model->sent = 1;
                $model->update();
            }
            return $this->redirect(['assign/userlectures/' . $id]);
        }
        $options = [];
        $evaluations = [];
        $lectureDifficulties = [];
        $videoFrequencies = [];
        $lastlectures = [];
        $videoParam = Evaluations::getVideoParam();
        $evaluationsTitles = Evaluations::getEvaluationsTitles();
        $evaluationsValues = Evaluations::getEvaluationsValueTexts();
        // $lastlectures = UserLectures::getLastTenLectures($id); //UserLectures::getLastTenEvaluatedLectures($id);
        $lastlectures = UserLectures::getAllLectures($id);
        $sevenDayResult = UserLectures::getDayResult($id, 7);
        $thirtyDayResult = UserLectures::getDayResult($id, 30);
        $PossibleThreeLectures = LectureAssignment::getPossibleThreeLectures($id);
        $userLectures = UserLectures::getUserLectures($id);
        $lectures = Lectures::getLecturesObjectsForUser($userLectures);

        if ($lastlectures) {
            foreach ($lastlectures as $lecture) {
                if (!isset($evaluations[$lecture->lecture_id])) {
                    $evaluations[$lecture->lecture_id] = Userlectureevaluations::getLectureEvaluations($id, $lecture->lecture_id);
                }
                if (!isset($lectureDifficulties[$lecture->lecture_id])) {
                    $lectureDifficulties[$lecture->lecture_id] = LecturesDifficulties::getLectureDifficulties($lecture->lecture_id);
                }
            }
        }

        $onlyThoseWithoutDontBother = true;
        $filterLang = array_key_exists("lang", $get) ? $get["lang"] : null;
        $filterSubTypes = (array_key_exists("subTypes", $get) and isset($get["subTypes"]) && $get["subTypes"] !== '') ? explode(",", $get["subTypes"]) : null;
        $users = Users::getStudentsWithParams($onlyThoseWithoutDontBother, $filterLang, $filterSubTypes);

        if (Users::isCurrentUserTeacher()) {
            $currentUserTeacher = SchoolTeacher::getSchoolTeacher(Yii::$app->user->identity->id);
            $schoolLectureIds = SchoolLecture::getSchoolLectureIds($currentUserTeacher->school_id);
            $schoolStudentIds = SchoolStudent::getSchoolStudentIds($currentUserTeacher->school_id);

            $users = array_filter($users, function ($user) use ($schoolStudentIds) {
                return in_array($user["id"], $schoolStudentIds);
            });
            $lectures = array_filter($lectures, function ($lecture) use ($schoolLectureIds) {
                return in_array($lecture["id"], $schoolLectureIds);
            });
        }
        $currentUserId = $user->id;

        $userIds = array_keys($users);

        $currentUserKey = (array_search($currentUserId, $userIds, true));
        $prevUserId = key_exists($currentUserKey - 1, $userIds) ? $userIds[$currentUserKey - 1] : null;
        $nextUserId = key_exists($currentUserKey + 1, $userIds) ? $userIds[$currentUserKey + 1] : null;
        $userCount = count($users);

        $videotexts = unserialize($videoParam->star_text);

        $firstOpenTime = UserLectures::getFirstOpentime($currentUserId);

        $openTimes['seven'] = LectureViews::getDayResult($currentUserId, 7);
        $openTimes['thirty'] = LectureViews::getDayResult($currentUserId, 30);

        $options['id'] = $id;
        $options['videoparamtexts'] = $videotexts;
        $options['goalsum'] = $goalsum;
        $options['goalsnow'] = $goalsnow;
        $options['user'] = $user;
        $options['goals'] = $goals;
        $options['videoParam'] = $videoParam;
        $options['evaluations'] = $evaluations;
        $options['lastlectures'] = $lastlectures;
        $options['lectureDifficulties'] = $lectureDifficulties;
        $options['evaluationsTitles'] = $evaluationsTitles;
        $options['evaluationsValues'] = $evaluationsValues;
        $options['sevenDayResult'] = $sevenDayResult;
        $options['thirtyDayResult'] = $thirtyDayResult;
        $options['PossibleThreeLectures'] = $PossibleThreeLectures;
        $options['manualLectures'] = $lectures;
        $options['model'] = new UserLectures;
        $options['diff'] = $diff;
        $options['prevUserId'] = $prevUserId;
        $options['nextUserId'] = $nextUserId;
        $options['filterLang'] = $filterLang;
        $options['filterSubTypes'] = isset($filterSubTypes) ? implode(",", $filterSubTypes) : null;
        $options['currentUserIndex'] = $currentUserKey;
        $options['userCount'] = $userCount;
        $options['firstOpenTime'] = $firstOpenTime;
        $options['openTimes'] = $openTimes;

        return $this->render('userlectures', $options);
    }
}
