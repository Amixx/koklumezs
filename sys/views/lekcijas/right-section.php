<?php
$backgroundImage = trim(
    $videoThumb
        ? 'url(' . $this->render('video_thumb', [
            'lecturefiles' => [
                0 => [
                    'file' => $playAlongFile,
                    'thumb' => $videoThumb
                    ]
                ],
                'videos' => $videos,
                'baseUrl' => $baseUrl]) . ')'
        : ""
);
?>

<div>
    <div class="text-center">
        <h4>Spēlēsim kopā</h4>
        <div>
            <div class="text-center lecture-wrap lecture-wrap-related">
                <a class="lecture-thumb" style="background-color: white; background-image: <?= $backgroundImage ?>;"></a>
            </div>
        </div>
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