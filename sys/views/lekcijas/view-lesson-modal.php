<?php
echo $this->render("@app/views/shared/modal", [
    'id' => 'lecture-modal-' . $id,
    'title' => '',
    'body' =>  $this->render('video', [
        'lecturefiles' => $lecturefiles,
        'videos' => $videos,
        'baseUrl' => $baseUrl,
        'thumbnail' => $videoThumb ?? '',
        'idPrefix' => 'lecture-modal' . $id,
    ]),
]);