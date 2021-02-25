<?php
$this->title = \Yii::t('app', 'FAQs');

?>
<div class="difficulties-index">
    <div>
        <h3 class="row text-center hidden-xs">
            <div class="col-sm-6"><?= Yii::t("app", "Question") ?></div>
            <div class="col-sm-6"><?= Yii::t("app", "Answer") ?></div>
        </h3>
        <?php foreach($faqs as $faq){ ?>
            <div class="row Buj__Item">
                <div class="col-sm-6"><?= $faq["question"] ?>:</div>
                <div class="col-sm-6"><?= $faq["answer"] ?></div>
            </div>
        <?php } ?>   
    </div>
</div>