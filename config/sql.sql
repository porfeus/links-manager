--
-- Структура таблицы `sites`
--

CREATE TABLE IF NOT EXISTS `sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  `link` text NOT NULL,
  `note` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=422 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` text NOT NULL,
  `password` text NOT NULL,
  `email` text NOT NULL,
  `language` text NOT NULL,
  `activated_time` int(11) NOT NULL,
  `activated_add_time` int(11) NOT NULL,
  `users_limit` int(11) NOT NULL,
  `users_online` text NOT NULL,
  `email_send_time` int(11) NOT NULL,
  `ip_old` text NOT NULL,
  `ip_new` text NOT NULL,
  `last_enter_time` int(11) NOT NULL,
  `last_update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=82 DEFAULT CHARSET=utf8;
