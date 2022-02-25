<div class="text-center">
    <ul class="PlanSuggestion__Container">
        <?php foreach ($planRecommendations as $plan) { ?>
            <li class="PlanSuggestion" data-plan-id="<?= $plan->id ?>" data-plan-price-id="<?= $plan->stripe_price_id ?>">
                <div class="PlanSuggestion__Header"><?= $plan->name ?></div>
                <?php if ($plan->description) { ?>
                    <div class="PlanSuggestion__Description"><?= $plan->description ?></div>
                <?php } ?>
                <div class="PlanSuggestion__Price"><?= Yii::t('app', 'Monthly cost (euro)') ?>: <?= $plan->price() ?></div>
                <div class="PlanSuggestion__Savings"><?= Yii::t('app', 'Save up to {0} euro a year', 92) ?></div>
                <?php if ($plan->allow_single_payment) { ?>
                    <div class="form-group PlanSuggestion__PaymentCheckbox">
                        <label class="control-label">
                            <input type="checkbox" name="payment_all_at_once">&nbsp;<?= Yii::t('app', 'I will pay in one installment (10% discount)') ?>
                        </label>
                    </div>
                <?php } ?>
                <button class="btn btn-success PlanSuggestion__CheckoutButton"><?= Yii::t('app', 'Choose') ?></button>
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