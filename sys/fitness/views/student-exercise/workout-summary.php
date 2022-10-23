<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = \Yii::t('app', 'Workout summary');

$evaluations = [
    2 => "Garlaicīgi",
    4 => "Viegli",
    6 => "Nedaudz grūti",
    8 => "Ļoti grūti",
    10 => "Neiespējami",
];
?>
<!-- unpkg : use the latest version of Video.js -->
<div class="row" style="background: white; border-radius: 8px; margin-top: 16px; padding-bottom: 16px;">
    <div class="col-sm-12 text-center" style="margin-bottom: 16px;">
        <h2>Apsveicam! Treniņš ir galā! 🎉</h2>
    </div>
    <div class="col-md-6" style="text-align:center; margin-bottom: 16px;">
        <div>
            <div>
                <?= $this->render("amount-evaluation", [
                    'difficultyEvaluation' => $workoutEvaluation,
                    'readonly' => $hasBeenEvaluated,
                ]) ?>
            </div>
        </div>
    </div>
    <div class="col-md-6" style="margin-bottom: 32px;">
        <?php if (!$messageModel->id) { ?>
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($messageModel, 'text')->textarea() ?>
            <div style="display: flex; flex-wrap: wrap;">
                <?= $form->field($messageModel, 'video')->fileInput([
                    'accept' => "video/mp4,video/x-m4v,video/*"
                ]) ?>
                <?= $form->field($messageModel, 'audio')->fileInput([
                    'accept' => "audio/*"
                ]) ?>
            </div>
            <div class="form-group" style="text-align:center;">
                <?= Html::submitButton(\Yii::t('app', 'Send'), ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        <?php } else { ?>
            <p style="font-size: 18px;"><?= Yii::t('app', 'Messages for the coach') ?>: </p>
            <?php if ($messageModel->text) { ?>
                <p><?= $messageModel->text ?></p>
            <?php } ?>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                <?php if ($messageModel->video) { ?>
                    <?php
                    $exploded = explode(".", $messageModel->video);
                    $ext = end($exploded);
                    ?>
                    <div style="max-width: 300px">
                        <video id="post-workout-message-video" playsinline controls data-role="player">
                            <source src="<?= '/sys/files/' . $messageModel->video ?>" type="video/<?= $ext ?>"/>
                        </video>
                    </div>
                <?php } ?>
                <?php if ($messageModel->audio) { ?>
                    <?php
                    $exploded = explode(".", $messageModel->audio);
                    $ext = end($exploded);
                    ?>
                    <div style="max-width: 300px;">
                        <audio id="post-workout-message-audio" controls data-role="player">
                            <source src="<?= '/sys/files/' . $messageModel->audio ?>" type="audio/<?= $ext ?>"/>
                        </audio>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
    <div class="col-sm-12" style="margin-top: 16px;">
        <h4 class="text-center">Vingrojumi un novērtējumi</h4>
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>Vingrojums</th>
                <th>Reizes, laiks un svars</th>
                <th>Novērtējums</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($workout->workoutExerciseSets as $wes) { ?>
                <tr>
                    <td><?= $wes->exerciseSet->exercise->name ?></td>
                    <td><?= $wes->repsWeightTimeFormatted() ?></td>
                    <td><?= $wes->evaluation ? $evaluations[$wes->evaluation->evaluation] : '' ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <div class="text-center">
            <?= Html::a(Yii::t('app', 'Return'), ['lekcijas/index'], [
                    'class'=> 'btn btn-primary'
            ]) ?>
        </div>
    </div>
</div>