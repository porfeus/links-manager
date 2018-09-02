<?php

return [
  //admin
  'admin_login' => 'admin', //Логин админа
  'admin_password' => '12345', //Пароль админа
  'admin_email' => 'gugavaeezd@mail.ru', //E-mail админа

  //bases
  'show_base_1' => true, //Показывать базу 1 (true - да, false - нет)
  'show_base_2' => true, //Показывать базу 2 (true - да, false - нет)
  'base_1_name' => 'База 1', //Название базы 1
  'base_2_name' => 'База 2', //Название базы 2

  //users
  'default_language' => 'ru', //Язык пользователей по-умолчанию

  //database
  'DB_DRIVER'   => 'mysql', //Драйвер БД
  'DB_HOSTNAME' => 'localhost', //Сервер БД
  'DB_USERNAME' => 'user12345', //Логин пользователя БД
  'DB_PASSWORD' => 'wFNf768', //Пароль пользователя БД
  'DB_DATABASE' => 'sites_db', //База данных

  //pages
  'users_show_num' => 15, //Число записей на странице пользователей
  'sites_show_num' => 250, //Число записей на странице сайтов

  //notification
  'notification_days' => [1,2,3,4,5], //За сколько дней уведомлять о завершении активации
  'notification_limit' => 8, //Лимит отправки писем за один раз
];
