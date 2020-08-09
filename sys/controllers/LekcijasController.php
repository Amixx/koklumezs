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
use app\models\SectionsVisible;
use app\models\CommentResponses;
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
                'actions' => [],
            ],
        ];
    }

    /**
     * Lists all user Lectures models.
     * @return mixed
     */
    public function actionIndex($type = null)
    {
        $models = [];
        $pages = [];
        $user = Yii::$app->user->identity;
        // $modelsIds = UserLectures::getUserLectures($user->id);
        if ($type) {
            $modelsIds = UserLectures::getLecturesOfType($user->id, $type);
            if ($modelsIds) {
                $query = Lectures::find()->where(['in', 'id', $modelsIds]);
                $countQuery = clone $query;
                $pages = new Pagination(['totalCount' => $countQuery->count()]);
                $models = $query->offset($pages->offset)
                    ->limit($pages->limit)
                    ->all();

                //sakārtojas pēc lekcijas piešķiršanas datuma
                usort($models, function ($a, $b) use ($modelsIds) {
                    $keyA = array_search($a['id'], $modelsIds);
                    $keyB = array_search($b['id'], $modelsIds);
                    return $keyA < $keyB;
                });

                $opened = UserLectures::getOpened($user->id);
                $userLectureEvaluations = Userlectureevaluations::hasLectureEvaluations($user->id);
                $baseUrl = Yii::$app->request->baseUrl;

                return $this->render('index', [
                    'models' => $models,
                    'type' => $type,
                    'opened' => $opened,
                    'pages' => $pages,
                    'userLectureEvaluations' => $userLectureEvaluations,
                    'baseUrl' => $baseUrl,
                    'videos' => self::VIDEOS
                ]);
            }
        } else {
            $latestNewLecturesIds = UserLectures::getLatestLecturesOfType($user->id, "new");
            $latestStillLearningLecturesIds = UserLectures::getLatestLecturesOfType($user->id, "learning");
            $latestFavouriteLecturesIds = UserLectures::getLatestLecturesOfType($user->id, "favourite");
            $newLectures = Lectures::find()->where(['in', 'id', $latestNewLecturesIds])->all();
            $stillLearningLectures = Lectures::find()->where(['in', 'id', $latestStillLearningLecturesIds])->all();
            $favouriteLectures = Lectures::find()->where(['in', 'id', $latestFavouriteLecturesIds])->all();
            $opened = UserLectures::getOpened($user->id);
            $userLectureEvaluations = Userlectureevaluations::hasLectureEvaluations($user->id);
            $baseUrl = Yii::$app->request->baseUrl;

            return $this->render('overview', [
                'models' => $models,
                'newLectures' => $newLectures,
                'stillLearningLectures' => $stillLearningLectures,
                'favouriteLectures' => $favouriteLectures,
                'opened' => $opened,
                'pages' => $pages,
                'userLectureEvaluations' => $userLectureEvaluations,
                'baseUrl' => $baseUrl,
                'videos' => self::VIDEOS
            ]);
        }


        return $this->render('index', [
            'models' => $models,
            'pages' => $pages
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
        $uLecture = null;
        if (Yii::$app->user->identity->user_level == 'Student') {
            $uLecture = UserLectures::findOne(['user_id' => $user->id, 'lecture_id' => $id]);
        }
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
        $modelsIds = $force ? [$id] : UserLectures::getUserLectures($user->id); //UserLectures::getSentUserLectures($user->id)
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
                            //Katru reizi kad skolnieks novērtē uzdevumu, viņam tiek izraudzīts jauns uzdevums. Tā NEVAJADZĒTU BŪT. 
                            //LectureAssignment::giveNewAssignment($user->id, $x, $id);                            
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
                            $spam = true;
                            $coef = Studentgoals::getUserDifficultyCoef($user->id);
                            $new = LectureAssignment::giveNewAssignment($user->id, $coef, $id, $spam);
                        }
                        if ($dbg) {
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
                        /* more_lecture_requests = 0 update now is located in croncontroller
                        $u = Users::findOne($user->id);
                        $u->more_lecture_requests = 0;
                        $u->save(false);
                        */
                    }
                }
                if (isset($post["add-to-favourites"])) {
                    $uLecture->is_favourite = $post["add-to-favourites"];
                    $uLecture->update();
                }
                if (isset($post["add-to-still-learning"])) {
                    $uLecture->still_learning = $post["add-to-still-learning"];
                    $uLecture->update();
                }
                $this->refresh();
            }

            if (!$force) {
                UserLectures::setSeenByUser($user->id, $id);
            }
            $difficulties = Difficulties::getDifficulties();
            $difficultiesEng = array();
            foreach ($difficulties as $name) {
                $engName = "";
                switch ($name) {
                    case "Ritms":
                        $engName = "rhythm";
                        break;

                    case "Labā roka":
                        $engName = "right hand";
                        break;

                    case "Kreisā roka":
                        $engName = "left hand";
                        break;

                    case "Akordu maiņas biežums":
                        $engName = "chord change frequency";
                        break;

                    case "Akordi":
                        $engName = "chords";
                        break;

                    default:
                        break;
                }

                array_push($difficultiesEng, $engName);
            }
            $evaluations = Evaluations::getEvaluations();
            foreach ($evaluations as &$ev) {
                if ($ev['title'] === "Uzdevuma sarežģītība") {
                    $ev['titleEng'] = "Task difficulty";
                } else if ($ev['title'] === "Uzdevumu daudzums") {
                    $ev['titleEng'] = "Amount of tasks";
                } else if ($ev['title'] === "Komentāri") {
                    $ev['titleEng'] = "Comments";
                } else if ($ev['title'] = "Kādu dziesmu vēlētos nākamo") {
                    $ev['titleEng'] = "What song would you like next";
                }
            }
            function toEng($starText)
            {
                $engText = "";
                switch ($starText) {
                    case "Viss tik viegls, ka garlaicīgi":
                        $engText = "Super easy, boring";
                        break;
                    case "Ļoti ļoti viegli, noteikti vajag grūtāk":
                        $engText = "Very easy";
                        break;
                    case "Izspēlēju vienu reizi un jau viss skaidrs":
                        $engText = "Easy";
                        break;
                    case "Diezgan vienkārši":
                        $engText = "Quite easy";
                        break;
                    case "Nācās pastrādāt, bet tiku galā bez milzīgas piepūles":
                        $engText = "Doable";
                        break;
                    case "Tiku galā":
                        $engText = "Perfect";
                        break;
                    case "Diezgan grūti":
                        $engText = "Challenging";
                        break;
                    case "Itkā saprotu, bet pirksti neklausa":
                        $engText = "Hard";
                        break;
                    case "Kaut ko mēģinu, bet pārāk nesanāk":
                        $engText = "Very hard";
                        break;
                    case "Vispār neko nesaprotu":
                        $engText = "Impossible";
                        break;

                    case "Par daudz, vajadzētu mazāk":
                        $engText = "Too much";
                        break;
                    case "Tieši tik daudz ir labi":
                        $engText = "Perfect amount";
                        break;
                    case "Par maz, vajadzētu vairāk":
                        $engText = "Too little";
                        break;

                    case "Vajadzētu mazāk, bija par daudz":
                        $engText = "Too much";
                        break;
                    case "Ideāli, tā turpināt":
                        $engText = "Perfect amount";
                        break;
                    case "Bija par maz, dodiet uz nākamo reizi vairāk":
                        $engText = "Too little";
                        break;
                }

                return $engText;
            };
            foreach ($evaluations as &$evaluation) {
                if ($evaluation['star_text']) {
                    $starTextArray = unserialize($evaluation['star_text']);
                    foreach ($starTextArray as &$starText) {
                        $starText = $starText . "/" . toEng($starText);
                    };
                    $evaluation['star_text'] = serialize($starTextArray);
                }
            }
            //$handdifficulties = Handdifficulties::getDifficulties();
            $lectureDifficulties = LecturesDifficulties::getLectureDifficulties($id);
            $lectureHandDifficulties = Lectureshanddifficulties::getLectureDifficulties($id);
            $lectureEvaluations = Lecturesevaluations::getLectureEvaluations($id);
            $lecturefiles = Lecturesfiles::getLectureFiles($id);
            $userLectureEvaluations = $force ? [] : Userlectureevaluations::getLectureEvaluations($user->id, $id);
            $userComments = [];
            if (SectionsVisible::isVisible("Komentāri")) {
                $userComments = Userlectureevaluations::getComments($id);
                foreach ($userComments as &$comment) {
                    if ($comment['id']) {
                        $comment['responses'] = CommentResponses::getCommentResponses($comment['id']);
                    }
                }
            }
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

            $dbUser = Users::findOne([$id => $user->id]);
            $userCanDownloadFiles = $dbUser->allowed_to_download_files;
            $relatedLectures = Lectures::getLecturesByIds($ids);
            $difficultiesVisible = SectionsVisible::isVisible("Nodarbības sarežģītība");
            return $this->render('lekcija', [
                'model' => $model,
                'difficulties' => $difficulties,
                'difficultiesEng' => $difficultiesEng,
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
                'difficultiesVisible' => $difficultiesVisible,
                'comments' => $userComments,
                'uLecture' => $uLecture,
                'userCanDownloadFiles' => $userCanDownloadFiles
            ]);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionToggleIsFavourite($lectureId)
    {
        $model = UserLectures::findOne(['lecture_id' => $lectureId, 'user_id' => Yii::$app->user->identity->id]);
        $model->is_favourite = !$model->is_favourite;
        $model->update();
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionToggleStillLearning($lectureId)
    {
        $model = UserLectures::findOne(['lecture_id' => $lectureId, 'user_id' => Yii::$app->user->identity->id]);
        $model->still_learning = !$model->still_learning;
        $model->update();
        return $this->redirect(Yii::$app->request->referrer);
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
