<?php

namespace app\controllers;

use Yii;
use yii\helpers\Url;
use app\models\Users;
use app\models\School;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Event;
use yii\web\View;
use app\models\SchoolStudent;
use app\models\CommentResponses;
use app\models\StudentQuestions;
use app\models\Difficulties;
use app\models\SignupQuestions;
use app\models\SchoolFaqs;
use app\models\SchoolTeacher;
use app\models\DifficultiesSearch;
use yii\data\ActiveDataProvider;

class SchoolSettingsController extends Controller
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
                            return Users::isCurrentUserTeacher();
                        }
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

    public function actionIndex()
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByUsername(Yii::$app->user->identity->username);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $settings = School::getSettings(Yii::$app->user->identity->id);
        $schoolId = School::getCurrentSchoolId();
        $signupUrl = Url::base(true) . "/site/sign-up?s=" . $schoolId . "&l=" . Yii::$app->language;
        $difficultiesDataProvider = new ActiveDataProvider([
            'query' => Difficulties::find()->where(['school_id' => $schoolId]),
        ]);
        $faqsDataProvider = new ActiveDataProvider([
            'query' => SchoolFaqs::find()->where(['school_id' => $schoolId]),
        ]);
        $studentQuestionsDataProvider = new ActiveDataProvider([
            'query' => StudentQuestions::find()->where(['school_id' => $schoolId]),
        ]);
        $signupQuestionsDataProvider = new ActiveDataProvider([
            'query' => SignupQuestions::find()->where(['school_id' => $schoolId]),
        ]);
        
        return $this->render('index', [
            'settings' => $settings,
            'difficultiesDataProvider' => $difficultiesDataProvider,
            'faqsDataProvider' => $faqsDataProvider,
            'schoolId' => $schoolId,
            'studentQuestionsDataProvider' => $studentQuestionsDataProvider,
            'signupQuestionsDataProvider' => $signupQuestionsDataProvider,
            'signupUrl' => $signupUrl,
        ]);
    }


    /**
     * Updates an existing SectionsVisible model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate()
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByUsername(Yii::$app->user->identity->username);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $model = new School;
        $post = Yii::$app->request->post();
        $model = School::getByTeacher(Yii::$app->user->identity->id);

        if (count($post) > 0) {
            $model->load($post);

            $saved = $model->save();
            if ($saved) {
                Yii::$app->session->setFlash('success', 'Izmaiņas saglabātas!');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByUsername(Yii::$app->user->identity->username);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
}
