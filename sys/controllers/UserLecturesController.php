<?php

namespace app\controllers;

use app\models\Lectures;
use app\models\UserLectures;
use app\models\UserLecturesSearch;
use app\models\Users;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * UserLecturesController implements the CRUD actions for UserLectures model.
 */
class UserLecturesController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    // allow authenticated users
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Users::isUserAdmin(Yii::$app->user->identity->email);
                        },
                    ],
                    // everything else is denied
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all UserLectures models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserLecturesSearch();
        $students = Users::getActiveStudents();
        $admins = Users::getAdmins();
        $lectures = Lectures::getLectures();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $get = Yii::$app->request->queryParams;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'students' => $students,
            'admins' => $admins,
            'lectures' => $lectures,
            'get' => $get,
        ]);
    }

    /**
     * Displays a single UserLectures model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new UserLectures model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserLectures();
        $model->assigned = Yii::$app->user->identity->id;
        $model->created = date('Y-m-d H:i:s', time());
        $post = Yii::$app->request->post();
        $lectures = [];
        $students = Users::getActiveStudents();
        //$lectures = Lectures::getLectures();
        
        if ($post) {
            
            if (isset($post['UserLectures']['lecture_id']) && $model->load($post) && $model->save()) {
                $sent = self::sendEmail($model->user_id, $model->lecture_id);
                $model->sent = (int) $sent;
                $model->update();
                return $this->redirect(['view', 'id' => $model->id]);
            } elseif (isset($post['UserLectures']['user_id'])) {
                $user = Users::findOne($post['UserLectures']['user_id']);
                $students = [$user->id => $user->email];
                $model->user_id = $user->id;
                $userLectures = UserLectures::getUserLectures($user->id);
                $lectures = Lectures::getLecturesForUser($userLectures);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'students' => $students,
            'lectures' => $lectures,
        ]);

    }

    /**
     * Updates an existing UserLectures model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $sent = self::sendEmail($model->user_id, $model->lecture_id);
            $model->sent = (int) $sent;
            $model->update();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $students = Users::getActiveStudents();
        $lectures = Lectures::getLectures();
        return $this->render('update', [
            'model' => $model,
            'students' => $students,
            'lectures' => $lectures,
        ]);
    }

    /**
     * Deletes an existing UserLectures model.
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
     * Finds the UserLectures model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserLectures the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserLectures::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Sends confirmation email to user
     *
     * @return bool whether the email was sent
     */
    public function sendEmail($id, $lecture_id)
    {
        $user = Users::findOne([
            'id' => $id,
            'status' => Users::STATUS_ACTIVE,
        ]);
        if ($user === null) {
            return false;
        }
        $lecture = Lectures::findOne($lecture_id);
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'lekcija-html', 'text' => 'lekcija-text'],
                ['user' => $user, 'lecture' => $lecture]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($user->email)
            ->setSubject('Jauna lekcija ' . Yii::$app->name)
            ->send();
    }
}
