<?php
use yii\helpers\Url;
use \yii2mod\rating\StarRating;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Lekcija: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Lekcijas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;

?>
<!-- unpkg : use the latest version of Video.js -->
<link href="https://unpkg.com/video.js/dist/video-js.min.css" rel="stylesheet">
<script src="https://unpkg.com/video.js/dist/video.min.js"></script>
<div class="row">
    <div class="col-md-3 ">
        <?php foreach($userLectures as $lecture){  ?>
        <p><a href="<?=Url::to(['lekcijas/lekcija', 'id' => $lecture['lecture_id']]);?>"><?=$lecture->lecture->title?></a></p>
        <?php } ?>
    </div>
    <div class="border-left col-md-9">
        <h2 class="text-center"><?=$model->title?></h2>
        <?php if($lecturefiles){ ?>
            <?= $this->render('video', ['lecturefiles' => $lecturefiles,'videos' => $videos, 'baseUrl' => $baseUrl]); ?> 
            <?= $this->render('audio', ['lecturefiles' => $lecturefiles, 'audio' => $audio]); ?> 
            <?php } ?>
            
        <?=$model->description?>            
        <?php if($difficulties AND $lectureDifficulties){ 
                $sum = 0;
                foreach($difficulties as $id => $name){  
                    $continue = !isset($lectureDifficulties[$id]);
                    if($continue){
                        continue;
                    }
                    $sum += $lectureDifficulties[$id];
                } ?>
            <hr />               
            <div class="row">   
            <div class="col-md-12"><h3>Lekcijas sarežģītība: <?=$sum?></h3></div>
                <?php      
                foreach($difficulties as $id => $name){  
                    $continue = !isset($lectureDifficulties[$id]);
                    if($continue){
                        continue;
                    }
                    ?>
                <div class="col-md-3 text-center">
                   <?=$name?>: <?=$lectureDifficulties[$id]?>
                </div>                        
            <?php } ?>    
            </div> 
            <?php } ?>
        
            <?php if($handdifficulties AND $lectureHandDifficulties){  ?>
                <hr />
                <?php      
                if($handdifficulties['left']){ ?>
                <div class="row">  
                    <div class="col-md-12"> 
                        <h3>Kreisās rokas kategorijas</h3>                       
                    <ul>     
                    <?php foreach($handdifficulties['left'] as $id => $name){ 
                        $continue = !isset($lectureHandDifficulties[$id]);
                        if($continue){
                            continue;
                        }
                        ?>
                        <li>
                            <?=$name?>
                        </li>            
                    <?php } ?>
                    </ul>
                    </div>
                </div>
                <?php }
                if($handdifficulties['right']){ ?>                    
                <div class="row">
                    <div class="col-md-12">
                        <h3>Labās rokas kategorijas</h3>                      
                    <ul>  
                    <?php
                    foreach($handdifficulties['right'] as $id => $name){ 
                        $continue = !isset($lectureHandDifficulties[$id]);
                        if($continue){
                            continue;
                        }
                        ?>
                        <li>
                            <?=$name?>
                        </li>      
                    <?php } ?>
                    </ul>
                    </div>
                </div>  
                <?php } ?>       
            <?php } ?>
            <?php if($lecturefiles){ ?>
                <?= $this->render('docs', ['lecturefiles' => $lecturefiles, 'docs' => $docs]); ?> 
           <?php } ?>
          
            <?php if($evaluations AND $lectureEvaluations){  ?>
                <hr />
                <div class="row">
                    <div class="col-md-12">
                        <h3>Novērtē lekciju</h3>
                    </div>
                </div>
                <?php $form = ActiveForm::begin(); ?>
                <?php      
                foreach($evaluations as $id => $evaluation){ 
                    $continue = !isset($lectureEvaluations[$evaluation['id']]);
                    if($continue){
                        continue;
                    }
                    if($evaluation['type'] == 'teksts') { ?>
                    <div class="form-group field-election-election_description">
                        <label class="control-label" for="election-<?=$evaluation['id']?>"><?=$evaluation['title']?></label>
                        <textarea id="evaluations-title-<?=$evaluation['id']?>" class="form-control" rows="6" name="evaluations[<?=$evaluation['id']?>]"><?=isset($userLectureEvaluations[$evaluation['id']]) ? $userLectureEvaluations[$evaluation['id']] : ''?></textarea>    
                        <div class="help-block"></div>
                    </div>
                    <?php } else { ?>
                    <div class="form-group field-election-election_description">
                        <label class="control-label" for="election-<?=$evaluation['id']?>"><?=$evaluation['title']?></label>
                        <?=StarRating::widget([
                            'name' => 'evaluations[' . $evaluation['id'] . ']',
                            'value' => isset($userLectureEvaluations[$evaluation['id']]) ? $userLectureEvaluations[$evaluation['id']] : 0,
                            'clientOptions' => [
                                // Your client options
                                'id' => 'election-' . $evaluation['id'],
                                'required' => 'required',
                                'scoreName' => 'evaluations[' . $evaluation['id'] . ']'
                            ],
                        ]); ?>
                        <div class="help-block"></div>
                    </div>
                    <?php } ?>                                                 
                <?php } ?>   
                <div class="form-group">
                    <?= Html::submitButton('Iesniegt', ['class' => 'btn btn-success']) ?>
                </div>            
            </div>    
            <?php ActiveForm::end(); ?>
            <?php } ?>
        </div>
    </div>
</div>