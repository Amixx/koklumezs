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
use app\models\LectureViews;
use app\models\RelatedLectures;
use app\models\Studentgoals;
use app\models\Userlectureevaluations;
use app\models\UserLectures;
use app\models\Users;
use app\models\School;
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
                            return !empty(Yii::$app->user->identity);
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
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $models = [];
        $pages = [];
        $user = Yii::$app->user->identity;
        // $modelsIds = UserLectures::getUserLectures($user->id);

        $videoThumb = School::getCurrentSchool()->video_thumbnail;

        if ($type) { 
            $stillLearningLectures = [];
            if ($type == 'new') {
                $stillLearningLectures = UserLectures::getLatestLecturesOfType($user->id, "learning");
            }  
            $modelsIds = UserLectures::getLecturesOfType($user->id, $type) + $stillLearningLectures;
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
                    'videos' => self::VIDEOS,
                    'videoThumb' => $videoThumb
                ]);
            }
        } else {
            $latestNewLecturesIds = UserLectures::getLatestLecturesOfType($user->id, "new");
            $latestStillLearningLecturesIds = UserLectures::getLatestLecturesOfType($user->id, "learning");
            $latestFavouriteLecturesIds = UserLectures::getLatestLecturesOfType($user->id, "favourite");
            $stillLearningLectures = Lectures::find()->where(['in', 'id', $latestStillLearningLecturesIds])->all();
            $newLectures = Lectures::find()->where(['in', 'id', $latestNewLecturesIds])->all() + $stillLearningLectures;
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
                'videos' => self::VIDEOS,
                'videoThumb' => $videoThumb
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
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }

        $model = $this->findModel($id);
        $user = Yii::$app->user->identity;
        $uLecture = null;
        if (Yii::$app->user->identity->user_level == 'Student') {
            $uLecture = UserLectures::findOne(['user_id' => $user->id, 'lecture_id' => $id]);

            $lectureView = new LectureViews;
            $lectureView->user_id = $user->id;
            $lectureView->lecture_id = $id;
            $lectureView->save();
        }

        $videoThumb = School::getCurrentSchool()->video_thumbnail;

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

        $nextLessonId = null;
        $userLecture = UserLectures::findOne(['user_id' => $user->id, 'lecture_id' => $id]);
        if($userLecture){
            $type = $userLecture->is_favourite ? "favourite" : "learning";
            $nextLessonId = UserLectures::getNextLessonId($user->id, $id, $type);
        }

        $difficultyEvaluation = $force ? null : Userlectureevaluations::getLecturedifficultyEvaluation($user->id, $id);

        if ($check) {
            $post = Yii::$app->request->post();
            if(isset($post["difficulty-evaluation"])){
                if($difficultyEvaluation){
                    $difficultyEvaluation->evaluation = $post["difficulty-evaluation"];
                    $difficultyEvaluation->update();
                }else{
                    $difficultyEvaluation = new Userlectureevaluations();
                    $difficultyEvaluation->evaluation_id = 1;
                    $difficultyEvaluation->lecture_id = $model->id;
                    $difficultyEvaluation->user_id = $user->id;
                    $difficultyEvaluation->created = date('Y-m-d H:i:s', time());
                    $difficultyEvaluation->evaluation = $post["difficulty-evaluation"];
                    $difficultyEvaluation->public_comment = false;
                    $difficultyEvaluation->save();

                    $userLecture->evaluated = 1;
                    $userLecture->update();
                }

                $shouldRedirectToNextLesson = isset($post['redirect-to-next']) && $post['redirect-to-next'];
                if($shouldRedirectToNextLesson){
                    return $this->redirect(["lekcijas/lekcija/$nextLessonId"]);
                }

                $this->refresh();
            }

            if (!$force) {
                UserLectures::setSeenByUser($user->id, $id);
            }
            $difficulties = Difficulties::getDifficulties();
            $evaluations = Evaluations::getEvaluations();
            foreach ($evaluations as &$evaluation) {
                if ($evaluation['star_text']) {
                    $starTextArray = unserialize($evaluation['star_text']);
                    foreach ($starTextArray as &$starText) {
                        $starText = Yii::t('app', $starText);
                    };
                    $evaluation['star_text'] = serialize($starTextArray);
                }
            }
            $lectureDifficulties = LecturesDifficulties::getLectureDifficulties($id);
            $lectureHandDifficulties = Lectureshanddifficulties::getLectureDifficulties($id);
            $lectureEvaluations = Lecturesevaluations::getLectureEvaluations($id);
            $lecturefiles = Lecturesfiles::getLectureFiles($id);
            $hasEvaluatedLesson = $difficultyEvaluation !== null;
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
                'evaluations' => $evaluations,
                'lectureDifficulties' => $lectureDifficulties,
                'lectureHandDifficulties' => $lectureHandDifficulties,
                'lectureEvaluations' => $lectureEvaluations,
                'lecturefiles' => $lecturefiles,
                'userLectures' => $userLectures,
                'userEvaluatedLectures' => $userEvaluatedLectures,
                'videos' => self::VIDEOS,
                'docs' => self::DOCS,
                'audio' => self::AUDIO,
                'baseUrl' => $baseUrl,
                'force' => $force,
                'relatedLectures' => $relatedLectures,
                'difficultiesVisible' => $difficultiesVisible,
                'uLecture' => $uLecture,
                'userCanDownloadFiles' => $userCanDownloadFiles,
                'videoThumb' => $videoThumb,
                'nextLessonId' => $nextLessonId,
                'hasEvaluatedLesson' => $hasEvaluatedLesson,
                'difficultyEvaluation' => $difficultyEvaluation,
            ]);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionToggleIsFavourite($lectureId)
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $model = UserLectures::findOne(['lecture_id' => $lectureId, 'user_id' => Yii::$app->user->identity->id]);
        $model->is_favourite = !$model->is_favourite;
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
