<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Evaluations */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="evaluations-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput() ?>

    <?= $form->field($model, 'type')->dropDownList([ 'zvaigznes' => 'Zvaigznes', 'teksts' => 'Teksts', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'stars')->textInput() ?>

    <div class="form-group row">
        <div class="col-md-12">
        <label class="control-label" for="evaluations-starstext">Zvaigžņu teksti</label>
        <select class="select2 tags" id="evaluations-starstext" name="stars_texts[]" multiple>
        <?php 
        if(!empty($stars_texts)){
            foreach($stars_texts as $id => $stars_text){ ?>
                <option value="<?=$stars_text?>" selected><?=$stars_text?></option>
            <?php }
        }else{ ?>
        <option></option>
        <?php } ?>
        </select>
        </div>
    </div>

    <?= $form->field($model, 'is_scale')->checkBox(['value' => 1]) ?>
    
    <div class="form-group">
        <?= Html::submitButton('Saglabāt', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
