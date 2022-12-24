<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="progression-chain-form">

    <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>
    <div class="tab-pane fade active in" id="home" role="tabpanel" aria-labelledby="home-tab">
        <?= $form->field($model, 'title')->textInput() ?>
    </div>
    <?php if (isset($progressionChainExercises)) { ?>
        <div style="display:flex; flex-wrap:wrap; gap: 32px 16px;align-items:baseline;">
            <?php for ($i = 0; $i <= 20; $i++) { ?>
                <?php if ($i > 0) { ?>
                    <span style="display:inline-block" class="percentage-input-container">
                            <span style="display: flex; align-items: last baseline">
                                <span>+</span>
                                <?= $form->field($progressionChainExercises[$i], "[$i]difficulty_increase_percent")->textInput(['style' => 'width: 60px'])->label(false) ?>
                                <span>%</span>
                             </span>
                        </span>
                <?php } ?>
                <div style="display:inline-block" class="exercise-select-container">
                    <div style="display:inline-block">
                        <?= $form->field($progressionChainExercises[$i], "[$i]exercise_id")->dropDownList(
                            $exerciseSelectOptions,
                            [
                                'prompt' => '-- ' . \Yii::t('app', 'Choose') . ' --',
                                'class' => 'progression-chain-exercise-select',
                            ]
                        )->label(false) ?>
                    </div>
                    <?php if($i !== 20) { ?>
                        <button type="button" class="btn btn-sm btn-insert-progression-chain-exercise" style="display:inline-block"><i class="glyphicon glyphicon-plus"></i></button>
                    <?php }?>
                </div>
            <?php } ?>
        </div>

        <div class="progression-chain-main-exercise-form">
            <span>Ķēdes vingrojuma</span>
            <?= $form->field($mainExercise, "exerciseId")->dropDownList(
                $exerciseSelectOptions,
                [
                    'prompt' => '-- ' . \Yii::t('app', 'Choose') . ' --',
                    'class' => 'progression-chain-exercise-select',
                ]
            )->label(false) ?>
            <span>grūtība ir</span>
            <?= $form->field($mainExercise, "rep_bw_ratio_percent")->label(false) ?>
            <span>procenti reiz</span>
            <?= $form->field($mainExercise, 'weight_exercise_id')->dropDownList(
                $weightExerciseSelectOptions,
                [
                    'prompt' => '-- ' . \Yii::t('app', 'Choose') . ' --',
                ]
            )->label(false) ?>
            <span>reiz {ķermeņa svars}</span>
        </div>
    <?php } ?>

    <hr/>
    <div class="form-group">
        <?= Html::submitButton(\Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <hr>
    <hr>

    <h3><?= Yii::t('app', 'Exercise creation') ?></h3>

    <?php if (isset($exerciseModel)) {
        echo $this->render('../exercise/_form', [
            'model' => $exerciseModel,
            'tags' => $tags,
            'outerForm' => $form,
        ]);
    } ?>

    <?php ActiveForm::end(); ?>
</div>