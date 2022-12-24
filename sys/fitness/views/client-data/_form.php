<?php

/* @var $model app\fitness\models\ClientData */

use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$ckeditorOptions = ElFinder::ckeditorOptions(
    'elfinder',
    [
        'preset' => 'full',
        'filter' => ['image', 'application/pdf', 'text', 'video'],
    ]
);

?>

<div class="lectures-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="tab-pane fade active in" id="home" role="tabpanel" aria-labelledby="home-tab">
        <?= $form->field($model, 'bodyweight')->textInput(['type' => 'number']) ?>
        <?= $form->field($model, 'goal')->textInput() ?>
        <?= $form->field($model, 'experience')->textInput() ?>
        <?= $form->field($model, 'injuries')->textInput() ?>
        <?= $form->field($model, 'problems')->textInput() ?>
        <?= $form->field($model, 'operations')->textInput() ?>
        <?= $form->field($model, 'blood_analysis')->textarea() ?>
        <?= $form->field($model, 'emotional_state')->textarea() ?>
        <?= $form->field($model, 'notes')->widget(CKEditor::class, ['editorOptions' => $ckeditorOptions]) ?>
    </div>
    <hr/>
    <div class="form-group">
        <?= Html::submitButton(\Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>