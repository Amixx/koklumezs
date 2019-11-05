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
        <?php if($lecturefiles){
            $hasFiles = false;
            foreach($lecturefiles as $id => $file){ 
                $path_info = pathinfo($file['file']);
                if(in_array(strtolower($path_info['extension']),$videos)){
                    $hasFiles = true;
                }
            }    
            if($hasFiles){ 
            ?>
        <div class="row">
        <?php foreach($lecturefiles as $id => $file){ 
            $path_info = pathinfo($file['file']);
                if(!in_array(strtolower($path_info['extension']),$videos)){
                    continue;
                }
                ?>
                <div class="col-md-12">
                <p><?=$file['title']?></p>   
                <video
                    id="my-player<?=$id?>"
                    class="video-js vjs-layout-x-large"
                    controls
                    preload="auto"
                    poster="<?=$baseUrl?>/files/cover.jpg"
                    data-setup='{}'>
                <source src="<?=$file['file']?>" type="video/<?=strtolower($path_info['extension'])?>"></source>
                <p class="vjs-no-js">
                    To view this video please enable JavaScript, and consider upgrading to a
                    web browser that
                    <a href="https://videojs.com/html5-video-support/" target="_blank">
                    supports HTML5 video
                    </a>
                </p>
                </video>
                <script>
                    var player = videojs('my-player<?=$id?>',{responsive: true,width:400});
                </script>
                </div>
                <hr />                            
            <?php } ?>
            </div>
            <hr />  
            <?php } 
            $hasFiles = false;
            foreach($lecturefiles as $id => $file){ 
                $path_info = pathinfo($file['file']);
                if(in_array(strtolower($path_info['extension']),$audio)){
                    $hasFiles = true;
                }
            }    
            if($hasFiles){ 
            ?>
        <div class="row">
        <?php foreach($lecturefiles as $id => $file){ 
            $path_info = pathinfo($file['file']);
                if(!in_array(strtolower($path_info['extension']),$audio)){
                    continue;
                }
                ?>
                <div class="col-md-12">
                    <p><?=$file['title']?></p>   
                    <audio controls>
                        <source src="<?=$file['file']?>" type="audio/<?=strtolower($path_info['extension'])?>">
                        Your browser does not support the audio element.
                    </audio>
                
                </div>      
            <?php } ?>           
        </div>
        <hr /> 
        <?php }
        
        } ?>
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
            <h3>Lekcijas sarežģītība: <?=$sum?></h3> 
            <div class="row">   
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
                <h3>Kreisās rokas kategorijas</h3>      
                <?php foreach($handdifficulties['left'] as $id => $name){ 
                     $continue = !isset($lectureHandDifficulties[$id]);
                     if($continue){
                         continue;
                     }
                    ?>
                    <div class="col-md-3">
                        <?=$name?>
                    </div>            
                <?php } ?>
                </div>
                <?php }
                if($handdifficulties['right']){ ?>                    
                    <div class="row">
                    <h3>Labās rokas kategorijas</h3>    
                    <?php
                    foreach($handdifficulties['right'] as $id => $name){ 
                        $continue = !isset($lectureHandDifficulties[$id]);
                        if($continue){
                            continue;
                        }
                        ?>
                        <div class="col-md-3">
                            <?=$name?>
                        </div>      
                <?php } ?>
                </div>  
                <?php } ?>       
            <?php } ?>
            <?php if($lecturefiles){  
                $hasFiles = false;
                foreach($lecturefiles as $id => $file){ 
                    $path_info = pathinfo($file['file']);
                    if(in_array(strtolower($path_info['extension']),$docs)){
                        $hasFiles = true;
                    }
                }    
                if($hasFiles){       
                ?>  
                <hr />
                <h3>Ar lekciju saistītie materiāli:</h3>      
                <div class="row">
                <?php foreach($lecturefiles as $id => $file){ 
                    $path_info = pathinfo($file['file']);
                    if(!in_array(strtolower($path_info['extension']),$docs)){
                        continue;
                    }
                    ?>
                    <div class="col-md-3 text-center"> 
                        <a target="_blank" href="<?=$file['file']?>"><?=$file['title']?></a>   
                    </div>            
                <?php } ?>
                </div>
            <?php }
        } ?>
          
            <?php if($evaluations){  ?>
                <hr />
                <h3>Novērtē lekciju</h3>
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
<?php

?>