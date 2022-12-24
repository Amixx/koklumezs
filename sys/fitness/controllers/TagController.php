<?php

namespace app\fitness\controllers;

use Yii;
use app\fitness\models\Tag;
use app\models\Users;
use app\fitness\models\TagSearch;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class TagController extends Controller
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
        $searchModel = new TagSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('@app/fitness/views/tag/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = Tag::find()->where(['fitness_exercises.id' => $id])->joinWith('sets')->one();
        return $this->render('@app/fitness/views/tag/view', [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        $post = Yii::$app->request->post();
        $model = new Tag();
        $model->author_id = Yii::$app->user->identity->id;

        if ($model->load($post) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('@app/fitness/views/tag/create', [
            'model' => $model,
            'tagTypeSelectOptions' => Tag::TAG_TYPE_SELECT_OPTIONS
        ]);
    }

    public function actionUpdate($id)
    {
        $post = Yii::$app->request->post();
        $model = $this->findModel($id);

        if ($model->load($post) && $model->save()) {
            return $this->redirect(Url::previous());
        }

        Url::remember(Yii::$app->request->referrer);

        return $this->render('@app/fitness/views/tag/update', [
            'model' => $model,
            'tagTypeSelectOptions' => Tag::TAG_TYPE_SELECT_OPTIONS
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionApiList()
    {
        $tags = Tag::find()->asArray()->all();
        return json_encode($tags);
    }

    public function actionApiListTypeSelectOptions()
    {
        return json_encode(Tag::TAG_TYPE_SELECT_OPTIONS);
    }

    protected function findModel($id)
    {
        if (($model = Tag::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
