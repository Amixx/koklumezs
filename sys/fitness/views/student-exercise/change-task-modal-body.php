<?php

use yii\helpers\Html;

?>

<div class="text-center">
    <p><?= Yii::t('app', 'Hey! Have you played such a task before, or maybe you just dont like it? Click "change task" and I\'ll give you another task of similar complexity. Good luck!') ?></p>
    <?= Html::beginForm(["/lekcijas/request-different-lesson?lessonId=$uLecture->lecture_id"]); ?>
    <button type="submit" class="btn btn-blue" style="margin: 4px;"><?= Yii::t('app', 'Change task') ?></button>
    <?= Html::endForm(); ?>
</div>