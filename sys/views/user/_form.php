<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Users;
use  yii\jui\DatePicker;

$isTeacher = Users::isCurrentUserTeacher();
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

            <?= $form->field($model, 'password')->passwordInput(['maxlength' => true, 'value' => ""]) ?>

            <?php if (!$isTeacher) {
                echo $form->field($model, 'user_level')->dropDownList(['Admin' => 'Administrators', 'Student' => 'Students', 'Teacher' => 'Skolotājs'], ['prompt' => '']);
            ?>
                <label for="teacher_instrument">Instruments (tikai skolotājiem):</label>
            <?php
                echo Html::input("text", "teacher_instrument", "", ['class' => 'form-control']);
            }; ?>

            <?= $form->field($model, 'language')->dropDownList(['lv' => 'Latviešu', 'eng' => 'Angļu',], ['prompt' => '']) ?>

            <?= $form->field($model, 'subscription_type')->dropDownList(['free' => 'Par brīvu', 'paid' => 'Par maksu', 'lead' => 'Izmēģina',], ['prompt' => '']) ?>

            <?= $form->field($model, 'status')->dropDownList([Users::STATUS_INACTIVE => 'Nav aktīvs', Users::STATUS_ACTIVE => 'Aktīvs', Users::STATUS_PASSIVE => 'Pasīvs'], ['prompt' => '']) ?>

            <?= $form->field($model, 'goal')->textArea(['rows' => 6]) ?>

            <?= $form->field($model, 'dont_bother')->widget(DatePicker::classname(), ['dateFormat' => 'yyyy-MM-dd', 'language' => 'lv']) ?>

            <?= $form->field($model, 'allowed_to_download_files')->dropDownList([0 => 'Nē', 1 => 'Jā'], ['prompt' => '']) ?>
        </div>
        <div class="tab-pane fade" id="params" role="tabpanel" aria-labelledby="params-tab">
            <?php if ($difficulties) { ?>
                <?= $this->render('difficulties', ['difficulties' => $difficulties, 'studentGoals' => $studentGoals]) ?>
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