<?php
use app\helpers\ThumbnailHelper;

$thumbStyle = ThumbnailHelper::getThumbnailStyle($model->play_along_file, $videoThumb, $videos);
?>

<div>
    <div class="text-center">
        <?php if($model->play_along_file){ ?>
            <h4>Spēlēsim kopā</h4>
            <div>
                <div class="text-center lecture-wrap lecture-wrap-related">
                    <a class="lecture-thumb" data-toggle="modal" data-target="#lecture-modal-<?= $model->id ?>" style="<?= $thumbStyle ?>"></a>
                </div>
                <?= $this->render('view-lesson-modal', [
                    'baseUrl' => $baseUrl,
                    'videoThumb' => $videoThumb,
                    'videos' => $videos,
                    'lecturefiles' => [0 => ['title' => $model->title . " izspēle", 'file' => $model->play_along_file]],
                    'id' => $model->id,
                ]) ?>
            </div>
        <?php } ?>
        
    </div>
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