<?php

namespace app\models;

use app\models\LecturesDifficulties;
use app\models\RelatedLectures;
use app\models\Studentgoals;
use app\models\UserLectures;
use Yii;

/**
 * LectureAssignment implements spam.
 */
class LectureAssignment extends \yii\db\ActiveRecord
{
    private static $sum;
    /**
     * 1 + 1 + 1 + 1 + 1 
     */
    const MINIMUM = 5;
    /**
     * 10 + 10 + 10 + 10 + 10 
     */
    const MAXIMUM = self::MINIMUM * 10;

    public function __construct()
    {
        self::$sum = 0;
    }

    private function changeUserParams($user_id = null, $lecture_id = null)
    {
        $newDifficulties = LecturesDifficulties::getLectureDifficulties($lecture_id);
        self::$sum = 0;
        if (!empty($newDifficulties)) {
            //remove previous params
            Studentgoals::removeUserGoals($user_id, Studentgoals::NOW);
            foreach ($newDifficulties as $diff => $value) {
                $goal = new Studentgoals();
                $goal->user_id = $user_id;
                $goal->diff_id = $diff;
                $goal->type = Studentgoals::NOW;
                $goal->value = $value ?? 0;
                self::$sum += $goal->value;
                $goal->save();
            }
        }
        return !empty($newDifficulties);
    }

    private static function getKidRelation(int $id, int $it = 3, $results = [])
    {
        $results = [];
        $result = RelatedLectures::getRelatedParents($id);
        if ($result) {
            $id = reset($result);
            $results[$it] = $id;
            $it--;
            for ($x = $it; $x <= 0; $x--) {
                $result = RelatedLectures::getRelatedParents($id);
                if (!$result) {
                    break;
                }
                $id = reset($result);
                $results[$x] = $id;
            }
        }
        return $results;
    }

    public static function getNewDifficultyIds(int $result = 0, int $x = 0, $lecture_id = null, $user_id = null, $dbg = false, $returnIds = false): array
    {
        //always true from now
        //$dbg = false;
        ob_start();
        $modelsIds = [];

        if ($result) {

            //Ja ķēdē iekļaujas līdzīgi uzdevumi, tad liekam līdzīgus no vienas ķēdes, bet ja ķēdē nav līdzīgā sarežģītībā (jo ķēdes pērsvarā attīstas sarežģītībā), tad sūtam ko jaunu.
            $nextLectures = $lecture_id ? RelatedLectures::getRelations($lecture_id) : [];
            $foundNext = false;
            if (!empty($nextLectures)) {
                if ($dbg && !empty($nextLectures)) {
                    echo 'NEXT RELATED LECTURES<pre>';
                    print_r($nextLectures);
                    echo '</pre>';
                    echo 'NEXT LECTURES<br />';
                    foreach ($nextLectures as $id) {
                        $lecture = Lectures::findOne($id);
                        $dif = LecturesDifficulties::getLectureDifficulty($id);
                        echo $id . ' - ' . $lecture->title . '<strong>[' . $dif . ']</strong></br>';
                    }
                }
                foreach ($nextLectures as $next) {
                    $lectureDifficulty = LecturesDifficulties::getLectureDifficulty($next);
                    //found match in kids
                    if ($lectureDifficulty == $result) {
                        $foundNext = $next;
                        if (!$dbg && !$returnIds) {
                            //change user params
                            self::changeUserParams($user_id, $next);
                        }
                        $modelsIds = $nextLectures;
                        if (!empty($modelsIds)) {
                            if ($dbg) {
                                echo "<span style='color:red'>Found by next chain related difficulty:</span><pre>";
                                print_r($modelsIds);
                                echo "</pre>";
                                echo 'LECTURES<br />';
                                foreach ($modelsIds as $id) {
                                    $lecture = Lectures::findOne($id);
                                    $dif = LecturesDifficulties::getLectureDifficulty($id);
                                    echo $id . ' - ' . $lecture->title . '<strong>[' . $dif . ']</strong></br>';
                                }
                                echo "<span style='color:red'>New lecture by next chain related difficulty:<strong> $foundNext </strong></span>";
                            }
                        }
                        break;
                    }
                }
            }
            if (empty($modelsIds)) {
                /** get related in chain below this lecture
                 * 3. ■ <- 2. ■ <- 1. ■ <- This lecture □
                 */
                $kids = [];
                if ($lecture_id) {
                    if (($x == 1) || ($x == 10)) {
                    } else {
                        $kids = self::getKidRelation($lecture_id);
                        if ($dbg && !empty($kids)) {
                            echo 'KIDS<pre>';
                            print_r($kids);
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
                            if (!$dbg && !$returnIds) {
                                //change user params
                                self::changeUserParams($user_id, $kid);
                            }
                            $modelsIds = $kids;
                            if (!empty($modelsIds)) {
                                if ($dbg) {
                                    echo "<span style='color:red'>Found by kids difficulty:</span><pre>";
                                    print_r($modelsIds);
                                    echo "</pre>";
                                    echo 'LECTURES<br />';
                                    foreach ($modelsIds as $id) {
                                        $lecture = Lectures::findOne($id);
                                        $dif = LecturesDifficulties::getLectureDifficulty($id);
                                        echo $id . ' - ' . $lecture->title . '<strong>[' . $dif . ']</strong></br>';
                                    }
                                    echo "<span style='color:red'>New lecture by kids difficulty:<strong> $foundKid </strong></span>";
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
                    if ($lecture_id) {
                        $ids = array_diff($ids, [$lecture_id]);
                    }
                    //check if user is not already signed to found lectures
                    $newIds = UserLectures::getNewLectures($user_id, $ids);
                    if ($dbg) {
                        echo 'random new';
                        var_dump($newIds);
                    }
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
                        if (!$dbg && !$returnIds) {
                            //change user params
                            self::changeUserParams($user_id, $newLecture);
                        }
                        $kids = self::getKidRelation($newLecture);
                        if ($kids) {
                            $modelsIds = array_merge($kids, [$newLecture]);
                        } else {
                            $modelsIds = [$newLecture];
                        }
                    }
                    if (!empty($modelsIds)) {
                        if ($dbg) {
                            echo "<span style='color:red'>Found by new difficulty:</span><pre>";
                            print_r($modelsIds);
                            echo "</pre>";
                            echo 'LEKCIJAS<br />';
                            foreach ($modelsIds as $id) {
                                $lecture = Lectures::findOne($id);
                                $dif = LecturesDifficulties::getLectureDifficulty($id);
                                echo $id . ' - ' . $lecture->title . '<strong>[' . $dif . ']</strong></br>';
                            }
                            echo "<span style='color:red'>New lecture by difficulty:</span><strong> $newLecture </strong>";
                        }
                    }
                }
            }
        }
        if (empty($modelsIds)) {
            echo "<br /><span style='color:red'>NOT FOUND ANY NEW LECTURE by difficulty:<strong> $result </strong></span>";
        } else {
            echo "<br /><span style='color:green'>FOUND NEW LECTURE by difficulty:<strong> $result </strong></span>";
        }
        $log = ob_get_clean();
        if ($dbg) {
            echo 'random ids23';
            var_dump($log);
        }
        Yii::$app->session->setFlash('assignmentlog',  $log);
        return $modelsIds;
    }

    public static function getPossibleThreeLectures($user_id = null, $spam = false, $dbg = false)
    {
        $result = [];
        $last = UserLectures::getLastEvaluatedLecture($user_id);
        if ($last) {
            $count = $last->sent_times;
            if ($count == 1) {
                $x = 4;
            } elseif ($count == 2) {
                //Respektīvi ievietojam nākamo uzdevumu ar vērtējumu "8 (diezgan sarežģīti un nepieciešams ko vieglāku nākamajā reizē)".
                //8 (itkā saprotu, ebt pirksti neklausa) MAXIMUM-x-2/3
                $x = 8;
            } else {
                $x = Studentgoals::getUserDifficultyCoef($user_id);
            }
            $result = self::giveNewAssignment($user_id, $x, $last->lecture_id, $spam, $dbg, true);
        } else {
            $result = self::getSameDiffLectures($user_id, $spam, $dbg);
        }
        return $result;
    }

    public static function getSameDiffLectures($user_id = null, $spam = false, $dbg = false)
    {
        $result = [];
        $x = 0; //Studentgoals::getUserDifficultyCoef($user_id);
        $predefinedResult = Studentgoals::getUserDifficulty($user_id);
        $result = self::giveNewAssignment($user_id, $x, null, $spam, $dbg, true, $predefinedResult);
        return $result;
    }

    /**
     * @property $userDifficulty
     * MAXIMUM - $userDifficulty
     * 1 (Viss tik viegls, ka garlaicīgi, vajag pieslēgties manuāli)
     * 2 (ļoti ļoti viegli, neoteikti vajag grūāk) MAXIMUM-x+4 (jāpieliek 4 sarežģītības punkti klāt kopumā. Piemēram ja bija 45345, tad tagad varētu būt 56455 vai 35566)
     * 3 (izspēlēju vienu reizi un jau viss skaidrs) MAXIMUM-x+3
     * 4 (Diezgan vienkārši)        MAXIMUM-x+2
     * 5 (nācās pastrādāt, bet tiku galā bez milzīgas piepūles) MAXIMUM-x+1 vai MAXIMUM-x+2 ( ja ir jauns ķēdes uzdevums)
     * 6 (Tiku galā): |Paliek tas pats MAXIMUM-x vai arī MAXIMUM-x+1 (jau ir nākamais ķēdes uzdevums)
     * 7 (diezgan gŗūti) MAXIMUM-x-1
     * 8 (itkā saprotu, ebt pirksti neklausa) MAXIMUM-x-2/3
     * 9 (kaut ko mēģinu, bet pārāk nesanāk): MAXIMUM-x-4
     * 10 (vispār neko nesaprotu): Manuāli
     */
    public static function getNewUserDifficulty($user_id, $x = null, $lecture_id = null, $dbg = false): int
    {
        $userDifficulty = Studentgoals::getUserDifficulty($user_id);
        $lectureDifficulty = null;
        if ($lecture_id) {
            $lectureDifficulty = LecturesDifficulties::getLectureDifficulty($lecture_id);
        }
        if ($dbg) {
            echo 'User difficulty:' . $userDifficulty . '<br />';
            echo 'Lecture difficulty:' . $lectureDifficulty . '<br />';
        }
        $nextLectures = $lecture_id ? RelatedLectures::getRelations($lecture_id) : [];
        $difficulty = $nextLectures ? $lectureDifficulty : $userDifficulty;
        $nextLecture = count($nextLectures);
        /**
         * minimum difficulty
         * 1 + 1 + 1 + 1 +1
         */
        $minimumDifficulty = self::MINIMUM;
        switch ($x) {
                /**
                 * 1 (Viss tik viegls, ka garlaicīgi, vajag pieslēgties manuāli)
                 */
            case 1:
                $result = 0;
                break;
                /**
                 * 2 (ļoti ļoti viegli, neoteikti vajag grūāk) MAXIMUM-x+4 (jāpieliek 4 sarežģītības punkti klāt kopumā. Piemēram ja bija 45345, tad tagad varētu būt 56455 vai 35566)
                 */
            case 2:
                $result = $difficulty - $x + 4;
                break;
                /**
                 * 3 (izspēlēju vienu reizi un jau viss skaidrs) MAXIMUM-x+3
                 */
            case 3:
                $result = $difficulty - $x + 3; // -3 + 3 lol, it cancels out :D
                break;
                /**
                 * 4 (Diezgan vienkārši)        MAXIMUM-x+2
                 */
            case 4:
                $result = $difficulty - $x + 2;
                break;
                /**
                 * 5 (nācās pastrādāt, bet tiku galā bez milzīgas piepūles) MAXIMUM-x+1 vai MAXIMUM-x+2 ( ja ir jauns ķēdes uzdevums)
                 */
            case 5:
                $result = $nextLecture ? $difficulty - $x + 2 : $difficulty - $x + 1;
                break;
                /**
                 * 6 (Tiku galā): |Paliek tas pats MAXIMUM-x vai arī MAXIMUM-x+1 (jau ir nākamais ķēdes uzdevums)
                 */
            case 6:
                $result = $nextLecture ? $difficulty - $x + 1 : $difficulty - $x;
                break;
                /**
                 * 7 (diezgan gŗūti) MAXIMUM-x-1
                 */
            case 7:
                $result = $difficulty - $x - 1;
                break;
                /**
                 * 8 (itkā saprotu, ebt pirksti neklausa) MAXIMUM-x-2/3
                 */
            case 8:
                $result = ceil($difficulty - $x - 2 / 3);
                break;
                /**
                 * 9 (kaut ko mēģinu, bet pārāk nesanāk): MAXIMUM-x-4
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
        if ($result && ($userDifficulty > $lectureDifficulty)) {

         * evaluating old lecture, skill is greater by default
         */
        /**$result = $userDifficulty;
        }*/
        return (($result > $minimumDifficulty) ? $minimumDifficulty : $result);
    }

    public static function giveNewAssignment($user = null, $x = 0, $id = null, $spam = false, $dbg = false, $returnIds = false, int $predefinedResult = 0)
    {
        $result =  ($predefinedResult > 0) ? $predefinedResult : self::getNewUserDifficulty($user, $x, $id, $dbg);
        if ($result) {
            if ($dbg) {
                echo 'New difficulty:<strong>' . $result . '</strong><br />';
            }
            $ids = self::getNewDifficultyIds($result, $x, $id, $user, $dbg, $returnIds);
            if ($ids) {
                //check if user is not already signed to found lectures
                $newIds = UserLectures::getNewLectures($user, $ids);

                if (!empty($newIds) && !$dbg) {
                    // foreach ($newIds as $lec) {
                    //     $skipErrors = true;
                    //     $model = new UserLectures();
                    //     $model->user_id = $user;
                    //     $model->lecture_id = $lec;
                    //     $model->assigned = 1;
                    //     $model->created = date('Y-m-d H:i:s', time());
                    //     $model->user_difficulty = self::$sum ?? 0;
                    //     $saved = $model->save($skipErrors);
                    //     //dont send now, only when needed, twice a week or smthn..
                    //     $sendNow = false;
                    //     if ($saved & ($sendNow || $spam)) {
                    //         //noņemt e-pastu automātisko sūtīšanu. Cilvēkiem uzrādās tie kā "Nedroši"
                    //         //$sent = UserLectures::sendEmail($model->user_id, $model->lecture_id);
                    //         $sent = 1;
                    //         $model->sent = (int) $sent;
                    //         $model->update();
                    //         //from cron call
                    //         if ($spam) {
                    //             $m = new Sentlectures();
                    //             $m->user_id = $model->user_id;
                    //             $m->lecture_id = $model->lecture_id;
                    //             $m->created = date('Y-m-d H:i:s', time());
                    //             //noņemt e-pastu automātisko sūtīšanu. Cilvēkiem uzrādās tie kā "Nedroši"
                    //             /*$sent = UserLectures::sendEmail($model->user_id, $model->lecture_id);
                    //             if (!$sent) {
                    //                 UserLectures::sendAdminEmail($model->user_id, $model->lecture_id, 0);
                    //             }*/
                    //             $m->sent = (int) $sent;
                    //             $m->save();
                    //         }
                    //     }
                    // }
                    // if ($returnIds) {
                    //     return $ids;
                    // }
                } elseif ($dbg) {
                    echo '<pre>';
                    print_r($newIds);
                    echo '</pre>';
                } elseif (!$returnIds) {
                    //recursion till the end of time..
                    if ($result < self::MAXIMUM) {
                        $result++;
                        return self::giveNewAssignment($user, $x, $id, false, false, false, $result);
                    }
                    if ($dbg) {
                        echo '<br />SPAM TO ADMIN';
                    } else {
                        //spam admin about manual involvement
                        UserLectures::sendAdminEmail($user, $id, $x);
                    }
                }
            } elseif (!$returnIds) {
                //recursion till the end of time..
                if ($result < self::MAXIMUM) {
                    $result++;
                    return self::giveNewAssignment($user, $x = 0, $id, false, false, false, $result);
                } elseif ($dbg) {
                    echo '<br />SPAM TO ADMIN';
                } else {
                    //spam admin about manual involvement
                    UserLectures::sendAdminEmail($user, $id, $x);
                }
            }
        } elseif (!$returnIds) {
            if ($dbg) {
                echo '<br />SPAM TO ADMIN';
            } else {
                //spam admin about manual involvement
                UserLectures::sendAdminEmail($user, $id, $x);
            }
        }
        return $result;
    }
}
