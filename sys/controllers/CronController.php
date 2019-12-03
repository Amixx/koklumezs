<?php

namespace app\controllers;

use app\models\LectureAssignment;
use app\models\Sentlectures;
use app\models\UserLectures;
use app\models\Studentgoals;
use app\models\Users;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CronController sends spam a lot.
 */
class CronController extends Controller
{

    /**
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $get = Yii::$app->request->queryParams;
        if (!isset($get['send'])) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $onlyThoseWithoutDontBother = true;
        $users = Users::getActiveStudents($onlyThoseWithoutDontBother);
        $queue = [];
        $spam = true;
        ob_start();
        foreach ($users as $user_id => $userVals) {
            $queue = UserLectures::getUnsentLectures($user_id);
            $last = UserLectures::getLastEvaluatedLecture($user_id);
            
            //user has some assigned lectures that has not sent
            if ($queue) {
                foreach ($queue as $q) {
                    $lecture = UserLectures::findOne($q->lecture_id);
                    if ($lecture) {
                        $model = new Sentlectures();
                        $model->user_id = $user_id;
                        $model->lecture_id = $lecture->lecture_id;
                        $model->created = date('Y-m-d H:i:s', time());
                        $sent = UserLectures::sendEmail($user_id, $model->lecture_id);                      
                        if (!$sent) {
                            UserLectures::sendAdminEmail($user_id, $model->lecture_id, 0);
                        }
                        $model->sent = (int) $sent;
                        $model->save();
                        $q->sent = (int) $sent;
                        $q->save();
                    }
                }
            }
            if ($last) {
                $count = $last->sent_times;
                if ($count == 1) {
                    $x = 4;
                } elseif ($count == 2) {
                    $x = 8;
                } else {
                    $x = Studentgoals::getUserDifficultyCoef($user_id);
                }
                LectureAssignment::giveNewAssignment($user_id, $x, $last->lecture_id, $spam);
                $last->sent_times = (int)$last->sent_times + 1;
                $last->update();
            } else {
                $x = Studentgoals::getUserDifficultyCoef($user_id);
                LectureAssignment::giveNewAssignment($user_id, $x, null, $spam);
            }
        }

        //$model->update();
        $log = ob_get_clean();
        return $this->renderPartial('index', [
            'log' => $log,
        ]);
    }

}
