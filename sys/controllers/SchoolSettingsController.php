<?php

namespace app\controllers;

use app\models\BankAccounts;
use Yii;
use yii\helpers\Url;
use app\models\Users;
use app\models\School;
use yii\web\Controller;
use yii\filters\VerbFilter;
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
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return Yii::$app->user->identity->isTeacher();
                        }
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

    public function actionIndex()
    {
        $settings = School::getSettings();
        $userContext = Yii::$app->user->identity;
        $schoolId = $userContext->getSchool()->id;
        $signupUrl = Url::base(true) . "/registration/index?s=" . $schoolId . "&l=" . Yii::$app->language;
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
        $bankAccount = School::getBankAccount($schoolId);

        return $this->render('index', [
            'settings' => $settings,
            'difficultiesDataProvider' => $difficultiesDataProvider,
            'faqsDataProvider' => $faqsDataProvider,
            'bankAccount' => $bankAccount,
            'schoolId' => $schoolId,
            'signupQuestionsDataProvider' => $signupQuestionsDataProvider,
            'signupUrl' => $signupUrl,
            'loginUrl' => $loginUrl,
        ]);
    }

    public function actionUpdate()
    {
        $userContext = Yii::$app->user->identity;
        $post = Yii::$app->request->post();
        $model = $userContext->getSchool();
        $schoolSubPlans = SchoolSubPlans::getMappedForSelection();

        if (count($post) > 0) {
            $model->load($post);

            $saved = $model->save();
            if ($saved) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Changes saved') . '!');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'schoolSubPlans' => $schoolSubPlans,
        ]);
    }

    public function actionBankUpdate()
    {
        $post = Yii::$app->request->post();
        $userContext = Yii::$app->user->identity;
        $schoolId = $userContext->getSchool()->id;
        $model = BankAccounts::getCurrentSchoolsBankAccount($schoolId);
        $bankAccount = School::getBankAccount($schoolId);

        if (count($post) > 0) {
            $model->load($post);
            $saved = $model->save();
            if ($saved) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Changes saved') . '!');
                return $this->redirect(['index']);
            }
        }

        return $this->render('bank-update', [
            'model' => $model,
            'bankAccount' => $bankAccount
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
}
