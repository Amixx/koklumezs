<div id="install-prompt" class="a2hs"> <?= \Yii::t('app', 'Add to home screen') ?></div>
<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; koklumezs.lv <?= date('Y') ?></p>
    </div>
</footer>
<script src="https://cdn.plyr.io/3.6.8/plyr.polyfilled.js"></script>
<script src="https://pay.fondy.eu/static_common/v1/checkout/ipsp.js"></script>
<script>
    window.userLanguage = '<?= Yii::$app->language ?>'
</script>
<script src="https://js.stripe.com/v3/"></script>
