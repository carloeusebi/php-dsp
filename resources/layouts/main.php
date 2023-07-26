<?php

use app\core\utils\Request; ?>

<!DOCTYPE html>
<html lang="it">

<head>
    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-PPT73ZG');
    </script>
    <!-- End Google Tag Manager -->

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Carlo Eusebi">
    <meta name="description" content="Psicologo Cognitivo Comportamentale, mi occupo di consulenze psicologiche, sostegno e propongo percorsi individualizzati a Fano e online. Prenota la tua consulenza.">

    <title>Dellasanta Psicologo | <?= $pageTitle ?? 'Pagina non trovata' ?></title>

    <!-- roboto -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100italic,300,300italic,regular,italic,500,500italic,700,700italic,900,900italic" rel="stylesheet">
    <!-- abhaya -->
    <link href="https://fonts.googleapis.com/css?family=Abhaya+Libre:regular,500,600,700,800" rel="stylesheet" />

    <link rel="icon" href="img/Favicon.png" type="image/png">
    <!-- font awesome -->
    <link rel="stylesheet" href="/vendor/components/font-awesome/css/all.min.css">
    <!--  my library -->
    <link rel="stylesheet" href="css/mylibrary.min.css" type="text/css">
    <!-- animations -->
    <link rel="stylesheet" href="css/animations.min.css" type="text/css">
    <!-- styles.css -->
    <link rel="stylesheet" href="css/styles.min.css" type="text/css">
    <!-- scripts -->
    <script defer src="/js/scripts.js"></script>
</head>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PPT73ZG" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<header id="top-header">
    <div class="container d-flex justify-space-between align-center">
        <div id="hdr-logo">
            <a href="https://www.dellasantapsicologo.it" target="_blank">
                <img class="fluid-img" src="/img/Logo.webp" alt="logo del sito">
            </a>
        </div>
        <div id="hamburger-menu" class="m-20">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <nav id="top-navbar">
            <ul>
                <li><a href="/" <?= Request::urlIs('/') ? 'class="active"' : '' ?>>Home</a></li>
                <li><a href="/chi-sono" <?= Request::urlIs('/chi-sono') ? 'class="active"' : '' ?>>Chi Sono</a></li>
                <li><a href="/cosa-aspettarsi" <?= Request::urlIs('/cosa-aspettarsi') ? 'class="active"' : '' ?>>Cosa aspettarsi dalla Terapia</a></li>
                <li><a href="/di-cosa-mi-occupo" <?= Request::urlIs('/di-cosa-mi-occupo') ? 'class="active"' : '' ?>>Di cosa mi Occupo</a></li>
                <li><a href="/contatti" <?= Request::urlIs('/contatti') ? 'class="active"' : '' ?>>Contatti</a></li>
            </ul>
        </nav>
    </div>
</header>

<?php if ($page !== '/home' && $page !== '404') include __DIR__ . '/../partials/hero-secondary.php' ?>

<main>
    <?= $content ?>
</main>

<?php if ($page !== '404') : ?>

    <!-- # CONTATTAMI -->
    <section id="contattami">
        <div class="container">

            <div class="d-flex-lg flex-gap20 align-end mb-20">
                <div class="col-33 mb-20">
                    <h3>Hai bisogno di un consulto?</h3>
                    <h2>Contattami</h2>
                    <p class="mb-10">
                        Scrivi le tue informazioni e ti ricontatterò per fissare un primo consulto.
                    </p>
                    <hr class="mb-30">
                    <address>
                        <ul class="fa-ul">
                            <li><a href="https://www.google.it/maps/place/Via+Cavour,+27,+61032+Fano+PU/@43.8404319,13.0169682,17z/data=!3m1!4b1!4m6!3m5!1s0x132d1058c36acf17:0xc83016c05d18f682!8m2!3d43.8404281!4d13.0195431!16s%2Fg%2F11csgb9dcq?entry=ttu" target="_blank">
                                    <span class="fa-li"><i class="fa-solid fa-location-dot"></i></span>
                                    Via Cavour, 27 61032 Fano PU
                                </a></li>
                            <li><a href="mailto:dellasanta.federico@gmail.com ">
                                    <span class="fa-li"><i class="fa-solid fa-envelope"></i></span>
                                    dellasanta.federico@gmail.com
                                </a></li>
                            <li><a href="tel:375-7345384">
                                    <span class="fa-li"><i class="fa-solid fa-phone"></i></span>
                                    375 7345384
                                </a></li>
                            <li class="mb-0"><a href="https://wa.me/0393757345384" target="_blank">
                                    <span class="fa-li"><i class="fa-brands fa-whatsapp"></i></span>
                                    Inviami un messaggio
                                </a></li>
                        </ul>
                    </address>
                </div>

                <!-- # FORM -->
                <div class="col-66 mb-20">
                    <form id="contact-form" method="post">
                        <div class="d-flex-lg flex-gap20">
                            <div class="col-50 p-20-lg d-flex flex-column justify-space-between mb-20">

                                <!-- fname -->
                                <input class="contact-info" type="text" name="name" id="name" placeholder="Nome" autocomplete="name" value="<?= $form['name'] ?? '' ?>" required>

                                <!-- phone -->
                                <input class="contact-info" type="tel" minlength="7" name="phone" id="phone" placeholder="Numero di Telefono" autocomplete="tel" value="<?= $form['phone'] ?? '' ?>" required>

                                <!-- mail -->
                                <input class="contact-info" type="email" name="mail" id="mail" placeholder="Email" value="<?= $form['mail'] ?? '' ?>" required>

                            </div>
                            <div class="col-50 p-20-lg mb-20">

                                <!-- message -->
                                <textarea class="contact-info" name="message" id="message" rows="15" placeholder="Come posso aiutarti?" required><?= $form['message'] ?? '' ?></textarea>
                            </div>
                        </div>
                        <div class="d-flex-lg flex-gap20">
                            <div class="col-50 p-20-lg">
                                <div>
                                    <a href="https://www.iubenda.com/privacy-policy/29156312" class="iubenda-link mb-10" target="_blank">
                                        Normativa sull'utilizzo dei dati personali
                                    </a>
                                </div>
                                <input class="c-pointer" type="checkbox" name="norm-cb" id="norm-cb" required>
                                <label for="norm-cb" class="c-pointer"> Ho letto e accetto la normativa sui dati personali <sup>*</sup></label>
                            </div>
                            <div class="col-50 p-20-lg">
                                <input type="checkbox" id="miele-cb" name="miele-cb">
                                <button class="btn unclickable mt-20" name="submit" id="formButton" value="Invia il messaggio">
                                    <i class="fa-solid fa-circle-notch fa-spin fa-xl"></i>
                                    Invia il messaggio
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if ($status === 'success') : ?>
                <div class="response success mt-20">Email inviata correttamente
                    <i class="fa-solid fa-xmark fa-xl"></i>
                </div>
            <?php elseif ($errors) : ?>
                <div class="response bad mt-20"><?= $errors ?>
                    <i class="fa-solid fa-xmark fa-xl"></i>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <!-- ! FOOTER -->

    <footer>

        <!-- # CONTATTI -->
        <div id="response-anchor"></div>


        <section id="contatti">
            <div class="container d-flex-md">
                <div class="col-50 tablet">
                    <img src="img/Logo-768x191.webp" alt="">
                </div>
                <div class="col-50 d-flex-lg">
                    <div class="col-50 mb-50">
                        <h4 class="text-align-start">Link Utili</h4>
                        <ul class="fa-ul underline-on-hover">
                            <li><span class="fa-li"><i class="fa-solid fa-caret-right"></i></span><a href="/">Home</a></li>
                            <li><span class="fa-li"><i class="fa-solid fa-caret-right"></i></span><a href="/chi-sono">Chi sono</a></li>
                            <li><span class="fa-li"><i class="fa-solid fa-caret-right"></i></span><a href="/cosa-aspettarsi">Cosa aspettarsi dalla Terapia</a></li>
                            <li><span class="fa-li"><i class="fa-solid fa-caret-right"></i></span><a href="/di-cosa-mi-occupo">Di cosa mi occupo</a></li>
                            <li><span class="fa-li"><i class="fa-solid fa-caret-right"></i></span><a href="/contatti">Contatti</a></li>
                        </ul>
                    </div>
                    <div class="col50">
                        <h4 class="text-align-start">Contatti</h4>
                        <address>
                            <ul class="fa-ul">
                                <li><a href="https://www.google.it/maps/place/Via+Cavour,+27,+61032+Fano+PU/@43.8404319,13.0169682,17z/data=!3m1!4b1!4m6!3m5!1s0x132d1058c36acf17:0xc83016c05d18f682!8m2!3d43.8404281!4d13.0195431!16s%2Fg%2F11csgb9dcq?entry=ttu" target="_blank">
                                        <span class="fa-li"><i class="fa-solid fa-location-dot"></i></span>
                                        Via Cavour, 27 61032 Fano PU
                                    </a></li>
                                <li><a href="mailto:dellasanta.federico@gmail.com ">
                                        <span class="fa-li"><i class="fa-solid fa-envelope"></i></span>
                                        dellasanta.federico@gmail.com
                                    </a></li>
                                <li><a href="tel:375-7345384">
                                        <span class="fa-li"><i class="fa-solid fa-phone"></i></span>
                                        375 7345384
                                    </a></li>
                                <li> <span class="fa-li"><i class="fa-regular fa-clock"></i></span>
                                    Dalle 9:00 alle 19:00 <br>
                                    Lunedì - Venerdi
                                </li>
                            </ul>
                        </address>
                    </div>
                </div>
            </div>
        </section>

        <div class="ftr-bottom">
            <div class="container">
                <div class="copyright">Federico Dellasanta P.I. 02766970418 | © 2022 tutti i diritti riservati</div>
                <div class="copyright">Realizzato da <a href="https://carloeusebiwebdeveloper.it" target="_blank">carloeusebiwebdeveloper.it</a></div>
            </div>
        </div>
    </footer>

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