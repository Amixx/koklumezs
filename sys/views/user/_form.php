<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Users;
use  yii\jui\DatePicker;
/* @var $this yii\web\View */
/* @var $model app\models\Users */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item active">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Lietotāja dati</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="params-tab" data-toggle="tab" href="#params" role="tab" aria-controls="params" aria-selected="false">Parametri</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="hands-tab" data-toggle="tab" href="#hands" role="tab" aria-controls="hands" aria-selected="false">Roku kategorijas</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade active in" id="home" role="tabpanel" aria-labelledby="home-tab">
            <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                

            <?= $form->field($model, 'password')->passwordInput(['maxlength' => true,'value'=>""]) ?>    

            
            <?= $form->field($model, 'user_level')->dropDownList([ 'Admin' => 'Admin', 'Student' => 'Students', ], ['prompt' => '']) ?>

            <?= $form->field($model, 'status')->dropDownList([ Users::STATUS_INACTIVE => 'Nav aktīvs', Users::STATUS_ACTIVE => 'Aktīvs'], ['prompt' => '']) ?>

            <?= $form->field($model, 'goal')->textArea(['rows' => 6]) ?>

            <?= $form->field($model, 'dont_bother')->widget(DatePicker::classname(), ['dateFormat' => 'yyyy-MM-dd','language' => 'lv']) ?>    
        </div>
        <div class="tab-pane fade" id="params" role="tabpanel" aria-labelledby="params-tab">
            <?php if($difficulties){ ?>
                <h2>Parametri šobrīd</h2>      
                <?php      
                foreach($difficulties as $id => $name){  ?>
                <div class="form-group field-studentgoals">
                    <label class="control-label"for="studentgoals-title-now<?=$id?>"><?=$name?></label>
                    <select id="studentgoals-title-now<?=$id?>" class="form-control" name="studentgoals[now][<?=$id?>]" aria-required="true" aria-invalid="false">
                        <option value=""></option>
                        <?php for($a=1;$a<=10;$a++){ ?>
                        <option value="<?=$a?>" <?=(isset($studentGoals['Šobrīd'][$id]) AND  ($studentGoals['Šobrīd'][$id] == $a)) ? 'selected' : ''?>><?=$a?></option>
                        <?php } ?>
                    </select>
                    <div class="help-block"></div>
                </div>                       
            <?php } ?>       
            <?php } ?>
            <?php if($difficulties){  ?>
                <h2>Parametri vēlamie</h2>      
                <?php      
                foreach($difficulties as $id => $name){ ?>
                <div class="form-group field-studentgoals">
                    <label class="control-label"for="studentgoals-title-future<?=$id?>"><?=$name?></label>
                    <select id="studentgoals-title-future<?=$id?>" class="form-control" name="studentgoals[future][<?=$id?>]" aria-required="true" aria-invalid="false">
                        <option value=""></option>
                        <?php for($a=1;$a<=10;$a++){ ?>
                        <option value="<?=$a?>" <?=(isset($studentGoals['Vēlamais'][$id]) AND  ($studentGoals['Vēlamais'][$id] == $a)) ? 'selected' : ''?>><?=$a?></option>
                        <?php } ?>
                    </select>
                    <div class="help-block"></div>
                </div>
            <?php } ?>
            <?php } ?>
        </div>
        <div class="tab-pane fade" id="hands" role="tabpanel" aria-labelledby="hands-tab">
            <?php if($handdifficulties){  ?>
                <h2>Roku kategorijas</h2>      
                <?php      
                if($handdifficulties['left']){ ?>
                <hr />
                <h3>Kreisās rokas kategorijas</h3>      
                <?php foreach($handdifficulties['left'] as $id => $name){ ?>
                    <div class="form-group field-handdifficulties-title custom-control custom-checkbox mr-sm-2">
                        <input type="checkbox" class="custom-control-input" id="handdifficulties-title-<?=$id?>" name="studenthandgoals[<?=$id?>]" <?=isset($studentHandGoals[$id]) ? 'checked' : ''?> value="1" aria-required="false" aria-invalid="false" />
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
                        <input type="checkbox" class="custom-control-input" id="handdifficulties-title-<?=$id?>" name="studenthandgoals[<?=$id?>]" <?=isset($studentHandGoals[$id]) ? 'checked' : ''?> value="1" aria-required="false" aria-invalid="false" />
                        <label class="custom-control-label" for="handdifficulties-title-<?=$id?>"><?=$name?></label>
                        <div class="help-block"></div> 
                    </div>         
                <?php }
                } ?>       
            <?php } ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Saglabāt', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
