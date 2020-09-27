Sveiki <?= $userFirstName ?>,

Jums ir piešķirta jauna nodarbība - <?= $lectureName ?>

<?php if (isset($teacherMessage) && $teacherMessage) {
    echo $teacherMessage;
} ?>