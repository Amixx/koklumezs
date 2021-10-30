<?php

namespace app\controllers;

use Yii;
use app\models\Difficulties;
use app\models\School;
use app\models\SignupQuestions;
use app\models\SignupQuestionsAnswerChoices;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\Url;

class SignupQuestionsController extends Controller
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

    public function actionCreate()
    {
        $userContext = Yii::$app->user->identity;
        $post = Yii::$app->request->post();
        $model = new SignupQuestions();
        $model->school_id = $userContext->getSchool()->id;

        if ($post) {
            $model->load($post);

            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Question added') . '!');
                if ($model->multiple_choice) {
                    SignupQuestionsAnswerChoices::createFromInputCollection($model->id, $post['answer_choice']);
                }
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Problem encountered! Couldn\'t add question') . '!');
            }

            return $this->redirect(Url::to(['registration-settings/index']));
        }

        $model->multiple_choice = 0;
        $model->allow_custom_answer = 0;

        return $this->render("create", [
            'model' => $model
        ]);
    }


    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['registration-settings/index']);
    }

    protected function findModel($id)
    {
        if (($model = SignupQuestions::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
