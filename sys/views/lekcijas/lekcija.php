<?php
use yii\helpers\Url;
use \yii2mod\rating\StarRating;
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
        <h3 class="text-center"><?=$model->title?></h3>
        <div class="row">
        <?php foreach($lecturefiles as $id => $file){ 
            $path_info = pathinfo($file['file']);
                if(in_array($path_info['extension'],$docs)){
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
                    poster=""
                    data-setup='{}'>
                <source src="<?=$file['file']?>" type="video/<?=$path_info['extension']?>"></source>
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
                
                            
            <?php } ?>
        </div>
        <?=$model->description?>
        <hr />        
        <?php if($difficulties){  ?>
            <h4>Lekcijas sarežģītība:</h4> 
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
        
            <?php if($handdifficulties){  ?>
                <hr />
                <h4>Roku kategorijas</h4>
                <?php      
                if($handdifficulties['left']){ ?>
                <div class="row">   
                <h5>Kreisās rokas kategorijas</h5>      
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
                    <h5>Labās rokas kategorijas</h5>    
                    <div class="row">
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
            <?php if($lecturefiles){  ?>  
                <hr />
                <h4>Ar lekciju saistītie materiāli:</h4>      
                <div class="row">
                <?php foreach($lecturefiles as $id => $file){ 
                    $path_info = pathinfo($file['file']);
                    if(in_array($path_info['extension'],$videos)){
                        continue;
                    }
                    ?>
                    <div class="col-md-3 text-center"> 
                        <a target="_blank" href="<?=$file['file']?>"><?=$file['title']?></a>   
                    </div>            
                <?php } ?>
                </div>
            <?php } ?>
            <?php if($evaluations){  ?>
                <hr />
                <h4>Novērtējumi</h4>
                <?php      
                foreach($evaluations as $id => $evaluation){ 
                    $continue = !isset($lectureEvaluations[$evaluation['id']]);
                    if($continue){
                        continue;
                    }
                    if($evaluation['type'] == 'teksts') { ?>
                    <div class="form-group field-election-election_description">
                        <label class="control-label" for="election-<?=$evaluation['id']?>"><?=$evaluation['title']?></label>
                        <textarea id="evaluations-title-<?=$evaluation['id']?>" class="form-control" rows="6" name="evaluations[<?=$evaluation['id']?>]"></textarea>    
                        <div class="help-block"></div>
                    </div>
                    <?php } else { ?>
                    <div class="form-group field-election-election_description">
                        <label class="control-label" for="election-<?=$evaluation['id']?>"><?=$evaluation['title']?></label>
                        <?=StarRating::widget([
                            'name' => 'evaluations[' . $evaluation['id'] . ']',
                            'value' => 0,
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
                </div>      
            <?php } ?>
        </div>
    </div>
</div>
<?php

?>