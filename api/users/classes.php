<?php
class Nbhzvn_User {
    public $id;
    public $timestamp;
    public $username;
    public $email;
    public $type;
    private $passphrase;
    public $display_name;
    public $description;
    public $discord_id;
    public $verification_required;
    private $verification_code;
    private $login_token;
    public $ban_information;

    function __construct($id, $private = false) {
        $this->id = null; $data = new stdClass();
        switch (gettype($id)) {
            case "string":
            case "integer": {
                $result = db_query('SELECT * FROM `nbhzvn_users` WHERE ' . ((gettype($id) == "string") ? "username" : "id") . ' = ?', $id);
                while ($row = $result->fetch_object()) $data = $row;
                break;
            }
            default: {
                $data = $id;
                break;
            }
        }
        $this->id = $data->id;
        $this->timestamp = $data->timestamp;
        $this->username = $data->username;
        $this->type = $data->type;
        $this->passphrase = decrypt_string($data->passphrase);
        $this->display_name = $data->display_name;
        $this->description = $data->description;
        $this->verification_code = decrypt_string($data->verification_code);
        $this->login_token = decrypt_string($data->login_token);
        if (!$private) {
            $this->email = decrypt_string($data->email);
            $this->discord_id = $data->discord_id;
            $this->verification_required = $data->verification_required;
            $this->ban_information = json_decode($data->ban_information);
        }
        else {
            unset($this->email);
            unset($this->discord_id);
            unset($this->verification_required);
            unset($this->ban_information);
        }
    }

    function display_name() {
        return $this->display_name ? $this->display_name : $this->username;
    }

    function verify_passphrase($passphrase) {
        return password_verify($passphrase, $this->passphrase);
    }

    function verify_account($code) {
        return password_verify($code, $this->verification_code);
    }

    function verify_account_hash($hash) {
        return ($hash == $this->verification_code);
    }

    function change_passphrase($passphrase) {
        $hash = password_hash($passphrase, PASSWORD_BCRYPT);
        db_query('UPDATE `nbhzvn_users` SET `passphrase` = ? WHERE `id` = ?', encrypt_string($hash), $this->id);
        $this->passphrase = $hash;
    }

    function change_display_name($value) {
        db_query('UPDATE `nbhzvn_users` SET `display_name` = ? WHERE `id` = ?', $value, $this->id);
        $this->display_name = $value;
    }

    function change_email($value) {
        db_query('UPDATE `nbhzvn_users` SET `email` = ?, `verification_required` = 1 WHERE `id` = ?', encrypt_string($value), $this->id);
        $this->email = $value;
    }

    function change_description($value) {
        db_query('UPDATE `nbhzvn_users` SET `description` = ? WHERE `id` = ?',  $value, $this->id);
        $this->description = $value;
    }

    function update_verification_code($code) {
        $hash = password_hash($code, PASSWORD_BCRYPT);
        db_query('UPDATE `nbhzvn_users` SET `verification_code` = ? WHERE `id` = ?', encrypt_string($hash), $this->id);
        $this->verification_code = $hash;
        return $hash;
    }

    function update_login_token() {
        $login_token = random_string(64);
        $hash = password_hash($login_token, PASSWORD_BCRYPT);
        $token = encrypt_string($hash);
        db_query('UPDATE `nbhzvn_users` SET `login_token` = ? WHERE `id` = ?', $token, $this->id);
        $this->login_token = $hash;
        return $login_token;
    }

    function update_discord_id($id) {
        db_query('UPDATE `nbhzvn_users` SET `discord_id` = ? WHERE `id` = ?', $id, $this->id);
        $this->discord_id = $id;
        return SUCCESS;
    }

    function ban($reason) {
        $ban_info = new stdClass();
        $ban_info->timestamp = time();
        $ban_info->reason = $reason;
        db_query('UPDATE `nbhzvn_users` SET `ban_information` = ? WHERE `id` = ?', json_encode($ban_info), $this->id);
        $this->ban_information = $ban_info;
        return SUCCESS;
    }

    function unban() {
        db_query('UPDATE `nbhzvn_users` SET `ban_information` = NULL WHERE `id` = ?', $this->id);
        $this->ban_information = null;
        return SUCCESS;
    }

    function change_type($type) {
        if ($type < 1 || $type > 3) throw new Exception(DISALLOWED_TYPE);
        db_query('UPDATE `nbhzvn_users` SET `type` = ? WHERE `id` = ?', $type, $this->id);
        $this->type = $type;
    }

    function check_login_token($token) {
        return password_verify($token, $this->login_token);
    }

    function apply_cookie() {
        $login_token = $this->update_login_token();
        setcookie("nbhzvn_username", $this->username, time() + 2592000, "/", "." . $_SERVER["HTTP_HOST"]);
        setcookie("nbhzvn_login_token", $login_token, time() + 2592000, "/", "." . $_SERVER["HTTP_HOST"]);
    }

    function first_verification() {
        return !$this->verification_code;
    }

    function send_verification_email() {
        global $http;
        global $host;
        $verification_code = sprintf("%08d", rand(0, 99999999));
        $hash = $this->update_verification_code($verification_code);
        if (!send_mail(
            $this->email,
            'Xác nhận tài khoản tại Nobihaza Vietnam Community Collection',
            '
                <p>Xin chào <b>' . $this->username . '</b>,</p>
                <p>Email của bạn đã được sử dụng để xác minh cho tài khoản trên ở trang web <b>Nobihaza Vietnam Community Collection</b>.</p>
                <p>Nếu bạn không thực hiện hành động này thì hãy vui lòng bỏ qua email này. Còn nếu bạn là người gửi yêu cầu xác minh tài khoản tới email này thì hãy nhập mã xác minh sau vào ô trong trang web đó:</p>
                <h2>' . $verification_code . '</h2>
                <p>Hoặc bạn cũng có thể <a href="' . $http . '://' . $host . '/verify?username=' . $this->username . '&code=' . urlencode($hash) . '">nhấn vào đây</a> để xác nhận, hoặc sao chép liên kết sau vào thanh địa chỉ:</p>
                <p>' . $http . '://' . $host . '/verify?username=' . $this->username . '&code=' . urlencode($hash) . '</p>
                <p>Cảm ơn bạn đã quan tâm tới Nobihaza Vietnam Community Collection của bọn mình, chúc bạn chơi game vui vẻ!</p>
            '
        )) throw new Exception(SEND_MAIL_FAILED);
        return SUCCESS;
    }

    function send_forgot_password_email() {
        global $http;
        global $host;
        $verification_code = sprintf("%08d", rand(0, 99999999));
        $hash = $this->update_verification_code($verification_code);
        if (!send_mail(
            $this->email,
            'Yêu cầu đặt lại mật khẩu tại Nobihaza Vietnam Community Collection',
            '
                <p>Xin chào <b>' . $this->username . '</b>,</p>
                <p>Bạn đã yêu cầu đặt lại mật khẩu cho tài khoản này ở trang web <b>Nobihaza Vietnam Community Collection</b>.</p>
                <p>Nếu bạn không thực hiện hành động này thì hãy vui lòng bỏ qua email này. Còn nếu bạn là người gửi yêu cầu đặt lại mật khẩu thì hãy <a href="' . $http . '://' . $host . '/forgot_password?username=' . $this->username . '&code=' . urlencode($hash) . '">nhấn vào đây</a> để xác nhận, hoặc sao chép liên kết sau vào thanh địa chỉ:</p>
                <p>' . $http . '://' . $host . '/forgot_password?username=' . $this->username . '&code=' . urlencode($hash) . '</p>
                <p>Cảm ơn bạn đã quan tâm tới Nobihaza Vietnam Community Collection của bọn mình, chúc bạn chơi game vui vẻ!</p>
            '
        )) throw new Exception(SEND_MAIL_FAILED);
        return SUCCESS;
    }

    function check_timeout($prop) {
        $result = db_query('SELECT `timestamp` FROM `nbhzvn_timeouts` WHERE `user_id` = ? AND `property` = ?', $this->id, $prop);
        while ($row = $result->fetch_object()) {
            return (time() < $row->timestamp);
        }
        return false;
    }

    function update_timeout($prop, $time) {
        $result = db_query('SELECT * FROM `nbhzvn_timeouts` WHERE `user_id` = ? AND `property` = ?', $this->id, $prop);
        if ($result->num_rows > 0) db_query('UPDATE `nbhzvn_timeouts` SET `timestamp` = ? WHERE `user_id` = ? AND `property` = ?', $time, $this->id, $prop);
        else db_query('INSERT INTO `nbhzvn_timeouts` (`user_id`, `property`, `timestamp`) VALUES (?, ?, ?)', $this->id, $prop, $time);
        return SUCCESS;
    }

    function followed_games() {
        $games = [];
        $result = db_query('SELECT `game_id` FROM `nbhzvn_gamefollows` WHERE `author` = ?', $this->id);
        while ($row = $result->fetch_object()) {
            $game = new Nbhzvn_Game($row->game_id);
            if ($game->approved) array_push($games, $game);
        }
        return $games;
    }

    function comments() {
        $comments = [];
        $result = db_query('SELECT `id` FROM `nbhzvn_comments` WHERE `author` = ? ORDER BY TIMESTAMP DESC', $this->id);
        while ($row = $result->fetch_object()) {
            array_push($comments, new Nbhzvn_Comment($row->id));
        }
        return $comments;
    }

    function uploaded_games() {
        $games = [];
        $result = db_query('SELECT * FROM `nbhzvn_games` WHERE `uploader` = ? AND `approved` = 1 ORDER BY `timestamp` DESC', $this->id);
        while ($row = $result->fetch_object()) {
            array_push($games, new Nbhzvn_Game($row));
        }
        return $games;
    }

    function popular_games() {
        $games = [];
        $result = db_query('SELECT * FROM `nbhzvn_games` WHERE `uploader` = ? AND `approved` = 1 ORDER BY `downloads` DESC', $this->id);
        while ($row = $result->fetch_object()) {
            array_push($games, new Nbhzvn_Game($row));
        }
        return $games;
    }

    function notifications() {
        $notifications = [];
        $result = db_query('SELECT * FROM `nbhzvn_notifications` WHERE `user_id` = ? ORDER BY `timestamp` DESC', $this->id);
        while ($row = $result->fetch_object()) {
            array_push($notifications, new Nbhzvn_Notification($row));
        }
        return $notifications;
    }

    function unread_notifications() {
        $notifications = [];
        $result = db_query('SELECT * FROM `nbhzvn_notifications` WHERE `user_id` = ? AND `is_unread` = 1', $this->id);
        while ($row = $result->fetch_object()) {
            array_push($notifications, new Nbhzvn_Notification($row));
        }
        return $notifications;
    }

    function send_notification($link, $content) {
        db_query('INSERT INTO `nbhzvn_notifications` (`timestamp`, `user_id`, `link`, `content`, `is_unread`) VALUES (?, ?, ?, ?, ?)', time(), $this->id, $link, $content, 1);
        return SUCCESS;
    }

    function send_comment_notification(Nbhzvn_Game $game, $commenter_id, $comment_id, $type = COMMENT_DEFAULT, $reply_id = null) {
        $link = "/games/" . $game->id; $commenter = new Nbhzvn_User($commenter_id); $content = "";
        switch ($type) {
            case COMMENT_DEFAULT: {
                $link .= "?highlighted_comment=" . $comment_id . "#comment-" . $comment_id;
                $content = "**{user}** đã bình luận vào game **{game}** của bạn.";
                break;
            }
            case COMMENT_REPLY: {
                $link .= "?highlighted_comment=" . $comment_id . "&reply_comment=" . $reply_id . "#comment-" . $reply_id;
                $content = "**{user}** đã trả lời bình luận của bạn ở game **{game}**.";
                break;
            }
            case COMMENT_MENTION: {
                if ($reply_id) $link .= "?highlighted_comment=" . $comment_id . "&reply_comment=" . $reply_id . "#comment-" . $reply_id;
                else $link .= "?highlighted_comment=" . $comment_id . "#comment-" . $comment_id;
                $content = "**{user}** đã nhắc đến bạn trong một bình luận ở game **{game}**.";
                break;
            }
        }
        $this->send_notification($link, str_replace("{user}", $commenter->display_name(), str_replace("{game}", $game->name, $content)));
    }

    function badge_html($author = false, $large = false) {
        if ($author) return '<span class="badge author">Chủ Thớt</span>';
        $str = array(
            NORMAL_USER_TYPE => '',
            UPLOADER_TYPE => '<span class="badge uploader">Uploader</span>',
            ADMINISTRATOR_TYPE => '<span class="badge admin">QTV</span>'
        )[$this->type];
        return $large ? str_replace("badge", "badge large", $str) : $str;
    }
}

class Nbhzvn_Notification {
    public $id;
    public $timestamp;
    public $user_id;
    public $link;
    public $content;
    public $is_unread;

    function __construct($id) {
        if (is_object($id)) $data = $id;
        else {
            $result = db_query('SELECT * FROM `nbhzvn_notifications` WHERE id = ?', $id);
            while ($row = $result->fetch_object()) $data = $row;
        }
        $this->id = $data->id;
        $this->timestamp = $data->timestamp;
        $this->user_id = $data->user_id;
        $this->link = $data->link;
        $this->content = $data->content;
        $this->is_unread = ($data->is_unread == 1);
    }

    function delete() {
        db_query('DELETE FROM `nbhzvn_notifications` WHERE `id` = ?', $this->id);
        $this->id = null;
        return SUCCESS;
    }

    function to_html() {
        $parsedown = new Parsedown();
        $parsedown->setSafeMode(true);
        $parsedown->setMarkupEscaped(true);
        return '
        <div id="notification-' . $this->id . '" class="comment_container"><a href="' . $this->link . '">
            <div class="anime__review__item">
                <div class="anime__review__item__text' . ($this->is_unread ? " notification_unread" : "") . '">
                    <h6><span style="font-size: 10pt">' . comment_time($this->timestamp) . '</span></h6>
                    <p>' . $parsedown->text($this->content) . '</p>
                    <p class="comment_options">
                        <a href="javascript:void(0)" onclick="deleteNotification(' . $this->id . ')">Xoá thông báo này</a>
                    </p>
                </div>
            </div>
        </a></div>
        ';
    }
}
?>