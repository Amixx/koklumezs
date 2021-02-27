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
        <?php if ($lecturefiles) { ?>
            <div class="btn-group">
                <button type="button" class="btn btn-orange dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                    <?= \Yii::t('app', 'Lyrics and notes');?>
                </button>
                <div class="dropdown-menu dropdown-menu-lg-left">
                    <?= $files = $this->render('docs', ['lecturefiles' => $lecturefiles, 'docs' => $docs]);?>
                </div>
            </div>
        <?php } ?>
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