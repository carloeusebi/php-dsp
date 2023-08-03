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