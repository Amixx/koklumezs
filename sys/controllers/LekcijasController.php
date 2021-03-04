<?php

namespace app\controllers;

use app\models\Difficulties;
use app\models\Evaluations;
use app\models\Lectures;
use app\models\LecturesDifficulties;
use app\models\Lecturesevaluations;
use app\models\Lecturesfiles;
use app\models\LectureViews;
use app\models\RelatedLectures;
use app\models\Userlectureevaluations;
use app\models\UserLectures;
use app\models\Users;
use app\models\School;
use app\models\SectionsVisible;
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
            $modelsIds = UserLectures::getLessonsOfType($user->id, $type);

            if ($modelsIds) {
                $query = Lectures::find()->where(['in', 'id', $modelsIds]);
                $countQuery = clone $query;
                $pages = new Pagination(['totalCount' => $countQuery->count()]);

                $SortByDifficulty = Yii::$app->request->get('sortByDifficulty');
                
                if (!(isset($SortByDifficulty)) || $SortByDifficulty == '' || $SortByDifficulty == 'desc') {
                    $sortByDifficulty = 'asc';
                    $orderBy = ['lectures.complexity' => SORT_ASC];
                } else {
                    $sortByDifficulty = 'desc';
                    $orderBy = ['lectures.complexity' => SORT_DESC];
                }

                $models = $query->offset($pages->offset)
                    ->limit($pages->limit)
                    ->orderBy($orderBy)
                    ->all();

                $opened = UserLectures::getOpened($user->id);
                $userLectureEvaluations = Userlectureevaluations::hasLectureEvaluations($user->id);
                $baseUrl = Yii::$app->request->baseUrl;

                $title_filter = Yii::$app->request->get('title_filter');

                if ($title_filter) {
                    $models = array_filter($models, function ($item) use ($title_filter) {
                        $title_lower = mb_strtolower(trim($item->title), 'UTF-8');
                        return strpos($title_lower, $title_filter) !== false;
                    });
                }

                return $this->render('index', [
                    'models' => $models,
                    'type' => $type,
                    'opened' => $opened,
                    'pages' => $pages,
                    'userLectureEvaluations' => $userLectureEvaluations,
                    'baseUrl' => $baseUrl,
                    'videos' => self::VIDEOS,
                    'videoThumb' => $videoThumb,
                    'sortByDifficulty' => $sortByDifficulty,
                    'title_filter' => $title_filter,
                    
                ]);
            }
        } else {
            $latestNewLecturesIds = UserLectures::getLatestLessonsOfType($user->id, "new");
            $latestFavouriteLecturesIds = UserLectures::getLatestLessonsOfType($user->id, "favourite");

            $SortByDifficulty = Yii::$app->request->get('sortByDifficulty');
            if (!(isset($SortByDifficulty)) || $SortByDifficulty == '' || $SortByDifficulty == 'desc') {
                $sortByDifficulty = 'asc';
                $orderBy = ['lectures.complexity' => SORT_ASC];
            } else {
                $sortByDifficulty = 'desc';
                $orderBy = ['lectures.complexity' => SORT_DESC];
            }

            $newLectures = Lectures::find()->where(['in', 'id', $latestNewLecturesIds])->orderBy($orderBy)->all();
            $favouriteLectures = Lectures::find()->where(['in', 'id', $latestFavouriteLecturesIds])->orderBy($orderBy)->all();        

            $opened = UserLectures::getOpened($user->id);
            $userLectureEvaluations = Userlectureevaluations::hasLectureEvaluations($user->id);
            $baseUrl = Yii::$app->request->baseUrl;

            $title_filter = 1;

            return $this->render('overview', [
                'models' => $models,
                'newLectures' => $newLectures,
                'favouriteLectures' => $favouriteLectures,
                'opened' => $opened,
                'pages' => $pages,
                'userLectureEvaluations' => $userLectureEvaluations,
                'baseUrl' => $baseUrl,
                'videos' => self::VIDEOS,
                'videoThumb' => $videoThumb,
                'sortByDifficulty' => $sortByDifficulty,
                'renderRequestButton' => !$user->wants_more_lessons,
            ]);
        }


        return $this->render('index', [
            'models' => $models,
            'pages' => $pages,
        ]);
    }

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

        $SortByDifficulty = Yii::$app->request->get('sortByDifficulty');
        
        if (!(isset($SortByDifficulty)) || $SortByDifficulty == '' || $SortByDifficulty == 'desc') {
            $sortByDifficulty = 'asc';
        } else {
            $sortByDifficulty = 'desc';
        }

        $force = Yii::$app->request->get('force');
        $userLectures = $force ? [] : UserLectures::getLectures($user->id, $SortByDifficulty);
        $modelsIds = $force ? [$id] : UserLectures::getUserLectures($user->id); //UserLectures::getSentUserLectures($user->id)
        $check = in_array($id, $modelsIds);
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

                $shouldRedirect = isset($post['redirect-lesson-id']) && $post['redirect-lesson-id'];
                if($shouldRedirect){
                    $redirectLessonId = $post['redirect-lesson-id'];
                    return $this->redirect(["lekcijas/lekcija/$redirectLessonId"]);
                }

                $this->refresh();
            }

            if (!$force) {
                UserLectures::setSeenByUser($user->id, $id);
            }
            
            $difficulties = Difficulties::getDifficulties();
            $lectureDifficulties = LecturesDifficulties::getLectureDifficulties($id);
            $lectureEvaluations = Lecturesevaluations::getLectureEvaluations($id);
            $lecturefiles = Lecturesfiles::getLectureFiles($id);
            $hasEvaluatedLesson = $difficultyEvaluation !== null;
            $baseUrl = Yii::$app->request->baseUrl;
            $relatedLessonIds = RelatedLectures::getRelations($id);
            $dbUser = Users::findOne([$id => $user->id]);
            $userCanDownloadFiles = $dbUser->allowed_to_download_files;
            $relatedLectures = Lectures::getLecturesByIds($relatedLessonIds);
            $difficultiesVisible = SectionsVisible::isVisible("Nodarbības sarežģītība");

            return $this->render('lekcija', [
                'model' => $model,
                'difficulties' => $difficulties,
                'lectureDifficulties' => $lectureDifficulties,
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
                'sortByDifficulty' => $sortByDifficulty,
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
