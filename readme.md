# Laravel WebSockets 🛰 Chat Example

This is a Chat system example application built with the [Laravel WebSockets](https://github.com/beyondcode/laravel-websockets) package, [VueJs](https://vuejs.org/) and [Laravel-Echo](https://laravel.com/docs/5.7/broadcasting#installing-laravel-echo).

With some modification from original source : https://github.com/qirolab/Laravel-WebSockets-Chat-Example.

## Usage

1. Clone this repository
`git clone https://github.com/bunmin/laravel.vue-chat.git`
2. `composer install`
4. `cp .env.example .env` and configure your database in .env file.
5. Run migration to create tables in database.
`php artisan migrate`
6. Final step run websockets server.
`php artisan websockets:serve`,

Now test it in your browser.



