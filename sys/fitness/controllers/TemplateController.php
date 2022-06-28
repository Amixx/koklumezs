<?php

namespace app\fitness\controllers;

use Yii;
use app\fitness\models\Template;
use app\fitness\models\TemplateExercise;
use app\models\Users;
use app\fitness\models\TemplateSearch;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class TemplateController extends Controller
{
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
                            return Users::isAdminOrTeacher(Yii::$app->user->identity->email);
                        },
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
        $searchModel = new TemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('@app/fitness/views/template/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('@app/fitness/views/template/view', [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        $post = Yii::$app->request->post();

        if ($post) {
            $template = new Template;
            $template->author_id = Yii::$app->user->identity->id;
            $template->title = $post['title'];
            $template->description = $post['description'];
            if ($template->save()) {
                foreach ($post['tempExercises'] as $tempEx) {
                    $templateExercise = new TemplateExercise;
                    $templateExercise->template_id = $template->id;
                    $templateExercise->exercise_id = $tempEx['exercise']['id'];
                    $templateExercise->weight = $tempEx['weight'];
                    $templateExercise->reps = $tempEx['reps'];
                    $templateExercise->save();
                }
            }
        }

        return $this->render('@app/fitness/views/template/create');
    }

    public function actionUpdate($id)
    {
        $post = Yii::$app->request->post();
        $model = $this->findModel($id);


        if ($post) {
            $model->title = $post['title'];
            $model->description = $post['description'];
            $model->update();
            foreach ($model->templateExercises as $tempEx) {
                $tempExDeleted = true;
                $tempExModel = TemplateExercise::findOne($tempEx['id']);

                foreach ($post['tempExercises'] as $postTempEx) {
                    if ($tempEx['id'] == $postTempEx['id']) {
                        $tempExDeleted = false;
                        $tempExModel->weight = $postTempEx['weight'];
                        $tempExModel->reps = $postTempEx['reps'];
                        $tempExModel->update();
                    }
                }

                if ($tempExDeleted) $tempExModel->delete();
            }
        }


        return $this->render('@app/fitness/views/template/update', [
            'templateId' => $id,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionApiList()
    {
        $templates = Template::find()->joinWith('templateExercises')->asArray()->all();
        return json_encode($templates);
    }

    public function actionApiGet($id)
    {
        $model = Template::find()->where(['fitness_templates.id' => $id])->joinWith('templateExercises')->asArray()->one();
        return json_encode($model);
    }

    protected function findModel($id)
    {
        if (($model = Template::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
