<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\RentForm;
use app\models\Lectures;
use app\models\SchoolTeacher;
use app\models\SignupQuestions;
use app\models\Evaluations;
use app\models\Lecturesevaluations;
use app\models\SignUpForm;
use app\models\SchoolStudent;
use app\models\School;
use app\models\RegistrationLesson;
use app\models\Users;
use app\models\StudentSubplans;
use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;
use app\models\ResendVerificationEmailForm;
use app\models\VerifyEmailForm;
use app\helpers\EmailSender;
use app\helpers\InvoiceManager;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $email = isset(Yii::$app->user->identity->email) ? Yii::$app->user->identity->email : null;

        if (Users::isAdminOrTeacher($email)) {
            return $this->redirect(['/lectures']);
        } elseif (Users::isStudent($email)) {
            return $this->redirect(['/lekcijas']);
        } else {
            return $this->redirect(['/site/login']);
        }
    }

    public function actionRequestPasswordReset()
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Pārbaudiet savu e-pastu, lai turpinātu paroles atjaunošanas procesu.');
                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Neizdevās nosūtīt e-pasta adresi, lai atjaunotu paroli.');
            }
        }
        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Jaunā parole saglabāta.');
            return $this->goHome();
        }
        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionVerifyEmail($token)
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        
        if ($user = $model->verifyEmail() && Yii::$app->user->login($user)) {
            Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
            return $this->goHome();
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    public function actionResendVerificationEmail()
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }
        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }

    public function actionLogin()
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionSignUp($s, $l)
    {
        $this->layout = '@app/views/layouts/signup';
        if(!Yii::$app->user->isGuest) Yii::$app->user->logout();

        Yii::$app->language = $l;

        $school = School::findOne($s);

        $model = new SignUpForm();
     
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model['language'] = $l;
            $userId = $model->signUp();

            if($userId){
                $schoolStudent = new SchoolStudent;

                $schoolStudent->school_id = $s;
                $schoolStudent->user_id = $userId;

                $saved = $schoolStudent->save();

                if($saved){
                    $user = Users::findById($userId);
                    Yii::$app->user->login($user);

                    $schoolTeacher = SchoolTeacher::getBySchoolId($s)["user"];
                    $firstLectureIds = RegistrationLesson::getLessonIds($school['id'], $model->ownsInstrument, $model->hasExperience);
                    $insertDate = date('Y-m-d H:i:s', time());
                    $insertColumns = [];

                    foreach($firstLectureIds as $lid){
                        $insertColumns[] = [$schoolTeacher["id"], $userId, $lid, $insertDate, 0, 0, 1];
                    }

                    Yii::$app->db
                        ->createCommand()
                        ->batchInsert('userlectures', ['assigned', 'user_id', 'lecture_id', 'created', 'user_difficulty', 'open_times', 'sent'], $insertColumns)
                        ->execute();

                    EmailSender::sendNewStudentNotification($user, $school['email']);

                    if($school['registration_message'] != null && $model->ownsInstrument){
                        EmailSender::sendPostSignupMessage($school['registration_message'], $school['email'], $user['email']);
                    }

                    if(!$model->ownsInstrument){
                        $this->redirect(["rent", 'u' => $user['id'], 'l' => $l]);
                    } else if($model->hasExperience) {
                        $this->redirect(["signup-questions", 'u' => $user['id'], 'l' => $l, 's' => $s]);
                    }else {
                        Yii::$app->session->setFlash('success', 'Hei! Esi veiksmīgi piereģistrējies. Noskaties iepazīšanās video ar platformu un sākam spēlēt! Turpmākās 2 nedēļas vari izmēģināt bez maksas!');
                        return $this->redirect(['lekcijas/index']);
                    }
                }
            }
        }

        $model->password = '';
        $instrument = strtolower($school['instrument']);
        return $this->render('signup', [
            'model' => $model,
            'defaultLanguage' => $l,
            'registration_title' => $school['registration_title'],
            'instrument' => $instrument,
        ]);
    }

    public function actionRent($u, $l) {
        Yii::$app->language = $l;
        $school = School::getByStudent($u);
        $user = Users::findOne($u);
        $model = new RentForm;

        $model->fullname = $user['first_name'] . " " . $user['last_name'];
        $model->email = $user['email'];
        $valid = $model->load(Yii::$app->request->post()) && $model->validate();

        if ($valid) {
            if($school['renter_message'] != null && $school['rent_schoolsubplan_id'] != null){
                $studentSubplan = new StudentSubPlans;
                $studentSubplan->user_id = $user['id'];
                $studentSubplan->plan_id = $school['rent_schoolsubplan_id'];
                $studentSubplan->is_active = false;
                $studentSubplan->start_date = date('Y-m-d H:i:s', time());
                $studentSubplan->sent_invoices_count = 0;
                $studentSubplan->times_paid = 0;
                $studentSubplan->save();

                $user->status = 11;
                $user->update();
                
                InvoiceManager::sendAdvanceInvoice($user, $studentSubplan, true);
            }

            $sent = EmailSender::sendRentNotification($model, $school['email']);
            if($sent){
                Yii::$app->session->setFlash('success', 'Paldies par tavu pieteikumu! Tuvākajā laikā sazināsimies ar tevi uz tavu norādīto epastu. Tikmēr vari noskatīties video par to, kā darboties platformā!');
                return $this->redirect(['lekcijas/index']);
            }
        }

        return $this->render('rent', [
            'model' => $model, 
        ]);
    }

    public function actionSignupQuestions($u, $l, $s){
        Yii::$app->language = $l;
        $user = Users::findOne($u);
        
        $schoolSignupQuestions = SignupQuestions::getForSchool($s);

        $post = Yii::$app->request->post();
        if($post && isset($post['answers']) && count($post['answers']) > 0){
            $aboutUser = "";
            foreach($post['answers'] as $id => $answer){
                if($answer !== ""){
                    $aboutUser .= $answer;
                    $aboutUser .= "\n";
                }
            }
            $user['about'] = $aboutUser;
            $saved = $user->save();
            if($saved){
                Yii::$app->session->setFlash('success', 'Hei! Esi veiksmīgi piereģistrējies. Noskaties iepazīšanās video ar platformu un sākam spēlēt! Turpmākās 2 nedēļas vari izmēģināt bez maksas!');
                return $this->redirect(['lekcijas/index']);
            }
        }

        return $this->render('signup-questions', [
            'questions' => $schoolSignupQuestions,
        ]);
    }

    public function actionFirstLecture($l, $u){
        $lectureId = 192;
        Yii::$app->language = $l;

        $lecture = Lectures::findOne($lectureId);
        $evaluations = Evaluations::getEvaluations();
        $lectureEvaluations = Lecturesevaluations::getLectureEvaluations($lectureId);
        foreach ($evaluations as &$evaluation) {
            if ($evaluation['star_text']) {
                $starTextArray = unserialize($evaluation['star_text']);
                foreach ($starTextArray as &$starText) {
                    $starText = Yii::t('app', $starText);
                };
                $evaluation['star_text'] = serialize($starTextArray);
            }
        }
        return $this->render('first-lecture', [
            'model' => $lecture,
            'evaluations' => $evaluations,
            'lectureEvaluations' => $lectureEvaluations,
        ]);
    }
}
