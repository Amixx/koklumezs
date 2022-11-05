<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use app\fitness\models\DifficultyEvaluation;

$difficultyEvaluationModel = $reps
    ? DifficultyEvaluation::createForReps($reps)
    : ($timeSeconds
        ? DifficultyEvaluation::createForTime($timeSeconds)
        : DifficultyEvaluation::createEmpty());
$evaluations = $difficultyEvaluationModel->createEvaluations();

$isButtonActive = function ($name) use ($evaluations, $difficultyEvaluation) {
    if (!$difficultyEvaluation) return false;

    $evalIndex = array_search($name, array_column($evaluations, 'text'));

    return $evaluations[$evalIndex]['value'] === intval($difficultyEvaluation->evaluation);
};


?>

<p style="font-size: 18px; <?= isset($readonly) && $readonly ? '' : 'font-weight: bold' ?>">Kā gāja?</p>
<div>
    <?php $form = ActiveForm::begin(); ?>
    <?= Html::hiddenInput("difficulty-evaluation", null) ?>

    <div class="fitness-difficulty-eval">
        <div>
            <?php foreach ($evaluations as $evaluation) {
                $name = $evaluation['text'];
                $emojiClass = "emoji emoji-$name";
                ?>
                <button
                        type="button"
                        data-role="fitness-eval-btn"
                        data-value="<?= $evaluation['value'] ?>"
                        class="btn <?= $isButtonActive($name) ? 'active' : '' ?>"
                        title="<?= $evaluation['text'] ?>"
                    <?= isset($readonly) && $readonly ? 'disabled' : '' ?>
                >
                    <?= $evaluation['text'] ?>
                </button>
            <?php } ?>
        </div>
    </div>


    <?php ActiveForm::end(); ?>
</div>