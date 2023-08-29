<?php

use app\core\utils\Request; ?>

<!DOCTYPE html>
<html lang="it">

<?php include_once __DIR__ . '/../partials/head.php' ?>

<!-- Google Tag Manager (noscript) -->
<!-- <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PPT73ZG" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript> -->
<!-- End Google Tag Manager (noscript) -->

<?php include __DIR__ . '/../partials/header.php' ?>

<?php if ($page !== '/home' && $page !== '404') include __DIR__ . '/../partials/hero-secondary.php' ?>

<main>
    <?= $content ?>
</main>

<?php if ($page !== '404') : ?>

    <!-- # CONTATTAMI -->
    <?php include __DIR__ . '/../partials/contact-form.php' ?>

    <!-- ! FOOTER -->

    <?php include __DIR__ . '/../partials/footer.php' ?>

    <?php if ($status || $errors) : ?>
        <script defer>
            document.getElementById('response-anchor').scrollIntoView(false);
        </script>
    <?php endif ?>

    <!-- if ($page !== '404') :-->
<?php endif ?>

<!-- iubenda -->
<script type="text/javascript">
    var _iub = _iub || [];
    _iub.csConfiguration = {
        "consentOnContinuedBrowsing": false,
        "cookiePolicyId": 29156312,
        "countryDetection": true,
        "floatingPreferencesButtonDisplay": "bottom-right",
        "gdprAppliesGlobally": false,
        "invalidateConsentWithoutLog": true,
        "perPurposeConsent": true,
        "siteId": 2614419,
        "whitelabel": false,
        "lang": "it",
        "banner": {
            "acceptButtonCaptionColor": "#FFFFFF",
            "acceptButtonColor": "#0073CE",
            "acceptButtonDisplay": true,
            "backgroundColor": "#FFFFFF",
            "closeButtonRejects": true,
            "customizeButtonCaptionColor": "#4D4D4D",
            "customizeButtonColor": "#DADADA",
            "customizeButtonDisplay": true,
            "explicitWithdrawal": true,
            "fontSize": "16px",
            "listPurposes": true,
            "logo": null,
            "position": "float-bottom-center",
            "textColor": "#000000",
            "content": "Noi e terze parti selezionate utilizziamo cookie o tecnologie simili per finalità tecniche come specificato nella cookie policy",
            "customizeButtonCaption": "Ulteriori informazioni"
        }
    };
</script>
<script type="text/javascript" src="//cdn.iubenda.com/cs/iubenda_cs.js" charset="UTF-8"  async></script>