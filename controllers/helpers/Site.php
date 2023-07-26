<?php

namespace app\controllers\helpers;

use app\controllers\SiteController;

class Site extends SiteController
{

  protected static function getPageTitle(string $page): string
  {
    return match ($page) {
      '/home' => 'Home',
      '/chi-sono' => 'Chi Sono',
      '/cosa-aspettarsi' => 'Cosa Aspettarsi dalla Terapia',
      '/di-cosa-mi-occupo' => 'Di cosa mi Occupo',
      '/contatti' => 'Contatti',
      default => 'Pagina non trovata'
    };
  }
}
