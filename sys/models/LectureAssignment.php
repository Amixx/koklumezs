<?php

namespace app\models;

use app\models\LecturesDifficulties;
use app\models\RelatedLectures;
use app\models\Sentlectures;
use app\models\Studentgoals;
use app\models\UserLectures;
use Yii;

/**
 * LectureAssignment implements spam.
 */
class LectureAssignment extends \yii\db\ActiveRecord
{
    private function changeUserParams($user_id = null, $lecture_id = null)
    {
        $newDifficulties = LecturesDifficulties::getLectureDifficulties($lecture_id);

        if (!empty($newDifficulties)) {
            //remove previous params
            Studentgoals::removeUserGoals($user_id, Studentgoals::NOW);
            foreach ($newDifficulties as $diff => $value) {
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

    public function getNewDifficultyIds(int $result = 0, int $x = 0, $lecture_id = null, $user_id = null): array
    {
        $modelsIds = [];
        if ($result) {
            /** get related in chain below this lecture
             * 3. ■ <- 2. ■ <- 1. ■ <- This lecture □
             */
            $kids = [];
            if ($lecture_id) {
                if (($x == 1) or ($x == 10)) {} else {
                    $kids = self::getKidRelation($lecture_id);
                    if (Yii::$app->request->get('dbg')) {
                        echo 'KIDS<pre>';
                        var_dump($kids);
                        echo '</pre>';
                    }
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
                        self::changeUserParams($user_id, $kid);
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
                //remove current lecture if found
                $ids = array_diff($ids, [$lecture_id]);
                //check if user is not already signed to found lectures
                $newIds = UserLectures::getNewLectures($user_id, $ids);
                $newLecture = null;
                if (!empty($newIds)) {
                    $len = count($newIds);
                    $random = rand(0, $len - 1);
                    $a = 0;
                    foreach ($newIds as $lecture) {
                        //find random result
                        if ($a == $random) {
                            $newLecture = $lecture;
                            break;
                        }
                        $a++;
                    }
                }
                if ($newLecture) {
                    //change user params
                    self::changeUserParams($user_id, $newLecture);
                    $kids = self::getKidRelation($newLecture);
                    if ($kids) {
                        $modelsIds = array_merge($kids, [$newLecture]);
                    } else {
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
        $lectureDifficulty = null;
        if ($lecture_id) {
            $lectureDifficulty = LecturesDifficulties::getLectureDifficulty($lecture_id);
        }
        if (Yii::$app->request->get('dbg')) {
            echo 'User difficulty:' . $userDifficulty . '<br />';
            echo 'Lecture difficulty:' . $lectureDifficulty . '<br />';
        }
        $nextLectures = $lecture_id ? RelatedLectures::getRelations($lecture_id) : [];
        $difficulty = $nextLectures ? $lectureDifficulty : $userDifficulty;
        $nextLecture = count($nextLectures);

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

    public function giveNewAssignment($user = null, $x = 0, $id = null, $spam = false)
    {
        $result = self::getNewUserDifficulty($user, $x, $id);
        if ($result) {
            $ids = self::getNewDifficultyIds($result, $x, $id, $user);
            if ($ids) {
                //check if user is not already signed to found lectures
                $newIds = UserLectures::getNewLectures($user, $ids);
             
                if (!empty($newIds)) {
                    foreach ($newIds as $lec) {
                        $skipErrors = true;
                        $model = new UserLectures();
                        $model->user_id = $user;
                        $model->lecture_id = $lec;
                        $model->assigned = 1;
                        $model->created = date('Y-m-d H:i:s', time());
                        $saved = $model->save($skipErrors);
                        //dont send now, only when needed, twice a week or smthn..
                        $sendNow = false;
                        if ($saved and ($sendNow or $spam)) {
                            $sent = UserLectures::sendEmail($model->user_id, $model->lecture_id);
                            $model->sent = (int) $sent;
                            $model->update();
                            //from cron call
                            if ($spam) {
                                $m = new Sentlectures();
                                $m->user_id = $model->user_id;
                                $m->lecture_id = $model->lecture_id;
                                $m->created = date('Y-m-d H:i:s', time());
                                $sent = UserLectures::sendEmail($model->user_id, $model->lecture_id);
                                var_dump( $sent);die;
                                if (!$sent) {
                                    UserLectures::sendAdminEmail($model->user_id, $model->lecture_id, 0);
                                }
                                $m->sent = (int) $sent;
                                $m->save();
                            }
                        }
                    }
                } else {
                    //spam admin about manual involvement
                    UserLectures::sendAdminEmail($user, $id, $x);
                }
            } else {
                //spam admin about manual involvement
                UserLectures::sendAdminEmail($user, $id, $x);
            }
        } else {
            //spam admin about manual involvement
            UserLectures::sendAdminEmail($user, $id, $x);
        }
        return $result;
    }
}
