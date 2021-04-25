<?php
echo $this->render("@app/views/shared/modal", [
    'id' => 'lesson_modal_' . $id,
    'title' => '',
    'body' =>  $this->render('video', [
        'lectureVideoFiles' => $lecturefiles,
        'thumbnail' => $videoThumb ?? '',
        'idPrefix' => 'lesson_modal_' . $id,
    ]),
]);
