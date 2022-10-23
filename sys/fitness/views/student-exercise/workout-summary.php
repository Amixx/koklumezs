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
            <div class="form-group" style="text-align:center;">
                <?= Html::submitButton(\Yii::t('app', 'Send'), ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        <?php } else if ($messageModel->text) { ?>
            <p><?= Yii::t('app', 'Message for the coach') ?>: <?= $messageModel->text ?></p>
        <?php } ?>
    </div>
    <div class="col-sm-12" style="margin-top: 16px; overflow-x:scroll">
        <h4 class="text-center">Vingrojumi un novērtējumi</h4>
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>Vingrojums</th>
                <th>Reizes</th>
                <th>Laiks (sekundes)</th>
                <th>Svars (kg)</th>
                <th>Novērtējums</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($workout->workoutExerciseSets as $wes) { ?>
                <tr>
                    <td><?= $wes->exerciseSet->exercise->name ?></td>
                    <td><?= $wes->exerciseSet->reps ?></td>
                    <td><?= $wes->exerciseSet->time_seconds ?></td>
                    <td><?= $wes->weight ?></td>
                    <td><?= $wes->evaluation ? $evaluations[$wes->evaluation->evaluation] : '' ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>