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
                            return Users::isCurrentUserTeacher();
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
        $post = Yii::$app->request->post();
        $model = School::getByTeacher(Yii::$app->user->identity->id);
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
        $schoolId = School::getCurrentSchoolId();
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
