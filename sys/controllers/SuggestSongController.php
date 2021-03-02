<?php
namespace app\controllers;

use Yii;
use yii\helpers\Url;
use app\models\Users;
use app\models\Difficulties;
use app\models\School;
use app\models\SchoolTeacher;
use app\models\SuggestSong;
use app\models\SchoolStudent;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\rest\Action;
use yii\web\NotFoundHttpException;
class SuggestSongController extends Controller
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
                    ],
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
     * Lists all Difficulties models.
     * @return mixed
     */
    public function actionIndex()
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $isTeacher = Users::isCurrentUserTeacher();
        $schoolId = School::getCurrentSchoolId();

        $suggestionsDataProvider = new ActiveDataProvider([
            'query' => SuggestSong::find()->where(['school_id' => $schoolId]),
        ]);

        return $this->render('index', [
            'suggestionsDataProvider' => $suggestionsDataProvider,
            'isTeacher' => $isTeacher,
        ]);
    }

    public function actionCreate()
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $model = new SuggestSong();
        $studentId = Yii::$app->user->identity->id;
        $model['student_id'] = $studentId;
        $model['school_id'] = SchoolStudent::getSchoolStudent($studentId)->school_id;
        $model['song'] = Yii::$app->request->post()['song-name'];
        $model['times_suggested'] = 1;
        if($model->save()) {
            Yii::$app->session->setFlash('success', 'Ieteikums nosūtīts!');
        } else {
            Yii::$app->session->setFlash('error', 'Notikusi kļūda! Ieteiku netika nosūtīts!');
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionUpdate($id)
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = SuggestSong::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}