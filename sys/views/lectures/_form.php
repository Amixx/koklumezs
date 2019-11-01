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
        <h2>Parametri</h2>      
        <?php      
        foreach($difficulties as $id => $name){ ?>
        <div class="form-group field-difficulties-title">
            <label class="control-label" for="difficulties-title-<?=$id?>"><?=$name?></label>
            <input type="number" min="0" max="10" id="difficulties-title-<?=$id?>" class="form-control" name="difficulties[<?=$id?>]" value="<?=isset($lectureDifficulties[$id]) ? $lectureDifficulties[$id] : ''?>" aria-required="false" aria-invalid="false" />
            <div class="help-block"></div>
        </div>        
       <?php } ?>       
    <?php } ?>

    <?php if($handdifficulties){  ?>
        <h2>Roku kategorijas</h2>      
        <?php      
        if($handdifficulties['left']){ ?>
        <hr />
        <h3>Kreisās rokas kategorijas</h3>      
        <?php foreach($handdifficulties['left'] as $id => $name){ ?>
            <div class="form-group field-handdifficulties-title custom-control custom-checkbox mr-sm-2">
                <input type="checkbox" class="custom-control-input" id="handdifficulties-title-<?=$id?>" name="handdifficulties[<?=$id?>]" <?=isset($lectureHandDifficulties[$id]) ? 'checked' : ''?> value="1" aria-required="false" aria-invalid="false" />
                <label class="custom-control-label" for="handdifficulties-title-<?=$id?>"><?=$name?></label>
                <div class="help-block"></div> 
            </div>                  
        <?php }
        }
        if($handdifficulties['right']){ ?>
            <h3>Labās rokas kategorijas</h3>    
            <hr />  
            <?php
            foreach($handdifficulties['right'] as $id => $name){ ?>
            <div class="form-group field-handdifficulties-title custom-control custom-checkbox mr-sm-2">
                <input type="checkbox" class="custom-control-input" id="handdifficulties-title-<?=$id?>" name="handdifficulties[<?=$id?>]" <?=isset($lectureHandDifficulties[$id]) ? 'checked' : ''?> value="1" aria-required="false" aria-invalid="false" />
                <label class="custom-control-label" for="handdifficulties-title-<?=$id?>"><?=$name?></label>
                <div class="help-block"></div> 
            </div>         
        <?php }
        } ?>       
    <?php } ?>

    <?php if($evaluations){  ?>
        <h2>Novērtējumi</h2>
        <hr />      
        <?php      
         foreach($evaluations as $id => $evaluation){ ?>
            <div class="form-group field-evaluations-title custom-control custom-checkbox mr-sm-2">
                <input type="checkbox" class="custom-control-input" id="evaluations-title-<?=$evaluation['id']?>" name="evaluations[<?=$evaluation['id']?>]" <?=isset($lectureEvaluations[$evaluation['id']]) ? 'checked' : ''?> value="1" aria-required="false" aria-invalid="false" />
                <label class="custom-control-label" for="evaluations-title-<?=$evaluation['id']?>"><?=$evaluation['title']?> <small>[<?=$evaluation['type']?>]</small></label>
                <div class="help-block"></div> 
            </div>         
        <?php } ?>       
    <?php } ?>

    <div class="form-group">
        <?= Html::submitButton('Saglabāt', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
