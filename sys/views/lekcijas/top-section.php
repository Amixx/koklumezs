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
        <p>Hei! Kā tev veicās ar šo uzdevumu?</p>
        <div>
            <!-- pagaidām - kamēr nav emojīši -->
            <?php for($i = 0; $i < 5; $i++){ ?>
                <span class="glyphicon glyphicon-heart LectureEvaluations__Heart"></span>
            <?php } ?>
        </div>       
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
                'evaluations' => $evaluations, 
                'lectureEvaluations' => $lectureEvaluations,
                'force' => $force,
                'nextLessonId' => $nextLessonId,
            ]); 
        } else {
            echo Html::a(\Yii::t('app', 'Next lesson'), [$urlToNextLesson], ['class' => 'btn btn-orange']);
        }
    } ?>
    </td>
</tr>
</table>