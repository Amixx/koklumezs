<?php 

use yii\helpers\Html;

$heartClasses = $isFavourite 
    ? 'glyphicon-heart LectureEvaluations__Heart--active'
    : 'glyphicon-heart-empty';

$urlToNextLesson = "lekcijas/lekcija/$nextLessonId";

?>

<div class="col-sm-4">
    <p>
        Novērtē, cik viegli/grūti gāja ar uzdevumu?
    </p>
    <div>
        vēl nezin kas te būs (zvaigznes nē, moš emojīši)
    </div>
</div>
<div class="col-sm-4">
    <span class="LectureEvaluations__FavouriteText">Vai vēlies šo paspēlēt vēl? Atzīmē ar "Patīk"!</span>
    <?= Html::beginForm(["/lekcijas/toggle-is-favourite?lectureId=$uLecture->lecture_id"], 'get') ?>
    <button type="submit" class="removeBtnStyle"><span class="glyphicon LectureEvaluations__Heart <?= $heartClasses ?>"></span></button>
    <?= Html::endForm() ?>
    
</div>
<div class="col-sm-4">
    <?= Html::a(\Yii::t('app', 'Next lesson'), [$urlToNextLesson], ['class' => 'btn btn-primary']); ?>
</div>