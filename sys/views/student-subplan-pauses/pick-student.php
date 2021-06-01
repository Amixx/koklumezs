<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = \Yii::t('app', 'Create plan pause');
['label' => \Yii::t('app', 'Plan pauses'), 'url' => ['index']];
?>
<div class="difficulties-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div>
        <?php $form = ActiveForm::begin([
            'action' => ['teacher-create'],
            'method' => 'get',
        ]); ?>

        <div class="form-group">
            <?= Html::dropDownList('userId', null, $students, [
                'prompt' => Yii::t('app', 'Choose a student'),
            ]) ?>
        </div>



        <div class="form-group">
            <?= Html::submitButton(\Yii::t('app', 'Continue'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>