<?php
use yii\helpers\Url;

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
        <?php if($difficulties AND $lectureDifficulties){ ?>
            <?= $this->render('difficulties',['difficulties' => $difficulties, 'lectureDifficulties' => $lectureDifficulties]) ?>    
        <?php } ?>        
        <?php if($handdifficulties AND $lectureHandDifficulties){  ?>
            <?= $this->render('handdifficulties',['handdifficulties' => $handdifficulties, 'lectureHandDifficulties' => $lectureHandDifficulties]) ?>      
        <?php } ?>
        <?php if($lecturefiles){ ?>
            <?= $this->render('docs', ['lecturefiles' => $lecturefiles, 'docs' => $docs]); ?> 
        <?php } ?>          
        <?php if($evaluations AND $lectureEvaluations){  ?>
            <?= $this->render('evaluations', ['evaluations' => $evaluations, 'lectureEvaluations' => $lectureEvaluations]) ?>
        <?php } ?>
        <?php if($relatedLectures){ ?>
            <?= $this->render('related',['relatedLectures' => $relatedLectures,'lecturefiles' => $lecturefiles,'videos' => $videos, 'baseUrl' => $baseUrl,'userEvaluatedLectures' => $userEvaluatedLectures]) ?>
        <?php } ?>
        </div>
    </div>
</div>