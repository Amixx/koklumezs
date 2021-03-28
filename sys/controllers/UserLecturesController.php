<?php

namespace app\controllers;

use app\models\Difficulties;
use app\models\Lectures;
use app\models\LecturesDifficulties;
use app\models\UserLectures;
use app\models\UserLecturesSearch;
use app\models\Users;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class UserLecturesController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return Users::isAdminOrTeacher(Yii::$app->user->identity->email);
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all UserLectures models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserLecturesSearch();
        $students = Users::getActiveStudentEmails();
        $admins = Users::getAdmins();
        $lectures = Lectures::getLectures();
        $lectureObjects = Lectures::getLecturesObjects();
        $get = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($get);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'students' => $students,
            'admins' => $admins,
            'lectures' => $lectures,
            'lectureObjects' => $lectureObjects,
            'get' => $get,
        ]);
    }

    /**
     * Displays a single UserLectures model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    private static function getLectureDiffs($data)
    {
        $result = [];
        foreach ($data as $id) {
            $result[$id] = LecturesDifficulties::getLectureDifficulties($id);
        }
        return $result;
    }

    /**
     * Creates a new UserLectures model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $outofLectures = false;
        $model = new UserLectures();
        $model->assigned = Yii::$app->user->identity->id;
        $model->created = date('Y-m-d H:i:s', time());
        $post = Yii::$app->request->post();
        $lectures = [];
        $students = Users::getActiveStudentEmails();
        $seasonSelected = $hideParams = false;
        $seasons = Lectures::getSeasons();
        $userLecturesTimes = $selected = $lectureDifficulties = $userLectures = $lastLectures = [];
        $difficulties = Difficulties::getDifficulties();
        if ($post) {
            if (isset($post['UserLectures']['lecture_id']) && $model->load($post) && $model->save()) {
                $model = new UserLectures();
                $model->assigned = Yii::$app->user->identity->id;
                $model->created = date('Y-m-d H:i:s', time());
                $saved = $model->save();
                if ($saved) {
                    $lectureName = Lectures::findOne($model->lecture_id)->title;
                    UserLectures::sendEmail($model->user_id, $lectureName);
                    $model->sent = 1;
                    $model->update();
                }
                return $this->redirect(['index']);
            } elseif (isset($post['UserLectures']['user_id'])) {
                $user = Users::findOne($post['UserLectures']['user_id']);
                $students = [$user->id => $user->email];
                $model->user_id = $user->id;
                $userLecturesTimes = UserLectures::getUserLectureTimes($model->user_id);
                $userLectures = UserLectures::getUserLectures($model->user_id);
                $selected = isset($post['difficulties']) ? $post['difficulties'] : $selected;
                $seasonSelected = isset($post['season']) ? $post['season'] : $seasonSelected;

                if (!empty($selected)) {
                    $diffLecturesIDs = LecturesDifficulties::getLecturesByDiff($selected);
                    //find lectures already assigned for student and intersect with param lectures
                    if ($diffLecturesIDs) {
                        $ids = array_diff($diffLecturesIDs, $userLectures);
                        if (!empty($seasonSelected)) {
                            $lectures = Lectures::getLecturesBySeasonAndIds($ids, $seasonSelected, true);
                        } else {
                            $lectures = Lectures::getLecturesByIds($ids, true);
                        }
                    } else {
                        $lectures = Lectures::getLecturesForUser($userLectures);
                        if (!empty($seasonSelected) && !empty($lectures)) {
                            $ids = array_keys($lectures);
                            $lectures = Lectures::getLecturesBySeasonAndIds($ids, $seasonSelected, true);
                        }
                    }
                } else {
                    $lectures = Lectures::getLecturesForUser($userLectures);
                    if (!empty($seasonSelected) && !empty($lectures)) {
                        $ids = array_keys($lectures);
                        $lectures = Lectures::getLecturesBySeasonAndIds($ids, $seasonSelected, true);
                    }
                }

                $outofLectures = empty($lectures);
                $lectures = !empty($lectures) ? $lectures : [0 => 'Nodarbības netika atrastas'];
                $lastLecturesIds = UserLectures::getLastLecturesForUser($model->user_id);
                $lastLectures = Lectures::getLecturesByIds($lastLecturesIds);
                $lectureDifficulties = self::getLectureDiffs($lastLecturesIds);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'students' => $students,
            'lectures' => $lectures,
            'outofLectures' => $outofLectures,
            'lastLectures' => $lastLectures,
            'userLecturesTimes' => $userLecturesTimes,
            'difficulties' => $difficulties,
            'lectureDifficulties' => $lectureDifficulties,
            'selected' => $selected,
            'hideParams' => $hideParams,
            'seasons' => $seasons,
            'seasonSelected' => $seasonSelected,
        ]);
    }

    /**
     * Updates an existing UserLectures model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $outofLectures = false;
        $post = Yii::$app->request->post();
        $seasons = $userLecturesTimes = $selected = $lectureDifficulties = $lastLectures = [];
        if (isset($post['UserLectures']['lecture_id']) && $model->load($post) && $model->save()) {
            $model->evaluated = isset($post["UserLectures"]["evaluated"]) ? $post["UserLectures"]["evaluated"] : 0;
            $model->update();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $students = Users::getActiveStudentEmails();
        $lectures = Lectures::getLectures();
        $userLecturesTimes = UserLectures::getUserLectureTimes($model->user_id);
        $lastLecturesIds = UserLectures::getLastLecturesForUser($model->user_id);
        $lastLectures = Lectures::getLecturesByIds($lastLecturesIds);
        $difficulties = Difficulties::getDifficulties();
        $lectureDifficulties = self::getLectureDiffs($lastLecturesIds);
        $seasonSelected = $hideParams = true;
        return $this->render('update', [
            'model' => $model,
            'students' => $students,
            'lectures' => $lectures,
            'outofLectures' => $outofLectures,
            'lastLectures' => $lastLectures,
            'userLecturesTimes' => $userLecturesTimes,
            'difficulties' => $difficulties,
            'lectureDifficulties' => $lectureDifficulties,
            'selected' => $selected,
            'hideParams' => $hideParams,
            'seasons' => $seasons,
            'seasonSelected' => $seasonSelected,
        ]);
    }

    /**
     * Deletes an existing UserLectures model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the UserLectures model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserLectures the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserLectures::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
