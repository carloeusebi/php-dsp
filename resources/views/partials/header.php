<?php

use app\core\utils\Request; ?>

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