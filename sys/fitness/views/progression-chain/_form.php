<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="lectures-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="tab-pane fade active in" id="home" role="tabpanel" aria-labelledby="home-tab">
        <?= $form->field($model, 'title')->textInput() ?>
    </div>
    <?php if (isset($progressionChainExercises)) { ?>
        <div>
            <?php for ($i = 0; $i <= 10; $i++) { ?>
                <?php if ($i > 0) { ?>
                    <span style="display: flex; align-items: last baseline">
                      <span>+</span>
                        <?= $form->field($progressionChainExercises[$i], "[$i]difficulty_increase_percent")->textInput(['style' => 'width: 60px'])->label(false) ?>
                        <span>%</span>
                    </span>
                <?php } ?>

                <?= $form->field($progressionChainExercises[$i], "[$i]exercise_id")->dropDownList(
                    $exerciseSelectOptions,
                    [
                        'prompt' => '-- ' . \Yii::t('app', 'Choose') . ' --',
                    ]
                )->label(false) ?>
            <?php } ?>
        </div>
    <?php } ?>

    <hr/>
    <div class="form-group">
        <?= Html::submitButton(\Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>