<?php
function create_admin_user() {
    $result = db_query('SELECT `id` FROM `nbhzvn_users` WHERE `type` = 3');
    if ($result->num_rows == 0) {
        $username = strtolower($_ENV["ADMIN_USERNAME"]);
        $password = encrypt_string(password_hash($_ENV["ADMIN_PASSWORD"], PASSWORD_DEFAULT));
        $email = encrypt_string($_ENV["ADMIN_EMAIL"]);
        db_query('INSERT INTO `nbhzvn_users` (`timestamp`, `username`, `email`, `passphrase`, `type`, `verification_required`, `discord_id`) VALUES (?, ?, ?, ?, ?, ?, ?)', time(), $username, $email, $password, 3, 0, null);
    }
}

db_query("CREATE TABLE IF NOT EXISTS `nbhzvn_users` (`id` INT NOT NULL AUTO_INCREMENT , `timestamp` BIGINT NOT NULL , `username` TEXT NOT NULL , `email` TEXT NOT NULL COMMENT 'Must be encrypted with the encrypt_string function.' , `passphrase` TEXT NOT NULL COMMENT 'Hash the password first, then use the encrypt_string function.' , `type` INT NULL COMMENT '1 = Normal user; 2 = Uploader; 3 = Administrator' , `display_name` TEXT NULL , `description` TEXT NULL , `discord_id` TEXT NULL, `verification_required` BOOLEAN NOT NULL , `verification_code` TEXT NULL COMMENT 'Hash the code first, then use encrypt_string to encrypt' , `login_token` TEXT NULL COMMENT 'Must be encrypted using encrypt_string' , `ban_information` TEXT NULL COMMENT 'Must be JSON encoded' , PRIMARY KEY (`id`)) ENGINE = InnoDB");
db_query("CREATE TABLE IF NOT EXISTS `nbhzvn_games` (`id` INT NOT NULL AUTO_INCREMENT , `timestamp` BIGINT NOT NULL , `name` TEXT NOT NULL COMMENT '512 characters or fewer.' , `links` LONGTEXT NOT NULL COMMENT 'Must be JSON encoded.' , `image` TEXT NOT NULL , `screenshots` TEXT NOT NULL COMMENT 'Must be JSON encoded.' , `description` LONGTEXT NOT NULL , `engine` INT(2) NOT NULL COMMENT '1 - RPG Maker 2000/2003; 2 - RPG Maker XP/VX/VX Ace; 3 - RPG Maker MV; 4 - Other Game Engines' , `tags` TEXT NOT NULL , `release_year` INT NOT NULL , `author` TEXT NOT NULL COMMENT 'The original author/game developer' , `language` INT(2) NOT NULL COMMENT '1 - Vietnamese; 2 - English; 3 - Japanese; 4 - Chinese; 5 - Other; 6 - Multiple' , `translator` TEXT NULL COMMENT 'The game translator' , `uploader` INT NOT NULL COMMENT 'The uploader ID (account ID of the uploader)' , `status` INT(2) NOT NULL COMMENT '1 - Developing; 2 - Finished; 3 - Abandoned' , `views` BIGINT NOT NULL , `views_today` BIGINT NOT NULL COMMENT 'Auto-reset at the first request if updated_date < current date.' , `downloads_today` BIGINT NOT NULL COMMENT 'Auto-reset at the first request if updated_date < current date.' , `updated_date` DATE NOT NULL COMMENT 'Determine the current date to update views_today.' , `file_updated_time` BIGINT NULL , `downloads` BIGINT NOT NULL , `supported_os` TEXT NOT NULL COMMENT 'Seperated with \",\" (colon)' , `is_featured` BOOLEAN NOT NULL , `approved` BOOLEAN NOT NULL , PRIMARY KEY (`id`), FOREIGN KEY (uploader) REFERENCES nbhzvn_users(id)) ENGINE = InnoDB");
db_query("CREATE TABLE IF NOT EXISTS `nbhzvn_comments` (`id` INT NOT NULL AUTO_INCREMENT , `author` INT NOT NULL , `timestamp` BIGINT NOT NULL , `game_id` INT NOT NULL , `content` TEXT NOT NULL , `replied_to` INT NULL , `edited` BOOLEAN NULL , PRIMARY KEY (`id`) , FOREIGN KEY (author) REFERENCES nbhzvn_users(id) , FOREIGN KEY (game_id) REFERENCES nbhzvn_games(id)) ENGINE = InnoDB");
db_query("CREATE TABLE IF NOT EXISTS `nbhzvn_gameratings` (`id` INT NOT NULL AUTO_INCREMENT , `author` INT NOT NULL , `timestamp` BIGINT NOT NULL , `game_id` INT NOT NULL , `rating` INT(1) NOT NULL , reason TEXT NULL , PRIMARY KEY (`id`) , FOREIGN KEY (author) REFERENCES nbhzvn_users(id) , FOREIGN KEY (game_id) REFERENCES nbhzvn_games(id)) ENGINE = InnoDB");
db_query("CREATE TABLE IF NOT EXISTS `nbhzvn_gamefollows` (`id` INT NOT NULL AUTO_INCREMENT , `author` INT NOT NULL , `game_id` INT NOT NULL , PRIMARY KEY (`id`) , FOREIGN KEY (author) REFERENCES nbhzvn_users(id) , FOREIGN KEY (game_id) REFERENCES nbhzvn_games(id)) ENGINE = InnoDB");
db_query("CREATE TABLE IF NOT EXISTS `nbhzvn_timeouts` (`id` INT NOT NULL AUTO_INCREMENT , `user_id` INT NOT NULL , `property` TEXT NOT NULL , `timestamp` BIGINT NOT NULL , PRIMARY KEY (`id`) , FOREIGN KEY (user_id) REFERENCES nbhzvn_users(id)) ENGINE = InnoDB");
db_query("CREATE TABLE IF NOT EXISTS `nbhzvn_notifications` (`id` INT NOT NULL AUTO_INCREMENT , `timestamp` BIGINT NOT NULL , `user_id` INT NOT NULL , `link` TEXT NULL , `content` TEXT NOT NULL , `is_unread` BOOLEAN NOT NULL , PRIMARY KEY (`id`) , FOREIGN KEY (user_id) REFERENCES nbhzvn_users(id)) ENGINE = InnoDB");
create_admin_user();
?>