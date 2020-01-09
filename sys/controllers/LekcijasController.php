<?php

namespace app\controllers;

use app\models\Difficulties;
use app\models\Evaluations;
use app\models\LectureAssignment;
use app\models\Lectures;
use app\models\LecturesDifficulties;
use app\models\Lecturesevaluations;
use app\models\Lecturesfiles;
use app\models\Lectureshanddifficulties;
use app\models\RelatedLectures;
use app\models\Studentgoals;
use app\models\Userlectureevaluations;
use app\models\UserLectures;
use app\models\Users;
use Yii;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * LekcijasController implements the actions for Lectures model by student.
 */
class LekcijasController extends Controller
{
    const VIDEOS = ['mp4', 'mov', 'ogv', 'webm', 'flv', 'avi', 'f4v'];
    const DOCS = ['doc', 'docx', 'pdf'];
    const AUDIO = ['aac', 'alac', 'amr', 'flac', 'mp3', 'opus', 'vorbis', 'ogg', 'wav'];

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
                            return !empty(Yii::$app->user->identity); //Users::isStudent(Yii::$app->user->identity->email);
                        },
                    ],
                    // everything else is denied
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [

                ],
            ],
        ];
    }

    /**
     * Lists all user Lectures models.
     * @return mixed
     */
    public function actionIndex()
    {
        $models = [];
        $archive = [];
        $pages = [];
        $user = Yii::$app->user->identity;
        $modelsIds = UserLectures::getSentUserLectures($user->id);
        $evaluatedIds = UserLectures::getEvaluatedUserLectures($user->id);
        if ($modelsIds or $evaluatedIds) {
            $userLectures = UserLectures::getLectures($user->id);
            $query = Lectures::find()->where(['in', 'id', $modelsIds]);
            $countQuery = clone $query;
            $pages = new Pagination(['totalCount' => $countQuery->count()]);
            $models = $query->offset($pages->offset)
                ->limit($pages->limit)
                ->all();

            if ($evaluatedIds) {
                $archive = Lectures::find()->where(['in', 'id', $evaluatedIds])->all();
            }
            $opened = UserLectures::getOpened($user->id);
            $userLectureEvaluations = Userlectureevaluations::hasLectureEvaluations($user->id);
            $baseUrl = Yii::$app->request->baseUrl;

            return $this->render('index', [
                'models' => $models,
                'userLectures' => $userLectures,
                'opened' => $opened,
                'pages' => $pages,
                'userLectureEvaluations' => $userLectureEvaluations,
                'baseUrl' => $baseUrl,
                'videos' => self::VIDEOS,
                'archive' => $archive,
            ]);
        }

        return $this->render('index', [
            'models' => $models,
            'pages' => $pages,
            'archive' => $archive,
        ]);
    }

    /**
     * Displays a single Lecture model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionLekcija($id)
    {
        $model = $this->findModel($id);
        $user = Yii::$app->user->identity;
        $dbg = Yii::$app->request->get('dbg');
        $force = Yii::$app->request->get('force');
        if ($dbg) {
            $defX = Yii::$app->request->get('x');
            if (is_numeric($defX)) {
                echo 'X: <strong>' . $defX . '</strong><br />';
                $result = LectureAssignment::getNewUserDifficulty($user->id, $defX, $id, $dbg);
                if ($dbg) {
                    echo 'New difficulty:<strong>' . $result . '</strong><br />';
                }
                $data = LectureAssignment::getNewDifficultyIds($result, $defX, $id, $user->id, $dbg);
                echo '<hr />';
            } else {
                for ($x = 1; $x <= 10; $x++) {
                    echo 'X: <strong>' . $x . '</strong><br />';
                    $result = LectureAssignment::getNewUserDifficulty($user->id, $x, $id, $dbg);
                    if ($dbg) {
                        echo 'New difficulty:<strong>' . $result . '</strong><br />';
                    }
                    $data = LectureAssignment::getNewDifficultyIds($result, $x, $id, $user->id, $dbg);
                    echo '<hr />';
                }
            }
            die;
        }
        $modelsIds = $force ? [$id] : UserLectures::getUserLectures($user->id);//UserLectures::getSentUserLectures($user->id)
        $check = in_array($id, $modelsIds);
        $userLectures = $force ? [] : UserLectures::getLectures($user->id);
        $userEvaluatedLectures = $force ? [] : UserLectures::getEvaluatedLectures($user->id);
        if ($check) {
            $post = Yii::$app->request->post();
            if (isset($post['evaluations'])) {
                foreach ($post['evaluations'] as $pid => $value) {
                    $evaluation = new Userlectureevaluations();
                    $evaluation->evaluation_id = $pid;
                    $evaluation->lecture_id = $model->id;
                    $evaluation->user_id = $user->id;
                    $evaluation->created = date('Y-m-d H:i:s', time());
                    $evaluation->evaluation = $value ?? 0;
                    $evaluation->save();
                }
                $param = Evaluations::getScaleParam();
                if (isset($post['evaluations'][$param->id])) {
                    $x = (int) $post['evaluations'][$param->id];
                    if ($x > 0) {
                        $userLecture = UserLectures::findOne(['user_id' => $user->id, 'lecture_id' => $id]);
                        $userLecture->evaluated = 1;
                        $savedEvaluation = $userLecture->save();
                        // find next lecture(s)
                        if ($savedEvaluation) {
                            LectureAssignment::giveNewAssignment($user->id, $x, $id);                            
                        }
                    }
                }
                $videoParam = Evaluations::getVideoParam();
                if (isset($post['evaluations'][$videoParam->id])) {
                    $x = (int) $post['evaluations'][$videoParam->id];
                    // more, more, MORE!!!
                    if (($x == 3) and ($user->more_lecture_requests < Users::MAX_MORE_REQUESTS)) {
                        $gotNew = LectureAssignment::getSameDiffLectures($user->id, true, $dbg);
                        $new = [];
                        $coef = 0;
                        if (empty($gotNew)) {
                            $coef = Studentgoals::getUserDifficultyCoef($user->id);
                            $new = LectureAssignment::giveNewAssignment($user->id,$coef, $id);
                        }
                        if($dbg){
                            echo '</hr>More lectures request';
                            echo 'got same diff<br/>';
                            var_dump($gotNew);
                            echo 'got new lectures diff<br/>';
                            var_dump($new);
                            echo 'user coef diff<br/>';
                            var_dump($coef);
                            echo '</hr>';
                        }
                        $u = Users::findOne($user->id);
                        $u->more_lecture_requests = (int) $u->more_lecture_requests + 1;
                        $u->save(false);                        
                    } else {
                        $u = Users::findOne($user->id);
                        $u->more_lecture_requests = 0;
                        $u->save(false);
                    }
                }
            }
            if (!$force) {
                UserLectures::setSeenByUser($user->id, $id);
            }
            $difficulties = Difficulties::getDifficulties();
            $evaluations = Evaluations::getEvaluations();
            //$handdifficulties = Handdifficulties::getDifficulties();
            $lectureDifficulties = LecturesDifficulties::getLectureDifficulties($id);
            $lectureHandDifficulties = Lectureshanddifficulties::getLectureDifficulties($id);
            $lectureEvaluations = Lecturesevaluations::getLectureEvaluations($id);
            $lecturefiles = Lecturesfiles::getLectureFiles($id);
            $userLectureEvaluations = $force ? [] : Userlectureevaluations::getLectureEvaluations($user->id, $id);
            $baseUrl = Yii::$app->request->baseUrl;
            $ids = RelatedLectures::getRelations($id);
            if ($ids) {
                //filter out lecture that is not assigned to student
                $tmp = [];
                foreach ($ids as $check) {
                    if (in_array($check, $modelsIds)) {
                        $tmp[] = $check;
                    }
                }
                $ids = $tmp;
            }
            $relatedLectures = Lectures::getLecturesByIds($ids);
            return $this->render('lekcija', [
                'model' => $model,
                'difficulties' => $difficulties,
                //'handdifficulties' => $handdifficulties,
                'evaluations' => $evaluations,
                'lectureDifficulties' => $lectureDifficulties,
                'lectureHandDifficulties' => $lectureHandDifficulties,
                'lectureEvaluations' => $lectureEvaluations,
                'lecturefiles' => $lecturefiles,
                'userLectures' => $userLectures,
                'userEvaluatedLectures' => $userEvaluatedLectures,
                'userLectureEvaluations' => $userLectureEvaluations,
                'videos' => self::VIDEOS,
                'docs' => self::DOCS,
                'audio' => self::AUDIO,
                'baseUrl' => $baseUrl,
                'force' => $force,
                'relatedLectures' => $relatedLectures,
            ]);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the Lectures model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Lectures the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Lectures::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
