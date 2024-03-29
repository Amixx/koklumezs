<div class="text-center">
    <ul class="PlanSuggestion__Container">
        <?php foreach ($planRecommendations['plans'] as $plan) { ?>
            <li class="PlanSuggestion" data-plan-id="<?= $plan->id ?>">
                <div class="PlanSuggestion__Header"><?= $plan->name ?></div>
                <?php if ($plan->description) { ?>
                    <div class="PlanSuggestion__Description"><?= $plan->description ?></div>
                <?php } ?>
                <?php if ($plan->stripe_single_price_id) { ?>
                    <div class="PlanSuggestion__Option single">
                        <h5>Maksā vienā maksājumā - ietaupi līdz 10%!</h5>
                        <div class="PlanSuggestion__Price">
                            <?= Yii::t('app', 'Total cost') ?>:
                            <?= $planRecommendations['planPrices'][$plan->id]['single'] ?>
                            €
                        </div>
                        <button class="btn btn-success PlanSuggestion__CheckoutButton" data-price-type="single">
                            <?= Yii::t('app', 'Choose') ?>
                        </button>
                    </div>
                <?php } ?>
                <?php if ($plan->stripe_recurring_price_id) { ?>
                    <div class="PlanSuggestion__Option recurring">
                        <h5>Maksā katru mēnesi</h5>
                        </h5>
                        <div class="PlanSuggestion__Price">
                            <?= Yii::t('app', 'Monthly cost') ?>:
                            <?= $planRecommendations['planPrices'][$plan->id]['recurring'] ?>
                            €
                        </div>
                        <button class="btn btn-success PlanSuggestion__CheckoutButton" data-price-type="recurring">
                            <?= Yii::t('app', 'Choose') ?>
                        </button>
                    </div>
                <?php } ?>
            </li>
        <?php } ?>
        <li class="PlanSuggestion__Payment" style="display: none">
            <div class="lds-roller" id="payment-spinner" style="display: none">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
            <div class="PlanSuggestion__PaymentInner">
                <button class="btn btn-primary PlanSuggestion__CancelPayment" style="display:none"><?= Yii::t('app', 'Choose a different plan') ?></button>
                <div id="payment-element"></div>
                <div class="PlanSuggestion__ButtonContainer" style="display: none">
                    <button class="btn btn-success PlanSuggestion__ConfirmPaymentButton"><?= Yii::t('app', 'Confirm payment') ?></button>
                </div>
            </div>
            <div id="payment-error" style="display:none">
                <h2><?= Yii::t('app', 'An error was encountered while processing the payment') ?></h2>
                <p id="payment-error-message" class="text-danger"></p>
                <p id="payment-error-code" class="text-muted"><?= Yii::t('app', 'Error code') ?>: </p>
                <button class="btn btn-primary PlanSuggestion__RetryPaymentButton"><?= Yii::t('app', 'Try again') ?></button>
            </div>
        </li>
    </ul>

</div>