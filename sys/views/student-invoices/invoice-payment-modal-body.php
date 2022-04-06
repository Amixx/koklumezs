<div class="text-center">
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
        <div id="payment-element"></div>
        <div class="PlanSuggestion__ButtonContainer" style="display: none">
            <button class="btn btn-success ConfirmInvoicePayment"><?= Yii::t('app', 'Confirm payment') ?></button>
        </div>
    </div>
    <div id="payment-error" style="display:none">
        <h2><?= Yii::t('app', 'An error was encountered while processing the payment') ?></h2>
        <p id="payment-error-message" class="text-danger"></p>
        <p id="payment-error-code" class="text-muted"><?= Yii::t('app', 'Error code') ?>: </p>
        <button class="btn btn-primary PlanSuggestion__RetryPaymentButton"><?= Yii::t('app', 'Try again') ?></button>
    </div>
</div>