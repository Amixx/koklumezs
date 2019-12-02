<?php

namespace app\controllers;

use app\models\Difficulties;
use app\models\Evaluations;
use app\models\Handdifficulties;
use app\models\Lectures;
use app\models\LecturesDifficulties;
use app\models\Lecturesevaluations;
use app\models\Lecturesfiles;
use app\models\Lectureshanddifficulties;
use app\models\RelatedLectures;
use app\models\Studentgoals;
use app\models\Userlectureevaluations;
use app\models\UserLectures;
use app\models\Users;
use Yii;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * LekcijasController implements the actions for Lectures model by student.
 */
class LekcijasController extends Controller
{
    const VIDEOS = ['mp4', 'mov', 'ogv', 'webm', 'flv', 'avi', 'f4v'];
    const DOCS = ['doc', 'docx', 'pdf'];
    const AUDIO = ['aac', 'alac', 'amr', 'flac', 'mp3', 'opus', 'vorbis', 'ogg', 'wav'];
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
                            return Users::isStudent(Yii::$app->user->identity->email);
                        },
                    ],
                    // everything else is denied
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [

                ],
            ],
        ];
    }

    /**
     * Lists all user Lectures models.
     * @return mixed
     */
    public function actionIndex()
    {
        $models = [];
        $user = Yii::$app->user->identity;
        $modelsIds = UserLectures::getUserLectures($user->id);
        if ($modelsIds) {
            $userLectures = UserLectures::getLectures($user->id);
            $query = Lectures::find()->where(['in', 'id', $modelsIds]);
            $countQuery = clone $query;
            $pages = new Pagination(['totalCount' => $countQuery->count()]);
            $models = $query->offset($pages->offset)
                ->limit($pages->limit)
                ->all();
            $opened = UserLectures::getOpened($user->id);
            $userLectureEvaluations = Userlectureevaluations::hasLectureEvaluations($user->id);
            $baseUrl = Yii::$app->request->baseUrl;

            return $this->render('index', [
                'models' => $models,
                'userLectures' => $userLectures,
                'opened' => $opened,
                'pages' => $pages,
                'userLectureEvaluations' => $userLectureEvaluations,
                'baseUrl' => $baseUrl,
                'videos' => self::VIDEOS,
            ]);
        }

        return $this->render('index', [
            'models' => [],
            'pages' => [],
        ]);
    }

    private function getKidRelation(int $id, int $it = 3, $results = [])
    {
        $result = RelatedLectures::getRelatedParents($id);
        if ($result) {
            $id = reset($result);
            $results[$it] = $id;
            $it--;
            if ($it) {
                $results[$it] = self::getKidRelation($id, $it, $results);
            } else {
                return $results;
            }
        }
        return isset($results[$it]) ? $results[$it] : [];
    }

    private function changeUserParams($user_id = null, $lecture_id = null)
    {
        $newDifficulties = LecturesDifficulties::getLectureDifficulties($lecture_id);
        if(!empty($newDifficulties)){
            //remove previous params                        
            Studentgoals::removeUserGoals($user_id,Studentgoals::NOW);
            foreach($newDifficulties as $diff => $value)
            {
                $goal = new Studentgoals();
                $goal->user_id = $user_id;
                $goal->diff_id = $diff;                    
                $goal->type = Studentgoals::NOW;
                $goal->value = $value ?? 0;
                $goal->save();
            }
        }
        return !empty($newDifficulties);
    }

    public function getNewDifficultyIds(int $result = 0, int $x = 0, int $lecture_id = 0, $user_id = null): array
    {
        $modelsIds = [];
        if ($result) {
            /** get related in chain below this lecture
             * 3. ■ <- 2. ■ <- 1. ■ <- This lecture □
             */
            $kids = [];
            if (($x == 1) or ($x == 10)) {} else {
                $kids = self::getKidRelation($lecture_id);
                if (Yii::$app->request->get('dbg')) {
                    echo 'KIDS<pre>';
                    var_dump($kids);
                    echo '</pre>';
                }
            }
            $foundKid = false;
            if ($kids) {
                foreach ($kids as $kid) {
                    $lectureDifficulty = LecturesDifficulties::getLectureDifficulty($kid);
                    //found match in kids
                    if ($lectureDifficulty == $result) {
                        $foundKid = $kid;
                        //change user params
                        self::changeUserParams($user_id,$kid);
                        $modelsIds = $kids;
                        if (!empty($modelsIds)) {
                            if (Yii::$app->request->get('dbg')) {
                                echo "<span style='color:red'>Found by kids difficulty:</span><pre>";
                                print_r($modelsIds);
                                echo "</pre>";
                            }
                        }
                        break;
                    }
                }
            }
            //not found relations, check new random lecture chain.
            if ($foundKid === false) {                
                $ids = LecturesDifficulties::getLecturesByDifficulty($result);                
                //check if user is not already signed to found lectures
                $newIds = UserLectures::getNewLectures($user_id, $ids);
                $newLecture = null;
                if (!empty($newIds)) {
                    $len = count($newIds);
                    $random = rand(0, $len - 1);
                    $a = 0;   
                    foreach($newIds as $lecture){
                        //find random result
                        if($a == $random){
                            $newLecture = $lecture;
                            break;
                        }
                        $a++;
                    }
                }
                if($newLecture){
                    //change user params
                    self::changeUserParams($user_id,$newLecture);
                    $kids = self::getKidRelation($newLecture);
                    if($kids){
                        $modelsIds = array_merge($kids,[$newLecture]);
                    }else{
                        $modelsIds = [$newLecture];
                    }
                }
                if (!empty($modelsIds)) {
                    if (Yii::$app->request->get('dbg')) {
                        echo "<span style='color:red'>Found by new difficulty:</span><pre>";
                        print_r($modelsIds);
                        echo "</pre>";
                    }
                }
            }
           
        }
        return $modelsIds;
    }

    /**
     * @property $userDifficulty
     * Max - $userDifficulty
     * 1 (Viss tik viegls, ka garlaicīgi, vajag pieslēgties manuāli)
     * 2 (ļoti ļoti viegli, neoteikti vajag grūāk) Max-x+4 (jāpieliek 4 sarežģītības punkti klāt kopumā. Piemēram ja bija 45345, tad tagad varētu būt 56455 vai 35566)
     * 3 (izspēlēju vienu reizi un jau viss skaidrs) Max-x+3
     * 4 (Diezgan vienkārši)        Max-x+2
     * 5 (nācās pastrādāt, bet tiku galā bez milzīgas piepūles) Max-x+1 vai max-x+2 ( ja ir jauns ķēdes uzdevums)
     * 6 (Tiku galā): |Paliek tas pats Max-x vai arī Max-x+1 (jau ir nākamais ķēdes uzdevums)
     * 7 (diezgan gŗūti) Max-x-1
     * 8 (itkā saprotu, ebt pirksti neklausa) Max-x-2/3
     * 9 (kaut ko mēģinu, bet pārāk nesanāk): Max-x-4
     * 10 (vispār neko nesaprotu): Manuāli
     */
    public function getNewUserDifficulty($user_id, $x = null, $lecture_id = null): int
    {
        $userDifficulty = Studentgoals::getUserDifficulty($user_id);
        $lectureDifficulty = LecturesDifficulties::getLectureDifficulty($lecture_id);
        if (Yii::$app->request->get('dbg')) {
            echo 'User difficulty:' . $userDifficulty . '<br />';
            echo 'Lecture difficulty:' . $lectureDifficulty . '<br />';
        }
        
        $nextLectures = RelatedLectures::getRelations($lecture_id);
        $difficulty = $nextLectures ? $lectureDifficulty : $userDifficulty;
        //var_dump( $previousLectures);
        $nextLecture = count($nextLectures);
        /*foreach ($nextLectures as $lecture) {
            $test = LecturesDifficulties::getLectureDifficulty($lecture);
            if ($test > $lectureDifficulty) {
                if (Yii::$app->request->get('dbg')) {
                    echo 'next related Lecture:' . $lecture . ' <br />';
                }
                $nextLecture = $lecture;
                break;
            } else {
                if (Yii::$app->request->get('dbg')) {
                    echo 'test Lecture:' . $test . ' <br />';
                }
            }
        }*/
        
        switch ($x) {
            /**
                 * 1 (Viss tik viegls, ka garlaicīgi, vajag pieslēgties manuāli)
                 */
            case 1:
                $result = 0;
                break;
            /**
                 * 2 (ļoti ļoti viegli, neoteikti vajag grūāk) Max-x+4 (jāpieliek 4 sarežģītības punkti klāt kopumā. Piemēram ja bija 45345, tad tagad varētu būt 56455 vai 35566)
                 */
            case 2:
                $result = $difficulty - $x + 4;
                break;
            /**
                 * 3 (izspēlēju vienu reizi un jau viss skaidrs) Max-x+3
                 */
            case 3:
                $result = $difficulty - $x + 3; // -3 + 3 lol, it cancels out :D
                break;
            /**
                 * 4 (Diezgan vienkārši)        Max-x+2
                 */
            case 4:
                $result = $difficulty - $x + 2;
                break;
            /**
                 * 5 (nācās pastrādāt, bet tiku galā bez milzīgas piepūles) Max-x+1 vai max-x+2 ( ja ir jauns ķēdes uzdevums)
                 */
            case 5:
                $result = $nextLecture ? $difficulty - $x + 2 : $difficulty - $x + 1;
                break;
            /**
                 * 6 (Tiku galā): |Paliek tas pats Max-x vai arī Max-x+1 (jau ir nākamais ķēdes uzdevums)
                 */
            case 6:
                $result = $nextLecture ? $difficulty - $x + 1 : $difficulty - $x;
                break;
            /**
                 * 7 (diezgan gŗūti) Max-x-1
                 */
            case 7:
                $result = $difficulty - $x - 1;
                break;
            /**
                 * 8 (itkā saprotu, ebt pirksti neklausa) Max-x-2/3
                 */
            case 8:
                $result = ceil($difficulty - $x - 2 / 3);
                break;
            /**
                 * 9 (kaut ko mēģinu, bet pārāk nesanāk): Max-x-4
                 */
            case 9:
                $result = $difficulty - $x - 4;
                break;
            /**
                 * 10 (vispār neko nesaprotu): Manuāli
                 */
            case 10:
                $result = 0;
                break;
            default:
                $result = $difficulty;
        }
        /** maybe, will see..
        if ($result and ($userDifficulty > $lectureDifficulty)) {

         * evaluating old lecture, skill is greater by default
         */
        /**$result = $userDifficulty;
        }*/
        return $result;
    }

    /**
     * Displays a single Lecture model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionLekcija($id)
    {
        $model = $this->findModel($id);
        $user = Yii::$app->user->identity;
        if (Yii::$app->request->get('dbg')) {

            for ($x = 1; $x <= 10; $x++) {
                $result = self::getNewUserDifficulty($user->id, $x, $id);
                $data = self::getNewDifficultyIds($result, $x, $id, $user->id);
                echo 'iteration: ' . $x . '<br />';
                echo "new difficulty:<pre>";
                print_r($result);
                echo "</pre>";
                echo "new lecture ids:<pre>";
                print_r($data);
                echo "</pre>";
                echo '<hr />';
            }
            die;
        }
        $modelsIds = UserLectures::getUserLectures($user->id);
        $check = in_array($id, $modelsIds);
        $userLectures = UserLectures::getLectures($user->id);
        $userEvaluatedLectures = UserLectures::getEvaluatedLectures($user->id);
        if ($check) {
            $post = Yii::$app->request->post();
            if (isset($post['evaluations'])) {
                foreach ($post['evaluations'] as $pid => $value) {
                    $evaluation = new Userlectureevaluations();
                    $evaluation->evaluation_id = $pid;
                    $evaluation->lecture_id = $model->id;
                    $evaluation->user_id = $user->id;
                    $evaluation->created = date('Y-m-d H:i:s', time());
                    $evaluation->evaluation = $value ?? 0;
                    $evaluation->save();
                }
                $param = Evaluations::getScaleParam();
                if (isset($post['evaluations'][$param])) {
                    $x = (int) $post['evaluations'][$param];
                    $userLecture = UserLectures::findOne($id);
                    $userLecture->evaluated = 1;
                    $savedEvaluation = $userLecture->save();
                    // find next lecture(s)
                    if ($savedEvaluation) {
                        $result = self::getNewUserDifficulty($user->id, $x, $id);
                        if ($result) {
                            $ids = self::getNewDifficultyIds($result, $x, $id, $user->id);
                            if ($ids) {
                                //check if user is not already signed to found lectures
                                $newIds = UserLectures::getNewLectures($user->id, $ids);
                                if (!empty($newIds)) {
                                    $skippErrors = true;
                                    $model = new UserLectures();
                                    $model->assigned = null;
                                    $model->created = date('Y-m-d H:i:s', time());
                                    $saved = $model->save($skippErrors);
                                    if ($saved) {
                                        $sent = UserLectures::sendEmail($model->user_id, $model->lecture_id);
                                        $model->sent = (int) $sent;
                                        $model->update();
                                    }
                                }
                            }
                        }
                    }
                }
            }
            UserLectures::setSeenByUser($user->id, $id);
            $difficulties = Difficulties::getDifficulties();
            $evaluations = Evaluations::getEvaluations();
            $handdifficulties = Handdifficulties::getDifficulties();
            $lectureDifficulties = LecturesDifficulties::getLectureDifficulties($id);
            $lectureHandDifficulties = Lectureshanddifficulties::getLectureDifficulties($id);
            $lectureEvaluations = Lecturesevaluations::getLectureEvaluations($id);
            $lecturefiles = Lecturesfiles::getLectureFiles($id);
            $userLectureEvaluations = Userlectureevaluations::getLectureEvaluations($user->id, $id);
            $baseUrl = Yii::$app->request->baseUrl;
            $ids = RelatedLectures::getRelations($id);
            $relatedLectures = Lectures::getLecturesByIds($ids);
            return $this->render('lekcija', [
                'model' => $model,
                'difficulties' => $difficulties,
                'handdifficulties' => $handdifficulties,
                'evaluations' => $evaluations,
                'lectureDifficulties' => $lectureDifficulties,
                'lectureHandDifficulties' => $lectureHandDifficulties,
                'lectureEvaluations' => $lectureEvaluations,
                'lecturefiles' => $lecturefiles,
                'userLectures' => $userLectures,
                'userEvaluatedLectures' => $userEvaluatedLectures,
                'userLectureEvaluations' => $userLectureEvaluations,
                'videos' => self::VIDEOS,
                'docs' => self::DOCS,
                'audio' => self::AUDIO,
                'baseUrl' => $baseUrl,
                'relatedLectures' => $relatedLectures,
            ]);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the Lectures model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Lectures the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Lectures::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
