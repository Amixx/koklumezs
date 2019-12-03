<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $user common\models\User */
$link = null;
if($lecture){
    $link = Yii::$app->urlManager->createAbsoluteUrl(['lekcijas/lekcija', 'id' => $lecture->id]);
}
?>
Lietotājam <?= Html::encode($user->email) ?> ir manuāli jāpiešķir jauna lekcija.

<?php if($lecture){ ?>
Pēdējā lekcija bija: <?=$lecture->title?>(<?=$lecture->complexity?>) <?= $link ?>.
<?php } ?>

Aprēķinātā, jaunā, sarežģītība ir:<?= $x ?>.