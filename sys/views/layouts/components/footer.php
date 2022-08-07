<div id="install-prompt" class="a2hs"> <?= \Yii::t('app', 'Add to home screen') ?></div>
<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; koklumezs.lv <?= date('Y') ?></p>
    </div>
</footer>
<script src="https://cdn.plyr.io/3.6.8/plyr.polyfilled.js"></script>
<script src="https://pay.fondy.eu/static_common/v1/checkout/ipsp.js"></script>
<script>
    window.userLanguage = '<?= Yii::$app->language ?>';
    window.stripeConfig = {
        pk: '<?= Yii::$app->params['stripe']['pk'] ?>'
    }
</script>
<script src="https://js.stripe.com/v3/"></script>
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14"></script>
<script src="https://unpkg.com/vue-select@latest"></script>
<link rel="stylesheet" href="https://unpkg.com/vue-select@latest/dist/vue-select.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.27.2/axios.min.js" integrity="sha512-odNmoc1XJy5x1TMVMdC7EMs3IVdItLPlCeL5vSUPN2llYKMJ2eByTTAIiiuqLg+GdNr9hF6z81p27DArRFKT7A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>