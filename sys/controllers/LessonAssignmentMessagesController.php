<?php

namespace app\controllers;

use app\models\LessonAssignmentMessages;
use Yii;
use app\models\Users;
use app\models\School;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\Url;

class LessonAssignmentMessagesController extends Controller
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
                        'matchCallback' => function () {
                            return Users::isAdminOrTeacher(Yii::$app->user->identity->email);
                        }
                    ],
                    // everything else is denied
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [],
            ],
        ];
    }


    public function actionCreate($lessonId)
    {
        $model = new LessonAssignmentMessages;

        $model->lesson_id = $lessonId;
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $this->returnToLesson($lessonId, 'Automatic assignment message created');
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($lessonId)
    {
        $model = $this->findModel($lessonId);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->returnToLesson($lessonId, 'Automatic assignment message updated');
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($lessonId)
    {
        $this->findModel($lessonId)->delete();

        return $this->returnToLesson($lessonId, 'Automatic assignment message deleted');
    }

    protected function findModel($lessonId)
    {
        if (($model = LessonAssignmentMessages::findOne(['lesson_id' => $lessonId])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function returnToLesson($lessonId, $message)
    {
        Yii::$app->session->setFlash('success', Yii::t('app', $message) . '!');
        return $this->redirect(Url::to(['lectures/update', 'id' => $lessonId]));
    }
}
