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
        <?php /*
        <li class="nav-item">
            <a class="nav-link" id="hands-tab" data-toggle="tab" href="#hands" role="tab" aria-controls="hands" aria-selected="false">Roku kategorijas</a>
        </li>
        */ ?>
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
            <?= $this->render('difficulties',['difficulties' => $difficulties, 'studentGoals' => $studentGoals]) ?>
            <?php } ?>
        </div>
        <?php /*
        <div class="tab-pane fade" id="hands" role="tabpanel" aria-labelledby="hands-tab">
            <?php if($handdifficulties){  ?>
                <?= $this->render('handdifficulties',['handdifficulties' => $handdifficulties, 'studentHandGoals' => $studentHandGoals]) ?>  
            <?php } ?>
        </div>
        */ ?>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Saglabāt', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
