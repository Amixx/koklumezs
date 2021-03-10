<div class="visible-xs">
<?php

use app\helpers\ThumbnailHelper;

$thumbStyle = ThumbnailHelper::getThumbnailStyle($model->play_along_file, $videoThumb, $videos);

$width = $model->play_along_file ? "38%" : "100%";
$marginTop = $model->play_along_file ? "60px" : "0px";
$btnClass = "btn btn-orange dropdown-toggle";
if($model->play_along_file) {
    $btnClass .= " btn-narrow";
}
?>
<div style="display: inline-block; width:<?= $width ?>; vertical-align: top; margin-top:8px">
    <?php if ($lecturefiles) { ?>
        <button type="button" class="<?= $btnClass ?>" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
            <?= \Yii::t('app', 'Lyrics and notes');?>
        </button>
        <div class="dropdown-menu dropdown-menu-lg-left">
            <?= $files = $this->render('docs', ['lecturefiles' => $lecturefiles, 'docs' => $docs]);?>
        </div> 
    <?php } ?>
    <p style="color:black; margin: <?= $marginTop ?> 0 0 6px"><?= Yii::t('app', 'Previous assignments in this lesson') ?></p>
</div>


<?php if($model->play_along_file){ ?>
    <div style="display: inline-block; width:60%;">
            <div>
                <div class="lecture-wrap">
                    <a class="lecture-thumb" data-toggle="modal" data-target="#lecture-modal-<?= $model->id ?>" style="<?= $thumbStyle ?>"></a>
                    <span class="lecture-title">Spēlēsim kopā</span>
                </div>
                <?= $this->render('view-lesson-modal', [
                    'baseUrl' => $baseUrl,
                    'videoThumb' => $videoThumb,
                    'videos' => $videos,
                    'lecturefiles' => [0 => ['title' => $model->title . " izspēle", 'file' => $model->play_along_file]],
                    'id' => $model->id,
                ]) ?>
            </div>
    </div>
<?php } ?>

<?php if ($relatedLectures) { ?>
    <?= $this->render('related', [
        'relatedLectures' => $relatedLectures,
        'lecturefiles' => $lecturefiles,
        'videos' => $videos,
        'baseUrl' => $baseUrl,
        'userEvaluatedLectures' => $userEvaluatedLectures,
        'videoThumb' => $videoThumb
    ])?>
<?php } ?>
</div>