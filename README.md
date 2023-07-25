# Dellasanta Psicologo

![Tests](https://github.com/carloeusebi/php-vue-dsp/actions/workflows/deploy.yml/badge.svg)<br>
Website for Federico Dellasanta Psicologo with front cover site and Admin Panel to manage patients and psychological evaluation questionnaires.

> Built with PHP 8.1, Vue.js and Tailwind.css.

## Demo

<!-- url is temporary -->

Front cover Site: https://carloeusebiwebdeveloper.it<br>
Admin Panel: https://carloeusebiwebdeveloper.it/admin

```
Email: admin
Password: admin
```

## Installation

Make sure you have environment setup properly. You will need MySQL, PHP8.1, Node.js and composer.

### Install PHP Website + API

1. Download the project (or clone using GIT) and save it in your htdocs folder
2. Copy `.env.example` into `.env` and configure database credentials
3. In `.env` configure your email credentials if you want to be able to send emails
4. Navigate to the project's root directory using terminal
5. Run `composer install`
6. Run migrations `php migrations.php`

### Install Vue.js Admin Panel

1. Navigate to `vue` folder
2. Run `npm install`
3. Copy `vue/.env.example` into `vue/.env`
4. Make sure `VITE_BASE_URL` key in `vue/.env` is set to your VITE port (Default: http://localhost:3000)
5. Make sure `VITE_API_URL` key in `backend/.env` is set to your PHP Api Host
6. Run `npm run dev`
7. Open Vue.js Admin Panel in browser and login with
   ```
   admin
   admin
   ```
