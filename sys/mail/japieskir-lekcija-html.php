<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $user common\models\User */
$link = null;
if($lecture){
    $link = Yii::$app->urlManager->createAbsoluteUrl(['lekcijas/lekcija', 'id' => $lecture->id]);
}
?>
<div class="password-reset">
    <p>Sveiki <?= Html::encode($user->first_name) ?>,</p>

    <p>Jums ir piešķirta jauna lekcija:</p>

    <p><?= Html::a(Html::encode($link), $link) ?></p>
</div>
<div class="password-reset">
    <p>Lietotājam <?= Html::encode($user->email) ?> ir manuāli jāpiešķir jauna lekcija.</p>

    <?php if($lecture){ ?>
    <p>Pēdējā lekcija bija: <?=$lecture->title?>(<?=$lecture->complexity?>) <?= $link ?>.</p>
    <?php } ?>
    <p>Aprēķinātā, jaunā, sarežģītība ir:<?= $x ?>.</p>