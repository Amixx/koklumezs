<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = \Yii::t('app', 'Lessons to assign after registration');

?>
<div class="difficulties-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="container">
        <div class="row">
            <?= $this->render('_lessons', [
                'title' => 'Without instrument; without experience',
                'model' => $registrationLessons['without_instrument']['without_experience'],
            ]) ?>
            <?= $this->render('_lessons', [
                'title' => 'Without instrument; with experience',
                'model' => $registrationLessons['without_instrument']['with_experience'],
            ]) ?>
        </div>
        <div class="row">
            <?= $this->render('_lessons', [
                'title' => 'With instrument; without experience',
                'model' => $registrationLessons['with_instrument']['without_experience'],
            ]) ?>
            <?= $this->render('_lessons', [
                'title' => 'With instrument; with experience',
                'model' => $registrationLessons['with_instrument']['with_experience'],
            ]) ?>
        </div>
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
                        0 => 'nav instrumenta',
                        1 => 'ir instrumentu'
                    ],
                    [
                        'prompt' => '-- instruments --',
                        'type' => 'number'
                    ]
                )->label('Kuriem skolēniem piešķirt nodarbību') ?>

                <?= $form->field(
                    $model, 
                    'for_students_with_experience'
                )->dropDownList(
                    [
                        0 => 'bez pieredzes',
                        1 => 'ar pieredzi'
                    ], [
                        'prompt' => '-- pieredze --',
                        'type' => 'number'
                    ])->label(false) ?>

                <div class="form-group">
                    <?= Html::submitButton(\Yii::t('app', 'Add'), ['class' => 'btn btn-success']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>