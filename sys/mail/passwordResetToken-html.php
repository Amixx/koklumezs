<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $user common\models\User */
$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
<div class="password-reset">
    <p>Sveiki <?= Html::encode($user->first_name) ?>,</p>

    <p>Lūdzu nospied uz zemāk norādīto saiti, lai atjaunotu savu paroli:</p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>