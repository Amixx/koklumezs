<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $user common\models\User */
$link = Yii::$app->urlManager->createAbsoluteUrl(['lekcijas/lekcija', 'id' => $lecture->id]);
?>
Sveiki <?= $user->first_name ?>,

Jums ir piešķirta jauna lekcija:

<?= $link ?>