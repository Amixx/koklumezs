<?php 

use yii\helpers\Html;

$heartClasses = $isFavourite 
    ? 'glyphicon-heart LectureEvaluations__Heart--active'
    : 'glyphicon-heart-empty';

$urlToNextLesson = "lekcijas/lekcija/$nextLessonId";

?>

<table class="LessonTop">
<tr>
    <td>
        <span>
            <h2 class="text-left">
                <?= $title ?>
            </h2>
        </span>
    </td>
    <td>
        <?= Html::button(\Yii::t('app', 'Vārdi un notis'), ['class' => 'btn btn-primary', $urlToNextLesson]); ?>
    </td>
</tr>    
<tr rowspan="2">
    <td>
    Atzīmē, cik viegli/grūti gāja ar uzdevumu?
    </td>
    <td>
        <span class="LectureEvaluations__FavouriteText">Vai vēlies šo paspēlēt vēl? Atzīmē ar "Patīk"!</span>
        <?= Html::beginForm(["/lekcijas/toggle-is-favourite?lectureId=$uLecture->lecture_id"], 'get') ?>
        <button type="submit" class="removeBtnStyle"><span class="glyphicon LectureEvaluations__Heart <?= $heartClasses ?>"></span></button>
        <?= Html::endForm() ?>
    </td>
</tr>
<tr>
    <td class="text-center">
        <?= Html::a(\Yii::t('app', 'Apstiprināt'), [$urlToNextLesson], ['class' => 'btn btn-primary']); ?>
    </td>
    <td>
    <?php if($nextLessonId){
        Html::a(\Yii::t('app', 'Next lesson'), [$urlToNextLesson], ['class' => 'btn btn-primary']);
    } ?>
    </td>
</tr>
</table>