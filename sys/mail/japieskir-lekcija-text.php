<?php

use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $user common\models\User */

$link = null;
if ($lecture) {
    $link = Yii::$app->urlManager->createAbsoluteUrl(['lekcijas/lekcija', 'id' => $lecture->id]);
}
?>
Lietotājam <?= Html::encode($user->username) ?> ir manuāli jāpiešķir jauna nodarbība.

<?php if ($lecture) { ?>
    Pēdējā nodarbība bija: <?= $lecture->title ?>(<?= $lecture->complexity ?>) <?= $link ?>.
<?php } ?>

Aprēķinātā, jaunā, sarežģītība ir:<?= $x ?>.