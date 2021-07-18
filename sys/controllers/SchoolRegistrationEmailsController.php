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
use app\models\SchoolRegistrationEmails;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class SchoolRegistrationEmailsController extends Controller
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
        $schoolId = School::getCurrentSchoolId();
        $schoolRegstrationEmails = SchoolRegistrationEmails::findOne(['school_id' => $schoolId]);

        var_dump($schoolRegstrationEmails->attributes());
        die();

        $schoolRegstrationEmails = [];

        return $this->render('index', [
            'emails' => $schoolRegstrationEmails,
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

    protected function findModel($schoolId)
    {
        if (($model = SchoolRegistrationEmails::findOne(['school_id' => $schoolId])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
