<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Lectures;
/* @var $this yii\web\View */
/* @var $model app\models\Lectures */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lectures-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput() ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'complexity')->dropDownList(Lectures::getComplexity(), ['prompt' => '']) ?>

    <?php if($difficulties){  ?>      
        <?php      
        foreach($difficulties as $id => $name){ ?>
        <div class="form-group field-difficulties-title">
            <label class="control-label" for="difficulties-title-<?=$id?>"><?=$name?></label>
            <input type="number" min="0" max="10" id="difficulties-title-<?=$id?>" class="form-control" name="difficulties[<?=$id?>]" value="<?=isset($lectureDifficulties[$id]) ? $lectureDifficulties[$id] : ''?>" aria-required="true" aria-invalid="false" />
            <div class="help-block"></div>
        </div>        
       <?php } ?>       
    <?php } ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
