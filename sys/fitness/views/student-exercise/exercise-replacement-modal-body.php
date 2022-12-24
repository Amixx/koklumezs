<?php

use yii\helpers\Html;

?>
<div>
    <?php foreach ($interchangeableExercises as $index => $interchangeableExercise) {
        $exercise = array_key_exists('exercise', $interchangeableExercise)
            ? $interchangeableExercise['exercise']
            : $interchangeableExercise;
        ?>
        <div class="exercise-replacement-option-container" <?= $index === 0 ? '' : 'hidden' ?>>
            <div>
                <h3 style="margin-top: 0;"><strong><?= $exercise->name ?></strong></h3>
                <?php if ($exercise->equipment_video) {
                    echo $this->render(
                        'video',
                        [
                            'fileUrl' => $exercise->equipment_video,
                            'thumbnail' => $exercise->getVideoThumb(),
                            'id' => 'fitness_interchagneable_exercise_technique' . $exercise->id,
                        ]
                    );
                } ?>
                <p>Vai Tev ir šim vingrojumam nepieciešamais aprīkojums?</p>
            </div>
            <div style="display: flex; gap: 8px;">
                <?= Html::button(Yii::t('app', 'No'), [
                    'class' => 'btn btn-danger btn-next-replacement-exercise',
                    'style' => 'width: 100%',
                ]) ?>
                <?= Html::a(
                    Yii::t('app', 'Yes'),
                    [
                        'fitness-student-exercises/replace-exercise',
                        'id' => $workoutExercise->id,
                        'replacementId' => $exercise->id,
                        'replacementBodyweightPercentage' => array_key_exists('percentage', $interchangeableExercise) ? $interchangeableExercise['percentage'] : null,
                    ], [
                    'class' => 'btn btn-success',
                    'style' => 'width: 100%',
                ]) ?>
            </div>
        </div>
    <?php } ?>
    <div class="exercise-replacement-option-container" hidden>
        <p><?= Yii::t('app', 'Unfortunately, the given exercise cannot replaced by any other') ?>! 😔</p>
        <button class="btn btn-primary"
                style="width: 100%"
                data-toggle="modal"
                data-target="#exercise-replacement-modal"
        ><?= Yii::t('app', 'Close') ?></button>
    </div>
</div>