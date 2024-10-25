<?php
db_query("CREATE TABLE IF NOT EXISTS `nbhzvn_users` (`id` INT NOT NULL AUTO_INCREMENT , `username` TEXT NOT NULL , `email` TEXT NOT NULL COMMENT 'Must be encrypted with the encrypt_string function.' , `passphrase` TEXT NOT NULL COMMENT 'Hash the password first, then use the encrypt_string function.' , `display_name` TEXT NOT NULL , `description` TEXT NOT NULL , `discord_id` TEXT NOT NULL COMMENT 'Must be encrypted using encrypt_string', `verification_required` BOOLEAN NOT NULL , `verification_code` TEXT NOT NULL COMMENT 'Hash the code first, then use encrypt_string to encrypt' , `login_token` TEXT NULL COMMENT 'Must be encrypted using encrypt_string' , PRIMARY KEY (`id`)) ENGINE = InnoDB");
db_query("CREATE TABLE IF NOT EXISTS `nbhzvn_games` (`id` INT NOT NULL AUTO_INCREMENT , `name` TEXT NOT NULL COMMENT '512 characters or fewer.' , `image` TEXT NOT NULL , `screenshots` TEXT NOT NULL COMMENT 'Must be JSON formatted.' , `description` LONGTEXT NOT NULL , `engine` INT(2) NOT NULL COMMENT '1 - RPG Maker 2000/2003; 2 - RPG Maker XP/VX/VX Ace; 3 - RPG Maker MV; 4 - Other Game Engines' , `tags` TEXT NOT NULL , `release_date` TIMESTAMP NOT NULL , `author` TEXT NOT NULL COMMENT 'The original author/game developer' , `language` INT(2) NOT NULL COMMENT '1 - Vietnamese; 2 - English; 3 - Japanese; 4 - Chinese; 5 - Other; 6 - Multiple' , `translator` TEXT NULL COMMENT 'The game translator' , `uploader` INT NOT NULL COMMENT 'The uploader ID (account ID of the uploader)' , `status` INT(2) NOT NULL COMMENT '1 - Developing; 2 - Finished; 3 - Abandoned' , `views` BIGINT NOT NULL , `views_today` BIGINT NOT NULL COMMENT 'Auto-reset at the first request if updated_date < current date.' , `updated_date` DATE NOT NULL COMMENT 'Determine the current date to update views_today.' , `downloads` BIGINT NOT NULL , `supported_os` TEXT NOT NULL COMMENT 'Seperated with \";\" (semicolon)' , `is_featured` BOOLEAN NOT NULL , PRIMARY KEY (`id`), FOREIGN KEY (uploader) REFERENCES nbhzvn_users(id)) ENGINE = InnoDB");
db_query("CREATE TABLE IF NOT EXISTS `nbhzvn_comments` (`id` INT NOT NULL AUTO_INCREMENT , `author` INT NOT NULL , `timestamp` TIMESTAMP NOT NULL , `content` TEXT NOT NULL , `replied_to` INT NOT NULL , PRIMARY KEY (`id`) , FOREIGN KEY (author) REFERENCES nbhzvn_users(id)) ENGINE = InnoDB");
db_query("CREATE TABLE IF NOT EXISTS `nbhzvn_gameratings` (`id` INT NOT NULL AUTO_INCREMENT , `author` INT NOT NULL , `timestamp` TIMESTAMP NOT NULL , `game_id` INT NOT NULL , `rating` INT(1) NOT NULL , PRIMARY KEY (`id`) , FOREIGN KEY (author) REFERENCES nbhzvn_users(id) , FOREIGN KEY (game_id) REFERENCES nbhzvn_games(id)) ENGINE = InnoDB");
db_query("CREATE TABLE IF NOT EXISTS `nbhzvn_gamefollows` (`id` INT NOT NULL , `author` INT NOT NULL , `game_id` INT NOT NULL , FOREIGN KEY (author) REFERENCES nbhzvn_users(id) , FOREIGN KEY (game_id) REFERENCES nbhzvn_games(id)) ENGINE = InnoDB")
?>