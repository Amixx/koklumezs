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

/**
 * UserLecturesController implements the CRUD actions for UserLectures model.
 */
class UserLecturesController extends Controller
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
                            return Users::isUserAdmin(Yii::$app->user->identity->email);
                        },
                    ],
                    // everything else is denied
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
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
        $students = Users::getActiveStudents();
        $admins = Users::getAdmins();
        $lectures = Lectures::getLectures();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $get = Yii::$app->request->queryParams;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'students' => $students,
            'admins' => $admins,
            'lectures' => $lectures,
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

    private function getLectureDiffs($data)
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
        $students = Users::getActiveStudents();
        $seasonSelected = $hideParams = false;
        $seasons = Lectures::getSeasons();
        //$lectures = Lectures::getLectures();
        $userLecturesTimes = $selected = $lectureDifficulties = $userLectures = $lastLectures = [];
        $difficulties = Difficulties::getDifficulties();
        if ($post) {
            if (isset($post['UserLectures']['lecture_id']) && $model->load($post) && $model->save()) {
                $sent = self::sendEmail($model->user_id, $model->lecture_id);
                $model->sent = (int) $sent;
                $model->update();
                return $this->redirect(['view', 'id' => $model->id]);
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
                        if (!empty($seasonSelected) and !empty($lectures)) {
                            $ids = array_keys($lectures);
                            $lectures = Lectures::getLecturesBySeasonAndIds($ids, $seasonSelected, true);
                        }
                    }
                } else {
                    $lectures = Lectures::getLecturesForUser($userLectures);
                    if (!empty($seasonSelected) and !empty($lectures)) {
                        $ids = array_keys($lectures);
                        $lectures = Lectures::getLecturesBySeasonAndIds($ids, $seasonSelected, true);
                    }
                }

                $outofLectures = empty($lectures);
                $lectures = !empty($lectures) ? $lectures : [0 => 'Lekcijas netika atrastas'];
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
        $seasons = $userLecturesTimes = $selected = $lectureDifficulties = $userLectures = $lastLectures = [];
        $difficulties = Difficulties::getDifficulties();
        if (isset($post['UserLectures']['lecture_id']) && $model->load($post) && $model->save()) {
            //$sent = self::sendEmail($model->user_id, $model->lecture_id);
            //$model->sent = (int) $sent;
            $model->update();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $students = Users::getActiveStudents();
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

        return $this->redirect(['index']);
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

    /**
     * Sends confirmation email to user
     *
     * @return bool whether the email was sent
     */
    public function sendEmail($id, $lecture_id)
    {
        $user = Users::findOne([
            'id' => $id,
            'status' => Users::STATUS_ACTIVE,
        ]);
        if ($user === null) {
            return false;
        }
        $lecture = Lectures::findOne($lecture_id);
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'lekcija-html', 'text' => 'lekcija-text'],
                ['user' => $user, 'lecture' => $lecture]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($user->email)
            ->setSubject('Jauna lekcija ' . Yii::$app->name)
            ->send();
    }
}
