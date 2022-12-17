<?php

use app\fitness\models\WeightExerciseAbilityRatio;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="lectures-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="tab-pane fade active in" id="home" role="tabpanel" aria-labelledby="home-tab">
        <div class="form-group">
            <label class="control-label">
                <?= \Yii::t('app', 'Exercise 1') ?>
            </label>
            <select
                    class="form-control all-exercise-select"
                    name="WeightExerciseAbilityRatio[exercise_id_1]"
                    aria-required="true"
                    aria-invalid="false">
                <?php if($model->exercise_id_1) { ?>
                    <option value="<?= $model->exercise_id_1 ?>" selected><?= $model->exercise1->name ?></option>
                <?php } ?>
            </select>
            <div class="help-block"></div>
        </div>
        <div class="form-group">
            <label class="control-label">
                <?= \Yii::t('app', 'Exercise 2') ?>
            </label>
            <select
                    class="form-control all-exercise-select"
                    name="WeightExerciseAbilityRatio[exercise_id_2]"
                    aria-required="true"
                    aria-invalid="false">
                <?php if($model->exercise_id_2) { ?>
                    <option value="<?= $model->exercise_id_2 ?>" selected><?= $model->exercise2->name ?></option>
                <?php } ?>
            </select>
            <div class="help-block"></div>
        </div>
        <?= $form->field($model, 'ratio_percent') ?>
    </div>
    <hr/>
    <div class="form-group">
        <?= Html::submitButton(\Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>