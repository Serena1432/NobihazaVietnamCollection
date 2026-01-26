<?php

class Nbhzvn_Game {
    public $id;
    public $timestamp;
    public $name;
    public $links;
    public $has_beta;
    public $beta_links;
    public $beta_users;
    public $image;
    public $screenshots;
    public $description;
    public $engine;
    public $tags;
    public $release_year;
    public $author;
    public $language;
    public $translator;
    public $uploader;
    public $status;
    public $views;
    public $views_today;
    public $downloads_today;
    public $updated_date;
    public $file_updated_time;
    public $downloads;
    public $supported_os;
    public $is_featured;
    public $approved;

    function __construct($id, $editMode = false, $beta = false) {
        global $user;
        if (is_object($id)) $data = $id;
        else {
            $result = db_query('SELECT * FROM `nbhzvn_games` WHERE id = ?', $id);
            while ($row = $result->fetch_object()) $data = $row;
        }
        $this->id = $data->id;
        $this->timestamp = $data->timestamp;
        $this->name = $data->name;
        $this->links = json_decode($data->links);
        $this->image = $data->image;
        $this->screenshots = json_decode($data->screenshots);
        $this->description = $data->description;
        $this->engine = $data->engine;
        $this->tags = $data->tags;
        $this->release_year = $data->release_year;
        $this->author = $data->author;
        $this->language = $data->language;
        $this->translator = $data->translator;
        $this->uploader = $data->uploader;
        $this->status = $data->status;
        $this->views = $data->views;
        $this->views_today = $data->views_today;
        $this->downloads_today = $data->downloads_today;
        $this->updated_date = $data->updated_date;
        $this->file_updated_time = $data->file_updated_time;
        $this->downloads = $data->downloads;
        $this->supported_os = $data->supported_os;
        $this->is_featured = $data->is_featured;
        $this->approved = ($data->approved == 1);
        unset($this->has_beta);
        unset($this->beta_links);
        unset($this->beta_users);
        if ($user != null && $data->beta_users != null && $data->beta_links != null && (in_array($user->id, json_decode($data->beta_users)) || $user->id == $this->uploader || $user->type > 2 || $beta == true)) {
            $this->beta_links = json_decode($data->beta_links);
        }
        if (json_decode($data->beta_links) != null && count(json_decode($data->beta_links)) > 0) $this->has_beta = true;
        if ($editMode && $user != null && $user->id == $this->uploader) {
            $this->beta_links = json_decode($data->beta_links);
            $this->beta_users = array_map(function($a) {
                $obj = new stdClass();
                $user = new Nbhzvn_User($a);
                $obj->id = $user->id;
                $obj->displayName = $user->id ? $user->display_name() : "Thành viên không xác định";
                return $obj;
            }, json_decode($data->beta_users));
        }
    }

    function add_views() {
        $data = new Nbhzvn_WebData();
        if ($this->approved && $data->views_timeout($this) < time()) {
            $this->views = intval($this->views) + 1;
            $this->views_today = intval($this->views_today) + 1;
            $data->update_views_timeout($this);
            db_query('UPDATE `nbhzvn_games` SET `views` = ?, `views_today` = ? WHERE `id` = ?', $this->views, $this->views_today, $this->id);
        }
        return SUCCESS;
    }

    function add_downloads_count() {
        $data = new Nbhzvn_WebData();
        if ($this->approved && $data->downloads_timeout($this) < time()) {
            $this->downloads = intval($this->downloads) + 1;
            $this->downloads_today = intval($this->downloads_today) + 1;
            $data->update_downloads_timeout($this);
            db_query('UPDATE `nbhzvn_games` SET `downloads` = ?, `downloads_today` = ? WHERE `id` = ?', $this->downloads, $this->downloads_today, $this->id);
        }
        return SUCCESS;
    }

    function approve() {
        db_query('UPDATE `nbhzvn_games` SET `approved` = 1 WHERE `id` = ?', $this->id);
        $this->approved = true;
        return SUCCESS;
    }

    function unapprove() {
        db_query('UPDATE `nbhzvn_games` SET `approved` = 0 WHERE `id` = ?', $this->id);
        $this->approved = false;
        return SUCCESS;
    }

    function delete() {
        db_query('DELETE FROM `nbhzvn_comments` WHERE `game_id` = ?', $this->id);
        db_query('DELETE FROM `nbhzvn_gamefollows` WHERE `game_id` = ?', $this->id);
        db_query('DELETE FROM `nbhzvn_gameratings` WHERE `game_id` = ?', $this->id);
        db_query('DELETE FROM `nbhzvn_changelogs` WHERE `game_id` = ?', $this->id);
        db_query('DELETE FROM `nbhzvn_games` WHERE `id` = ?', $this->id);
        $this->id = null;
        return SUCCESS;
    }

    function ratings() {
        $result = db_query('SELECT g.`id`, COUNT(r.`game_id`) AS total, AVG(r.`rating`) AS average FROM `nbhzvn_gameratings` r LEFT JOIN `nbhzvn_games` g ON r.`game_id` = g.`id` WHERE g.`id` = ?', $this->id);
        while ($row = $result->fetch_object()) {
            if (!$row->average) $row->average = 0;
            $row->average = floatval($row->average);
            return $row;
        }
        return new stdClass();
    }

    function follow_count() {
        $result = db_query('SELECT g.`id`, COUNT(f.`game_id`) AS follow_count FROM `nbhzvn_gamefollows` f LEFT JOIN `nbhzvn_games` g ON f.`game_id` = g.`id` WHERE g.`id` = ?', $this->id);
        while ($row = $result->fetch_object()) return $row->follow_count;
        return 0;
    }

    function followers() {
        $followers = [];
        $result = db_query('SELECT `author` FROM `nbhzvn_gamefollows` WHERE `game_id` = ?', $this->id);
        while ($row = $result->fetch_object()) array_push($followers, new Nbhzvn_User($row->author));
        return $followers;
    }

    function check_follow($user_id) {
        $result = db_query('SELECT `author` FROM `nbhzvn_gamefollows` WHERE `game_id` = ? AND `author` = ?', $this->id, $user_id);
        return !!$result->num_rows;
    }

    function check_rating($user_id) {
        $result = db_query('SELECT `author` FROM `nbhzvn_gameratings` WHERE `game_id` = ? AND `author` = ?', $this->id, $user_id);
        return !!$result->num_rows;
    }

    function toggle_follow($user_id) {
        $result = db_query('SELECT `author` FROM `nbhzvn_gamefollows` WHERE `game_id` = ? AND `author` = ?', $this->id, $user_id);
        if ($result->num_rows > 0) {
            db_query('DELETE FROM `nbhzvn_gamefollows` WHERE `game_id` = ? AND `author` = ?', $this->id, $user_id);
            return ACTION_UNFOLLOW;
        }
        db_query('INSERT INTO `nbhzvn_gamefollows` (`author`, `game_id`) VALUES (?, ?)', $user_id, $this->id);
        return ACTION_FOLLOW;
    }

    function toggle_featured() {
        $new_value = ($this->is_featured ? 0 : 1);
        db_query('UPDATE `nbhzvn_games` SET `is_featured` = ? WHERE `id` = ?', $new_value, $this->id);
        $this->is_featured = ($new_value == 1);
        return $new_value;
    }

    function add_rating($user_id, $rating, $reason) {
        $result = db_query('SELECT `rating` FROM `nbhzvn_gameratings` WHERE `game_id` = ? AND `author` = ?', $this->id, $user_id);
        if ($result->num_rows > 0) throw new Exception(ALREADY_RATED);
        db_query('INSERT INTO `nbhzvn_gameratings` (`author`, `timestamp`, `game_id`, `rating`, `reason`) VALUES (?, ?, ?, ?, ?)', $user_id, time(), $this->id, $rating, $reason);
        return $this->ratings();
    }

    function comments() {
        $comments = [];
        $result = db_query('SELECT * FROM `nbhzvn_comments` WHERE `game_id` = ? AND `replied_to` IS NULL ORDER BY `timestamp` DESC', $this->id);
        while ($row = $result->fetch_object()) array_push($comments, new Nbhzvn_Comment($row));
        return $comments;
    }

    function comment_count() {
        $result = db_query('SELECT COUNT(*) AS item_count FROM `nbhzvn_comments` WHERE `game_id` = ? AND `replied_to` IS NULL ORDER BY `timestamp` DESC', $this->id);
        while ($row = $result->fetch_object()) return $row->item_count;
        return null;
    }

    function edit($data) {
        $query = []; $values = [];
        foreach ($data as $key => $value) {
            array_push($query, '`' . $key . '` = ?');
            array_push($values, $value);
        }
        array_push($values, $this->id);
        db_query('UPDATE `nbhzvn_games` SET ' . implode(", ", $query) . ' WHERE `id` = ?', ...$values);
        return SUCCESS;
    }

    function add_comment($user_id, $content, $replied_to) {
        global $conn;
        db_query('INSERT INTO `nbhzvn_comments` (`author`, `timestamp`, `game_id`, `content`, `replied_to`, `edited`) VALUES (?, ?, ?, ?, ?, 0)', $user_id, time(), $this->id, $content, $replied_to);
        if ($conn->insert_id) {
            $comment_id = $conn->insert_id;
            $check = array();
            if (!$replied_to) {
                $author = new Nbhzvn_User($this->uploader);
                if ($author->id && $author->id != $user_id) {
                    $author->send_comment_notification($this, $user_id, $comment_id);
                    $check[$author->id] = true;
                }
            }
            else {
                $replying_comment = new Nbhzvn_Comment($replied_to);
                $author = new Nbhzvn_User($replying_comment->author);
                if ($author->id && $author->id != $user_id) {
                    $author->send_comment_notification($this, $user_id, $replied_to, COMMENT_REPLY, $comment_id);
                    $check[$author->id] = true;
                }
            }
            $mentions = get_mention_users($content);
            foreach ($mentions as $author) {
                if ($author->id && $author->id != $user_id && !$check[$author->id]) {
                    $author->send_comment_notification($this, $user_id, $replied_to ? $replied_to : $comment_id, COMMENT_MENTION, $replied_to ? $comment_id : null);
                    $check[$author->id] = true;
                }
            }
            return new Nbhzvn_Comment($comment_id);
        }
        return FAILED;
    }

    function discord_embed() {
        $http = (empty($_SERVER["HTTPS"]) ? "http" : "https");
        $host = get_root_domain();
        global $engine_vocab;
        global $language_vocab;
        $site = $http . "://" . $host;
        $embed = new Discord_Embed();
        $embed->title = $this->name;
        $embed->image = new Discord_EmbedImage($site . "/uploads/" . $this->image);
        $substr = substr($this->description, 0, 2045);
        $substr = substr($substr, 0, strrpos($substr, " "));
        $embed->description = (strlen($this->description) > 2048) ? ($substr . "...") : $this->description;
        $embed->url = $site . "/games/" . $this->id;
        $fields = [
            new Discord_EmbedField("Nhà phát triển:", $this->author, true),
            new Discord_EmbedField("Năm ra mắt:", strval($this->release_year), true),
            new Discord_EmbedField("Phần mềm làm game:", $engine_vocab[$this->engine], true),
            new Discord_EmbedField("Ngôn ngữ:", $language_vocab[$this->language], true),
            new Discord_EmbedField("Hỗ trợ:", implode(", ", array_map(function($v) {global $os_vocab; return $os_vocab[$v];}, explode(",", $this->supported_os))), true)
        ];
        if ($this->translator) $fields[] = new Discord_EmbedField("Dịch giả:", $this->translator, true);
        $embed->add_fields(...$fields);
        return $embed;
    }

    function update_file_time($time) {
        db_query('UPDATE `nbhzvn_games` SET `file_updated_time` = ? WHERE `id` = ?', $time, $this->id);
        $this->file_updated_time = $time;
    }

    function all_ratings() {
        $ratings = [];
        $query = db_query('SELECT * FROM `nbhzvn_gameratings` WHERE `game_id` = ? ORDER BY `timestamp` DESC', $this->id);
        while ($row = $query->fetch_object()) array_push($ratings, new Nbhzvn_Rating($row));
        return $ratings;
    }

    function change_owner($id) {
        db_query('UPDATE `nbhzvn_games` SET `uploader` = ? WHERE `id` = ?', $id, $this->id);
        $this->uploader = $id;
    }

    function changelogs() {
        $logs = [];
        $query = db_query('SELECT * FROM `nbhzvn_changelogs` WHERE `game_id` = ? ORDER BY `timestamp` DESC', $this->id);
        while ($row = $query->fetch_object()) {
            $changelog = new Nbhzvn_Changelog($row);
            $changelog->set_game_object($this);
            array_push($logs, $changelog);
        }
        return $logs;
    }

    function add_changelog($version, $description, $user = new Nbhzvn_User(0)) {
        global $conn;
        db_query('INSERT INTO `nbhzvn_changelogs` (`game_id`, `timestamp`, `version`, `description`) VALUES (?, ?, ?, ?)', $this->id, time(), $version, $description);
        if ($conn->insert_id) {
            $changelog_id = $conn->insert_id;
            $changelog = new Nbhzvn_Changelog($changelog_id);
            $changelog->set_game_object($this);
            return $changelog->to_html($user);
        }
        else return FAILED;
    }

    function send_beta_notifications() {
        global $user;
        if ($this->beta_users == null) return;
        foreach ($this->beta_users as $user_id) {
            $tmp_tester = new Nbhzvn_User($user_id);
            if ($tmp_tester->id) $tmp_tester->send_notification("/games/" . $this->id . "#betaDownloadSection", "**" . $user->display_name() . "** đã cập nhật bản Beta mới cho game **" . $this->name . "**.");
        }
    }

    function update_links($links = [], $beta_links = []) {
        $this->links = $links;
        $this->beta_links = $beta_links;
        db_query("UPDATE `nbhzvn_games` SET `links` = ?, `beta_links` = ? WHERE id = ?", json_encode($links), json_encode($beta_links), $this->id);
    }
}

class Nbhzvn_Comment {
    public $id;
    public $author;
    public $timestamp;
    public $game_id;
    public $content;
    public $replied_to;
    public $edited;

    function __construct($id) {
        if (is_object($id)) $data = $id;
        else {
            $result = db_query('SELECT * FROM `nbhzvn_comments` WHERE id = ?', $id);
            while ($row = $result->fetch_object()) $data = $row;
        }
        $this->id = $data->id;
        $this->author = $data->author;
        $this->timestamp = $data->timestamp;
        $this->game_id = $data->game_id;
        $this->content = $data->content;
        $this->replied_to = $data->replied_to;
        $this->edited = ($data->edited == 1);
    }

    function fetch_replies() {
        $replies = [];
        if (!$this->replied_to) {
            $result = db_query('SELECT * FROM `nbhzvn_comments` WHERE `replied_to` = ? ORDER BY `timestamp` ASC', $this->id);
            while ($row = $result->fetch_object()) array_push($replies, new Nbhzvn_Comment($row));
        }
        return $replies;
    }

    function reply_count() {
        if (!$this->replied_to) {
            $result = db_query('SELECT COUNT(*) AS item_count FROM `nbhzvn_comments` WHERE `replied_to` = ?', $this->id);
            while ($row = $result->fetch_object()) return $row->item_count;
        }
        return 0;
    }

    function delete() {
        db_query('DELETE FROM `nbhzvn_comments` WHERE `id` = ?', $this->id);
        db_query('DELETE FROM `nbhzvn_comments` WHERE `replied_to` = ?', $this->id);
        return SUCCESS;
    }

    function edit($content) {
        db_query('UPDATE `nbhzvn_comments` SET `content` = ?, `edited` = 1 WHERE `id` = ?', $content, $this->id);
        $this->content = $content;
        $this->edited = true;
        return SUCCESS;
    }
    
    function to_html($is_reply, $user = new Nbhzvn_User(0), $hide_options = false, $highlighted = 0) {
        $this_author = new Nbhzvn_User($this->author);
        $this_game = new Nbhzvn_Game($this->game_id);
        $replies = $this->reply_count();
        $pre_reply_html = "";
        if ($highlighted && $replies > 0) {
            $replies_list = $this->fetch_replies();
            foreach ($replies_list as $reply) $pre_reply_html .= $reply->to_html(true, $user, $hide_options, $reply->id == $highlighted);
            $replies = 0;
        }
        $options = [];
        if (!$hide_options) {
            if ($user->id == $this->author) array_push($options, '<a href="javascript:void(0)" onclick="editComment(' . $this->id . ')">Chỉnh sửa</a>');
            if ($user->id == $this->author || $user->type == 3) array_push($options, '<a href="javascript:void(0)" onclick="deleteComment(' . $this->id . ')">Xoá</a>');
            if ($user->id) array_push($options, '<a href="javascript:void(0)" onclick="replyComment(' . ($this->replied_to ? $this->replied_to : $this->id) . ', ' . ($this->replied_to ? ('\'' . $this_author->username . '\'') : "null") . ')">Trả lời</a>');
        }
        return '<div id="comment-' . $this->id . '" class="comment_container"><div class="anime__review__item"><div class="anime__review__item__text' . ($is_reply ? " reply" : "") . '"><h6><a href="/profile/' . $this->author . '">' . $this_author->display_name() . '</a>' . $this_author->badge_html(($this_game->uploader == $this_author->id)) . ' • <a href="/games/' . $this->game_id . ($this->replied_to ? ('?highlighted_comment=' . $this->replied_to . '&reply_comment=' . $this->id . '#comment-' . $this->id) : ('?highlighted_comment=' . $this->id . '#comment-' . $this->id)) . '"><span style="font-size: 10pt">' . comment_time($this->timestamp) . ($this->edited ? " (đã chỉnh sửa)" : "") . (($highlighted == $this->id) ? '<span class="highlighted_comment">Bình luận nổi bật</span>' : "") . '</span></a></h6><p id="comment-' . $this->id . '-content">' . process_mentions($this->content) . '</p>' . (count($options) ? ('<p id="comment-' . $this->id . '-options" class="comment_options">' . implode(" • ", $options) . '</p>') : "") . '</div><div id="comment-' . $this->id . '-replies" class="comment_replies">' . $pre_reply_html . '</div>' . (($replies > 0 && !$hide_options) ? '<div class="view_replies_btn" id="comment-' . $this->id . '-repliesbtn"><a href="javascript:void(0)" onclick="viewReplies(' . $this->id . ')">Xem ' . $replies . ' câu trả lời...</a></div>' : "") . '</div></div>';
    }
}

class Nbhzvn_Rating {
    public $id;
    public $author;
    public $timestamp;
    public $game_id;
    public $rating;
    public $reason;
    private $user;

    function __construct($id) {
        if (is_object($id)) $data = $id;
        else {
            $result = db_query('SELECT * FROM `nbhzvn_gameratings` WHERE `id` = ?', $id);
            while ($row = $result->fetch_object()) $data = $row;
        }
        $this->id = $data->id;
        $this->user = new Nbhzvn_User($data->author);
        $this->author = substr($this->user->display_name(), 0, 2) . "*****";
        $this->timestamp = $data->timestamp;
        $this->game_id = $data->game_id;
        $this->rating = $data->rating;
        $this->reason = $data->reason;
    }

    function delete($reason) {
        global $user;
        db_query('DELETE FROM `nbhzvn_gameratings` WHERE `id` = ?', $this->id);
        $game = new Nbhzvn_Game($this->game_id);
        if ($this->user->id != $user->id) $this->user->send_notification("/games/" . $this->game_id, "Quản Trị Viên đã xóa đánh giá game **" . $game->name . "** của bạn với lý do: " . $reason);
    }

    function to_html($user = new Nbhzvn_User(0)) {
        $stars = "";
        for ($i = 0; $i < $this->rating; $i++) $stars .= '<i class="fa fa-star"></i>';
        for ($i = 0; $i < 5 - $this->rating; $i++) $stars .= '<i class="fa fa-star-o"></i>';
        $reason = htmlentities($this->reason ? $this->reason : "");
        if (!$reason) $reason = '<i>Thành viên này không để lại lý do nào' . (($this->timestamp < 1740576333) ? ', vì đánh giá này được thực hiện trước thời gian website yêu cầu thành viên phải ghi lý do' : ''). '.</i>';
        return '<div id="rating-' . $this->id . '" class="comment_container"><div class="anime__review__item"><div class="anime__review__item__text"><h6><a href="' . ($user->type < 3 ? 'javascript:void(0)' : ('/profile/' . $this->user->id)) . '">' . ($user->type < 3 ? $this->author : $this->user->display_name()) . '</a> • <span class="rating_stars">' . $stars . '</span> • <span style="font-size: 10pt">' . comment_time($this->timestamp) . '</span></h6><p style="font-size: 10pt; margin-top: 5px">' . $reason . '</p>' . ($user->type == 3 ? ('<p class="comment_options"><a href="javascript:void(0)" onclick=\'deleteRating(' . $this->id . ')\'>Xoá đánh giá này</a></p>') : '') . '</div></div></div>';
    }
}

class Nbhzvn_Changelog {
    public $id;
    public $game_id;
    public $timestamp;
    public $version;
    public $description;
    private $game_object;

    function __construct($id) {
        if (is_object($id)) $data = $id;
        else {
            $result = db_query('SELECT * FROM `nbhzvn_changelogs` WHERE id = ?', $id);
            while ($row = $result->fetch_object()) $data = $row;
        }
        $this->id = $data->id;
        $this->game_id = $data->game_id;
        $this->timestamp = $data->timestamp;
        $this->version = $data->version;
        $this->description = $data->description;
    }

    function edit_description($desc) {
        db_query('UPDATE `nbhzvn_changelogs` SET `description` = ? WHERE id = ?', $desc, $this->id);
        $this->description = $desc;
    }

    function delete() {
        db_query('DELETE FROM `nbhzvn_changelogs` WHERE id = ?', $this->id);
        $this->id = null;
    }

    function set_game_object($game = new Nbhzvn_Game(0)) {
        $this->game_object = $game;
    }

    function to_html($user) {
        $parsedown = new Parsedown();
        $parsedown->setSafeMode(true);
        $parsedown->setMarkupEscaped(true);
        $parsedown->setBreaksEnabled(true);
        if (!is_object($this->game_object)) $this->game_object = new Nbhzvn_Game($this->game_id);
        $game = $this->game_object;
        return '<div id="changelog-' . $this->id . '" class="comment_container"><div class="anime__review__item"><div class="anime__review__item__text"><div class="row"><div class="col-6" style="text-align: left"><h4><b>' . $this->version . '</b></h4></div><div class="col-6" style="text-align: right; font-size: 10pt; margin-top: 4px">' . timestamp_to_string($this->timestamp, true) . '</div></div><div style="font-size: 10pt" id="changelog-' . $this->id . '-content">' . $parsedown->text($this->description) . '</div>' . (($user->id == $game->uploader) ? '<p id="changelog-' . $this->id . '-options" class="comment_options"><a href="javascript:void(0)" onclick="editChangelog(' . $this->id . ')">Chỉnh sửa nội dung</a> • <a href="javascript:void(0)" onclick="deleteChangelog(' . $this->id . ')">Xoá</a></p></p>' : '') . '</div></div></div>';
    }
}
?>