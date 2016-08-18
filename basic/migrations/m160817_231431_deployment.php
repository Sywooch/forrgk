<?php

use yii\db\Migration;


/**
 * Class m160817_231431_deployment
 *
 * Миграция для развертки проекта
 */
class m160817_231431_deployment extends Migration
{
    public function up()
    {
        // init
        $this->execute('SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO"');
        $this->execute('SET time_zone = "+00:00"');


        //struct
        $this->execute('CREATE TABLE IF NOT EXISTS `alerts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `from_user` int(11) NOT NULL DEFAULT \'0\',
  `to_user` int(11) NOT NULL DEFAULT \'0\',
  `title` varchar(128) NOT NULL,
  `content` text NOT NULL,
  `alert_type` varchar(1024) NOT NULL,
  `post_date` datetime NOT NULL,
  `var_list` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`,`from_user`,`to_user`,`alert_type`(255),`post_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');


        $this->execute('CREATE TABLE IF NOT EXISTS `alerts_events` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `from_user` int(10) unsigned NOT NULL DEFAULT \'0\',
  `to_user` int(10) unsigned NOT NULL DEFAULT \'0\',
  `title` varchar(128) NOT NULL,
  `content` text NOT NULL,
  `alert_type` varchar(16) NOT NULL,
  `position` smallint(5) unsigned NOT NULL DEFAULT \'0\',
  PRIMARY KEY (`id`),
  KEY `name` (`name`,`from_user`,`to_user`,`alert_type`),
  KEY `position` (`position`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');



        $this->execute('CREATE TABLE IF NOT EXISTS `alerts_events_assignment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alert_event_id` int(10) unsigned NOT NULL,
  `event_name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_event_assignment` (`alert_event_id`,`event_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');

        $this->execute('CREATE TABLE IF NOT EXISTS `alerts_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alert_id` int(10) unsigned NOT NULL,
  `type_name` varchar(32) NOT NULL,
  `for_event` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alert_and_type` (`alert_id`,`type_name`,`for_event`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');

        $this->execute('CREATE TABLE IF NOT EXISTS `alerts_views` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alert_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `view_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alert_unique` (`alert_id`,`user_id`),
  KEY `view_date` (`view_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');

        $this->execute('CREATE TABLE IF NOT EXISTS `articles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `content` text NOT NULL,
  `post_date` datetime NOT NULL,
  `author` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `author` (`author`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');

        $this->execute('CREATE TABLE IF NOT EXISTS `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');

        $this->execute('CREATE TABLE IF NOT EXISTS `auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');

        $this->execute('CREATE TABLE IF NOT EXISTS `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');

        $this->execute('CREATE TABLE IF NOT EXISTS `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');

        $this->execute('CREATE TABLE IF NOT EXISTS `plugins` (
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

        $this->execute('CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(60) NOT NULL,
  `salt` varchar(20) NOT NULL,
  `email` varchar(64) NOT NULL,
  `last_access` datetime NOT NULL,
  `last_enter` datetime NOT NULL,
  `halted` tinyint(1) NOT NULL DEFAULT \'0\',
  `token` varchar(48) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `token` (`token`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');


        /// set data
        $this->execute('INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
(\'admin\', \'1\', 1471383852)');

        $this->execute('INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES
(\'admin\', 1, NULL, NULL, NULL, 1471344668, 1471344668),
(\'adminAccess\', 2, \'Доступ к админпанели\', NULL, NULL, 1471344667, 1471344667),
(\'user\', 1, NULL, NULL, NULL, 1471344668, 1471344668);');

        $this->execute('INSERT INTO `auth_item_child` (`parent`, `child`) VALUES
(\'admin\', \'adminAccess\');');


        $this->execute('INSERT INTO `users` (`id`, `username`, `password`, `salt`, `email`, `last_access`, `last_enter`, `halted`, `token`) VALUES
(1, \'the_admin\', \'$2y$13$VSiR1sgnID9fXvYzyDHAyeGAVd06pfHBBZyaWuhA78Cg1xfYVv.Nm\', \'LTObZN3fzZzte8AC\', \'admin@example.com\', \'2016-08-16 12:34:28\', \'2016-08-16 12:34:28\', 0, \'\')');





        // testing data
        for ($i=1;$i<20;++$i) {
            $this->execute('INSERT INTO `users` (`username`, `password`, `salt`, `email`, `last_access`, `last_enter`, `halted`, `token`) VALUES
( \'testuser'.$i.'\', \'$2y$13$6mFm1Y7u3EIHxthUCZTNe.jjcJmibzsrkl4fLISVON/jkJMNaGYNe\', \'pS2JxIBbWGDxSsEn\', \'user@example.com\', \'2016-08-17 05:31:15\', \'2016-08-17 05:31:15\', 0, \'\')');

            $this->execute('INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
(\'user\', \''.($i+1).'\', 1471383852)');
        }



        /// test events
        $this->execute('INSERT INTO `alerts_types` (`alert_id`, `type_name`, `for_event`) VALUES
(1, \'browser\', 1)');


        $this->execute('INSERT INTO `alerts_events_assignment` ( `alert_event_id`, `event_name`) VALUES
( 1, \'login\')');

        $this->execute('INSERT INTO `alerts_events` (`name`, `from_user`, `to_user`, `title`, `content`, `alert_type`, `position`) VALUES
( \'Сработает при входе в систему\', 2, 0, \'Ещё один пользователь!\', \'К нам присоеденился ещё пользовтель {loggedUsername}, поприветствуем его!\', \'\', 0)');

        
    }

    public function down()
    {
        echo "m160817_231431_deployment cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
