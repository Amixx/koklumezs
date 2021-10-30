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

class RegistrationSettingsController extends Controller
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
        $userContext = Yii::$app->user->identity;
        $schoolId = $userContext->getSchool()->id;
        $signupQuestionsDataProvider = new ActiveDataProvider([
            'query' => SignupQuestions::find()->where(['school_id' => $schoolId])->joinWith('answerChoices'),
        ]);

        return $this->render('index', [
            'schoolId' => $schoolId,
            'signupQuestionsDataProvider' => $signupQuestionsDataProvider,
        ]);
    }
}
