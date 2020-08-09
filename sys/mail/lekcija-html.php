<?php

use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $user common\models\User */
//$link = Yii::$app->urlManager->createAbsoluteUrl(['lekcijas/lekcija', 'id' => $lecture->id]);
?>
<div class="password-reset">
    <p>Sveiki <?= Html::encode($user->first_name) ?>,</p>

    <p>Jums ir piešķirtas jaunas nodarbības</p>
    <?php /*
    <p><?= Html::a(Html::encode($link), $link) ?></p>
    */ ?>
</div>