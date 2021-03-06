<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = \Yii::t('app', 'Lessons to assign and messages to send after registration');

?>
<div class="difficulties-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="container">
        <?php for ($i = 0; $i < 2; $i++) { ?>
            <div class="row">
                <?php for ($j = 0; $j < 2; $j++) { ?>
                <?= $this->render('_lessons', [
                        'item' => $conf[$i][$j],
                    ]);
                } ?>
            </div>
        <?php } ?>

        <hr>
        <div class="row">
            <div class="col-12">
                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'lesson_id')->dropDownList($lectures, ['prompt' => '', 'type' => 'number']) ?>

                <?= $form->field(
                    $model,
                    'for_students_with_instrument'
                )->dropDownList(
                    [
                        0 => \Yii::t('app', 'Does not have instrument'),
                        1 => \Yii::t('app', 'Has instrument')
                    ],
                    [
                        'prompt' => '-- ' . \Yii::t('app', 'Instrument') . ' --',
                        'type' => 'number'
                    ]
                )->label(\Yii::t('app', 'Assign to students')) ?>

                <?= $form->field(
                    $model,
                    'for_students_with_experience'
                )->dropDownList(
                    [
                        0 => \Yii::t('app', 'Does not have experience'),
                        1 => \Yii::t('app', 'Has experience')
                    ],
                    [
                        'prompt' => '-- ' . \Yii::t('app', 'Experience') . ' --',
                        'type' => 'number'
                    ]
                )->label(false) ?>

                <div class="form-group">
                    <?= Html::submitButton(\Yii::t('app', 'Add'), ['class' => 'btn btn-success']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>