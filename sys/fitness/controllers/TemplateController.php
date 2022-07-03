<?php

namespace app\fitness\controllers;

use Yii;
use app\fitness\models\Template;
use app\fitness\models\TemplateExerciseSet;
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
                foreach ($post['templateExerciseSets'] as $tempEx) {
                    $templateExerciseSet = new TemplateExerciseSet;
                    $templateExerciseSet->template_id = $template->id;
                    $templateExerciseSet->exerciseset_id = $tempEx['exerciseSet']['id'];
                    $templateExerciseSet->weight = $tempEx['weight'];
                    $templateExerciseSet->save();
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

            foreach ($post['templateExerciseSets'] as $postTempEx) {
                if (!isset($postTempEx['id'])) { // new exercise set
                    $templateExerciseSet = new TemplateExerciseSet;
                    $templateExerciseSet->template_id = $model->id;
                    $templateExerciseSet->exerciseset_id = $postTempEx['exerciseSet']['id'];
                    $templateExerciseSet->weight = $postTempEx['weight'];
                    $templateExerciseSet->save();
                }
            }

            foreach ($model->templateExerciseSets as $tempExSet) {
                $tempExDeleted = true;
                $tempExSetModel = TemplateExerciseSet::findOne($tempExSet['id']);

                foreach ($post['templateExerciseSets'] as $postTempEx) {
                    if (!isset($postTempEx['id'])) {
                        $tempExDeleted = false;
                    } else if ($tempExSet['id'] == $postTempEx['id']) {
                        $tempExDeleted = false;
                        $tempExSetModel->weight = $postTempEx['weight'];
                        $tempExSetModel->update();
                    }
                }
                if ($tempExDeleted) $tempExSetModel->delete();
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
        $templates = Template::find()->joinWith('templateExerciseSets')->asArray()->all();
        return json_encode($templates);
    }

    public function actionApiGet($id)
    {
        $model = Template::find()->where(['fitness_templates.id' => $id])->joinWith('templateExerciseSets')->asArray()->one();
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
