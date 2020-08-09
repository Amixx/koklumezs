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
                            return Users::isAdminOrTeacher(Yii::$app->user->identity->email);
                        },
                    ],
                    // everything else is denied
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $options = [];
        $onlyThoseWithoutDontBother = true;
        $users = Users::getActiveStudents($onlyThoseWithoutDontBother);
        $evaluations = [];
        $lectureDifficulties = [];
        $goals = [];
        $videoFrequencies = [];
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

    public function actionUserlectures($id)
    {
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
                $sent = UserLectures::sendEmail($model->user_id, $model->lecture_id);
                $model->sent = (int) $sent;
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
                $sent = UserLectures::sendEmail($model->user_id, $model->lecture_id);
                $model->sent = (int) $sent;
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
        $lastlectures = UserLectures::getLastTenLectures($id); //UserLectures::getLastTenEvaluatedLectures($id);
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
        $filterSubType = array_key_exists("subType", $get) ? $get["subType"] : null;
        $users = Users::getActiveStudentsWithParams($onlyThoseWithoutDontBother, $filterLang, $filterSubType);
        $currentUserId = $user->id;

        $userIds = array_keys($users);
        $currentUserKey = (array_search($currentUserId, $userIds, true));
        $prevUserId = key_exists($currentUserKey - 1, $userIds) ? $userIds[$currentUserKey - 1] : null;
        $nextUserId = key_exists($currentUserKey + 1, $userIds) ? $userIds[$currentUserKey + 1] : null;
        $userCount = count($users);


        $videotexts = unserialize($videoParam->star_text);
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
        $options['filterSubType'] = $filterSubType;
        $options['currentUserIndex'] = $currentUserKey;
        $options['userCount'] = $userCount;
        return $this->render('userlectures', $options);
    }
}
