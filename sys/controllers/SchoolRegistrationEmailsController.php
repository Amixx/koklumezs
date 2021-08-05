<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\School;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\SchoolRegistraionEmailForm;
use app\models\SchoolRegistrationEmails;
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
        return $this->render('index', [
            'emails' => SchoolRegistrationEmails::getMappedForIndex(),
        ]);
    }

    public function actionCreate()
    {
        $schoolId = School::getCurrentSchoolId();
        $newModel = false;
        $model = SchoolRegistrationEmails::findOne(['school_id' => $schoolId]);
        if (!$model) {
            $model = new SchoolRegistrationEmails();
            $model->school_id = $schoolId;
            $newModel = true;
        }

        $possibleEmailTypes = SchoolRegistrationEmails::getLabels();
        foreach ($possibleEmailTypes as $type => $label) {
            if ($model[$type] !== NULL) {
                unset($possibleEmailTypes[$type]);
            }
        }

        $formModel = new SchoolRegistraionEmailForm();

        $post = Yii::$app->request->post();
        if ($post && $formModel->load($post) && $formModel->validate()) {
            $model[$formModel['type']] = $formModel['value'];

            $newModel ? $model->save() : $model->update();

            return $this->redirect('index');
        }

        return $this->render('create', [
            'model' => $formModel,
            'possibleEmailTypes' => $possibleEmailTypes,
        ]);
    }

    public function actionUpdate($type)
    {
        $schoolId = School::getCurrentSchoolId();
        $model = SchoolRegistrationEmails::findOne(['school_id' => $schoolId]);

        $formModel = new SchoolRegistraionEmailForm();
        $formModel['type'] = $type;
        $formModel['value'] = $model[$type];

        $emailTypeLabel = SchoolRegistrationEmails::getLabel($type);

        $post = Yii::$app->request->post();

        if ($post && $formModel->load($post) && $formModel->validate()) {
            $model[$formModel['type']] = $formModel['value'];
            $model->update();

            return $this->redirect('index');
        }

        return $this->render('update', [
            'model' => $formModel,
            'emailType' => $emailTypeLabel,
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
