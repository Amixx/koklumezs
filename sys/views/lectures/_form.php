<?php
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Lectures;
/* @var $this yii\web\View */
/* @var $model app\models\Lectures */
/* @var $form yii\widgets\ActiveForm */
$ckeditorOptions = ElFinder::ckeditorOptions('elfinder',
[
    'preset' => 'full', //разработанны стандартные настройки basic, standard, full данную возможность не обязательно использовать
    'inline' => false, //по умолчанию false
    'filter'        => ['image', 'application/pdf', 'text', 'video'] ,    // фильтр файлов, можно задать массив фильтров https://github.com/Studio-42/elFinder/wiki/Client-configuration-options#wiki-onlyMimes
]);
?>

<div class="lectures-form">

    <?php $form = ActiveForm::begin(); ?>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item active">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Lekcija</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="params-tab" data-toggle="tab" href="#params" role="tab" aria-controls="params" aria-selected="false">Parametri</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="hands-tab" data-toggle="tab" href="#hands" role="tab" aria-controls="hands" aria-selected="false">Roku kategorijas</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="evaluations-tab" data-toggle="tab" href="#evaluations" role="tab" aria-controls="evaluations" aria-selected="false">Novērtējumi</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="files-tab" data-toggle="tab" href="#files" role="tab" aria-controls="files" aria-selected="false">Faili</a>
        </li>        
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade active in" id="home" role="tabpanel" aria-labelledby="home-tab">
            <?= $form->field($model, 'title')->textInput() ?>
            <?= $form->field($model, 'description')->widget(CKEditor::className(),[
                'editorOptions' => $ckeditorOptions,
            ]) ?>
            <?= $form->field($model, 'complexity')->dropDownList(Lectures::getComplexity(), ['prompt' => '']) ?>
        </div>
        <div class="tab-pane fade" id="params" role="tabpanel" aria-labelledby="params-tab">
            <?php if($difficulties){  ?>
                <h2>Parametri</h2>      
                <?php      
                foreach($difficulties as $id => $name){  ?>
                <div class="form-group field-studentgoals">
                    <label class="control-label"for="difficulties-title<?=$id?>"><?=$name?></label>
                    <select id="difficulties-title<?=$id?>" class="form-control" name="difficulties[<?=$id?>]" aria-required="true" aria-invalid="false">
                        <option value=""></option>
                        <?php for($a=1;$a<=10;$a++){ ?>
                        <option value="<?=$a?>" <?=(isset($lectureDifficulties[$id]) AND ($lectureDifficulties[$id] == $a)) ? 'selected' : ''?>><?=$a?></option>
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
        </div>
        <div class="tab-pane fade" id="evaluations" role="tabpanel" aria-labelledby="evaluations-tab">
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
        </div>
        <div class="tab-pane fade" id="files" role="tabpanel" aria-labelledby="files-tab">
            <h2>Pievienotie faili</h2>
            <p>
                <a target="_blank" class="btn btn-success" href="/sys/lecturesfiles/create?lecture_id=<?=$model->id?>">Pievienot failu</a>
            </p>       
            <?php if($lecturefiles){  ?>        
                <table class="table table-striped table-bordered">
                <?php foreach($lecturefiles as $id => $file){ ?>
                    <tr> 
                        <td><?=$file['title']?></td>       
                        <td>
                            <a target="_blank" href="/sys/lecturesfiles/<?=$file['id']?>" title="Skatīt" aria-label="Skatīt" data-pjax="0"><span class="glyphicon glyphicon-eye-open"></span></a> 
                            <a target="_blank" href="/sys/lecturesfiles/update/<?=$file['id']?>" title="Rediģēt" aria-label="Rediģēt" data-pjax="0"><span class="glyphicon glyphicon-pencil"></span></a> 
                            <a href="/sys/lecturesfiles/delete/<?=$file['id']?>" title="Dzēst" aria-label="Dzēst" data-pjax="0" data-confirm="Vai Jūs tiešām vēlaties dzēst šo failu?" data-method="post"><span class="glyphicon glyphicon-trash"></span></a>
                        </td>
                    </tr>            
                <?php } ?>
                </table>
            <?php } ?>

        </div>
    </div>
    
    <div class="form-group">
        <?= Html::submitButton('Saglabāt', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
