<?php
echo $this->render("@app/views/shared/modal", [
    'id' => 'lecture-modal-' . $id,
    'title' => '',
    'body' =>  $this->render('video', [
        'lectureVideoFiles' => $lecturefiles,
        'thumbnail' => $videoThumb ?? '',
        'idPrefix' => 'lecture-modal' . $id,
    ]),
]);