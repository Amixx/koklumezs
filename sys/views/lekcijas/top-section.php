<?php 

use yii\helpers\Html;

$heartClasses = $uLecture->is_favourite 
    ? 'glyphicon-heart LectureEvaluations__Heart--active'
    : 'glyphicon-heart-empty';

$urlToNextLesson = "lekcijas/lekcija/$nextLessonId";

?>

<h3 class="text-center">
    <?= $title ?>
</h3>

<table class="LessonTop">   
<tr>
    <td>
        <?= $this->render("amount-evaluation", [
            'difficultyEvaluation' => $difficultyEvaluation, 
            'force' => $force,
            'redirectToNext' => false,
        ]) ?>
    </td>
    <td>
        <?= Html::button(\Yii::t('app', 'Vārdi un notis'), ['class' => 'btn btn-orange', $urlToNextLesson]); ?>
    </td>
</tr>
<tr>
    <td>
        <?= Html::beginForm(["/lekcijas/toggle-is-favourite?lectureId=$uLecture->lecture_id"], 'get') ?>
        <label for="heart" class="LectureEvaluations__FavouriteText">
            <button type="submit" class="removeBtnStyle"><span class="glyphicon LectureEvaluations__Heart <?= $heartClasses ?>"></span></button>
            Pievienot mīļākajām nodarbībām
        </label>
        <?= Html::endForm() ?>        
    </td>
    <td>
    <?php if($nextLessonId){
        if (!$hasEvaluatedLesson) {
            echo $this->render('alertEvaluation', [
                'force' => $force,
                'difficultyEvaluation' => $difficultyEvaluation,
            ]); 
        } else {
            echo Html::a(\Yii::t('app', 'Next lesson'), [$urlToNextLesson], ['class' => 'btn btn-orange']);
        }
    } ?>
    </td>
</tr>
</table>