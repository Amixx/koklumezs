<?php $this->title = \Yii::t('app', 'Lessons'); ?>
<div class="lectures-index">
    <div class="row">
        <div class="col-12">
            <h4 class="LectureOverview__Title"> <?= \Yii::t('app',  'Welcome back! If you encounter any problems contact us! Good luck!') ?> </h4>
        </div>
    </div>
    <div class="row">
        <?php
        $emptyText = $isFitnessSchool
            ? 'Congratulations! You\'ve seen all new workouts'
            : 'Congratulations! You\'ve seen all new lessons';

        echo $this->render('favouriteANDnew', [
            'userLessons' => $newLessons,
            'divTitle' => 'New lessons',
            'clickableTitle' => 'All new lessons',
            'type' => 'new',
            'emptyText' => $emptyText,
            'videoThumb' => $videoThumb,
            'nextLessons' => $nextLessons,
            'isNextLesson' => $isNextLesson,
            'renderRequestButton' => $renderRequestButton,
            'isActive' => $isActive,
            'teacherPortrait' => $teacherPortrait,
            'isStudent' => $isStudent,
            'isFitnessSchool' => $isFitnessSchool,
        ]) ?>
        <?=
        $this->render('favouriteANDnew', [
            'userLessons' => $favouriteLessons,
            'divTitle' => 'Favourite lessons',
            'clickableTitle' => 'All favourite lessons',
            'type' => 'favourite',
            'emptyText' => 'You have not added any lessons to this section yet. You can do this by marking in any lesson that you want to add it to this section.',
            'videoThumb' => $videoThumb,
            'renderRequestButton' => false,
            'teacherPortrait' => $teacherPortrait,
            'isStudent' => $isStudent,
        ]) ?>
    </div>

    <?php
    $session = Yii::$app->session;

    if ($session->has("renderPostRegistrationModal") && $session->get("renderPostRegistrationModal")) {
        echo $this->render('post-registration-modal');
    }

    if ($renderPlanSuggestions) {
        echo $this->render("@app/views/shared/modal", [
            'id' => 'plan-suggestion-modal',
            'title' => '',
            'bodyFileName' => "/lekcijas/plan-suggestion-modal-body",
            'large' => true,
            'bodyFileParams' => [
                'planRecommendations' => $planRecommendations,
            ],
        ]);
    }
    ?>
</div>