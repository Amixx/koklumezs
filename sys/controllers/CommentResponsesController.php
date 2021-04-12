<?php

namespace app\controllers;

use app\models\CommentResponses;
use Yii;
use app\models\Userlectureevaluations;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserLectureEvaluationsController implements the CRUD actions for Userlectureevaluations model.
 */
class CommentResponsesController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
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

        $commentResponses = CommentResponses::getCommentResponsesForUser()->asArray()->all();

        $this->view->params['unseen_responses_count'] = CommentResponses::getUnseenCommentsCount();

        CommentResponses::markResponsesAsSeen();

        return $this->render('index', [
            'commentResponses' => $commentResponses
        ]);
    }

    /**
     * Creates a new Userlectureevaluations model.
     * @return mixed
     */
    public function actionCreate()
    {

        $model = new CommentResponses;

        $model->author_id = Yii::$app->user->identity->id;
        $model->userlectureevaluation_id =
            Yii::$app->request->post()['evaluation_id'];
        $model->text = Yii::$app->request->post()['response_text'];
        $model->created = date('Y-m-d H:i:s', time());

        if ($model->save()) {
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
     * Updates an existing Userlectureevaluations model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Userlectureevaluations model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Userlectureevaluations model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Userlectureevaluations the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Userlectureevaluations::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
