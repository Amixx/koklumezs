<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\School;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\SchoolAfterEvaluationMessageForm;
use app\models\SchoolAfterEvaluationMessages;
use yii\web\NotFoundHttpException;

class SchoolAfterEvaluationMessagesController extends Controller
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
        return $this->render('index', [
            'messages' => SchoolAfterEvaluationMessages::getMessagesBySchoolIdGrouped($schoolId)
        ]);
    }

    public function actionCreate($evaluation)
    {
        $userContext = Yii::$app->user->identity;
        $schoolId = $userContext->getSchool()->id;
        $model = new SchoolAfterEvaluationMessages();
        $model->school_id = $schoolId;

        $formModel = new SchoolAfterEvaluationMessageForm();

        $post = Yii::$app->request->post();
        if ($post && $formModel->load($post) && $formModel->validate()) {
            $model->message = $formModel['message'];
            $model->evaluation = $evaluation;

            $model->save();

            return $this->redirect('index');
        }

        return $this->render('create', [
            'model' => $formModel,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = SchoolAfterEvaluationMessages::findOne(['id' => $id]);

        $formModel = new SchoolAfterEvaluationMessageForm();
        $formModel['message'] = $model->message;

        $post = Yii::$app->request->post();

        if ($post && $formModel->load($post) && $formModel->validate()) {
            $model->message = $formModel['message'];
            $model->update();

            return $this->redirect('index');
        }

        return $this->render('update', [
            'model' => $formModel,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = SchoolAfterEvaluationMessages::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
