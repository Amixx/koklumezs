<?php

namespace app\controllers;

use Yii;
use yii\helpers\Url;
use app\models\Users;
use app\models\School;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\StudentQuestions;
use app\models\Difficulties;
use app\models\SignupQuestions;
use app\models\SchoolSubPlans;
use app\models\SchoolFaqs;
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
                'class' => \yii\filters\AccessControl::class,
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
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $settings = School::getSettings(Yii::$app->user->identity->id);
        $schoolId = School::getCurrentSchoolId();
        $signupUrl = Url::base(true) . "/site/sign-up?s=" . $schoolId . "&l=" . Yii::$app->language;
        $loginUrl = Url::base(true) . "/site/login?s=" . $schoolId . "&l=" . Yii::$app->language;
        $difficultiesDataProvider = new ActiveDataProvider([
            'query' => Difficulties::find()->where(['school_id' => $schoolId]),
        ]);
        $faqsDataProvider = new ActiveDataProvider([
            'query' => SchoolFaqs::find()->where(['school_id' => $schoolId]),
        ]);
        $signupQuestionsDataProvider = new ActiveDataProvider([
            'query' => SignupQuestions::find()->where(['school_id' => $schoolId]),
        ]);

        return $this->render('index', [
            'settings' => $settings,
            'difficultiesDataProvider' => $difficultiesDataProvider,
            'faqsDataProvider' => $faqsDataProvider,
            'schoolId' => $schoolId,
            'signupQuestionsDataProvider' => $signupQuestionsDataProvider,
            'signupUrl' => $signupUrl,
            'loginUrl' => $loginUrl,
        ]);
    }

    public function actionUpdate()
    {
        $model = new School;
        $post = Yii::$app->request->post();
        $model = School::getByTeacher(Yii::$app->user->identity->id);
        $schoolSubPlans = SchoolSubPlans::getMappedForSelection();

        if (count($post) > 0) {
            $model->load($post);

            $saved = $model->save();
            if ($saved) {
                Yii::$app->session->setFlash('success', 'Izmaiņas saglabātas!');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'schoolSubPlans' => $schoolSubPlans,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
}
