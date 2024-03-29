<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\Difficulties;
use app\models\School;
use app\models\StartLaterCommitments;
use app\models\Trials;
use app\models\Userlectureevaluations;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;

class RecentlyRegisteredStudentsController extends Controller
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

        $users = Users::find()
            ->where(['is_deleted' => false, 'user_level' => Users::ROLE_USER, 'schoolstudents.school_id' => $schoolId])
            ->andWhere(['in', 'users.status', [Users::STATUS_ACTIVE, Users::STATUS_PASSIVE]])
            ->joinWith('schoolStudent')
            ->orderBy(['id' => SORT_DESC])->all();
        $sections = [
            'waitingForInstrument' => [
                'title' => Yii::t('app', 'Students waiting for a kokle'),
                'users' => [],
            ],
            'willStartLater' => [
                'title' => Yii::t('app', 'Students who are committed to starting later'),
                'users' => [],
                'dateColText' => 'Ieplānotais sākuma datums',
            ],
            'firstLessonsNotEvaluated' => [
                'title' => Yii::t('app', 'Students who have not yet evaluated the first lessons'),
                'users' => [],
                'dateColText' => 'Reģistrācijas datums un laiks',
            ],
        ];

        foreach ($users as $u) {
            if (!$u['schoolStudent']) continue;
            $ss = $u['schoolStudent'];

            if ($ss['signed_up_to_rent_instrument'] && !$ss['has_instrument']) {
                $sections['waitingForInstrument']['users'][] = $u;
            } else if (!$ss['show_real_lessons']) {
                $commmitment = StartLaterCommitments::find()->where(['user_id' => $u['id']])->one();

                if ($commmitment && !$commmitment['chosen_period_started']) {
                    $sections['willStartLater']['users'][] = [
                        'first_name' => $u['first_name'],
                        'last_name' => $u['last_name'],
                        'email' => $u['email'],
                        'date' => $commmitment['start_date']
                    ];
                }
            } else if ($ss['show_real_lessons'] && !Userlectureevaluations::hasAnyLegitEvaluations($u['id']) && $u['id'] > 1000) {
                $sections['firstLessonsNotEvaluated']['users'][] = [
                    'first_name' => $u['first_name'],
                    'last_name' => $u['last_name'],
                    'email' => $u['email'],
                    'date' => $ss['created']
                ];
            }
        }


        return $this->render('index', [
            'sections' => $sections,
        ]);
    }
}
