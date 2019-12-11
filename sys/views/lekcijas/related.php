<?php
use app\models\Lecturesfiles;
use yii\helpers\Html;
use yii\helpers\Url;
if($relatedLectures){
?>
<h2>Saistītās lekcijas</h2>
<hr />
<div class="lectures-related">
    
    <div class="row">
    <?php foreach ($relatedLectures as $model) {
        if(in_array($model->id,$userEvaluatedLectures)) continue;
    $lecturefiles = Lecturesfiles::getLectureFiles($model->id);
    ?>
        <div class="col-md-6 col-lg-3 text-center lecture-wrap">
            <a class="lecture-thumb" href="<?=Url::to(['lekcijas/lekcija', 'id' => $model->id])?>" style="background-image: url('<?=trim($model->thumb ? $this->render('video_thumb', ['lecturefiles' => [0 => ['file' => $model->file, 'thumb' => $model->thumb]], 'videos' => $videos, 'baseUrl' => $baseUrl]) :   $this->render('video_thumb', ['lecturefiles' => $lecturefiles, 'videos' => $videos, 'baseUrl' => $baseUrl]))?>');"></a>
            <?=$model->title?>
        </div>
    <?php } ?>        
    </div>
</div>
<?php } ?>