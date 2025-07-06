<?php
function get_user_from_cookie() {
    $username = cookie("nbhzvn_username"); $login_token = cookie("nbhzvn_login_token");
    if (!$username || !$login_token) return null;
    $user = new Nbhzvn_User($username);
    if (!$user->id || !$user->check_login_token($login_token)) {
        setcookie("nbhzvn_username", "", time() - 3600, "/", "." . $_SERVER["HTTP_HOST"]);
        setcookie("nbhzvn_login_token", "", time() - 3600, "/", "." . $_SERVER["HTTP_HOST"]);
        header("Location: " . $_SERVER["REQUEST_URI"]);
        return null;
    }
    return $user;
}

$user = get_user_from_cookie();
if ($user->verification_required
    && !str_contains($_SERVER["REQUEST_URI"], "verify")
    && !str_contains($_SERVER["REQUEST_URI"], "change_info")
    && !str_contains($_SERVER["REQUEST_URI"], "profile")) {
    if (!$is_api) {
        header("Location: /verify");
        die();
    }
    else {
        api_response(null, "Bạn cần xác minh tài khoản trước khi thực hiện hành động này.", 403);
    }
}
if ($user->ban_information
    && !str_contains($_SERVER["REQUEST_URI"], "banned")) {
    if (!$is_api) {
        header("Location: /banned");
        die();
    }
    else {
        api_response(null, "Tài khoản của bạn đã bị vô hiệu hoá.", 403);
    }
}
?>