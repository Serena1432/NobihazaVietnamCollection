<?php
require "api/functions.php";
require "api/users/functions.php";
require "api/users/cookies.php";
require "api/users/discord.php";
$error = "";
$fatal_error = "";
if (post("submit")) {
    if ($user) {
        header("Location: /");
        die();
    }
    $discord_user = get_discord_info($_SESSION["discord_token_type"], $_SESSION["discord_access_token"]);
    if (!$discord_user->id) $error = "Có lỗi xảy ra khi yêu cầu thông tin từ Discord. Vui lòng đăng nhập lại.";
    else {
        $temp_user = get_user_from_discord_id($discord_user->id);
        if ($temp_user) {
            $temp_user->apply_cookie();
            header("Location: /");
            die();
        }
        else {
            $username = (post("type") == "1") ? $discord_user->username : post("username"); $password = post("password");
            if (!check_csrf(post("csrf_token"))) $error = "Mã xác thực CSRF không đúng.";
            else if (!$username || !$password) $error = "Vui lòng nhập đầy đủ thông tin.";
            else if (strlen($username) < 6 || special_chars($username)) $error = "Tên đăng nhập không hơp lệ.";
            else if (strlen($password) < 8) $error = "Mật khẩu phải trên 8 kí tự.";
            else {
                try {
                    $result = register($username, $discord_user->email, $password, 0, $discord_user->id);
                    if ($result == SUCCESS) {
                        $temp_user = new Nbhzvn_User($username);
                        $temp_user->apply_cookie();
                        header("Location: /");   
                        die();
                    }
                }
                catch (Exception $ex) {
                    switch ($ex->getMessage()) {
                        case MISSING_INFORMATION: {
                            $error = "Vui lòng nhập đầy đủ thông tin.";
                            break;
                        }
                        case USERNAME_ALREADY_EXISTS: {
                            $error = "Tên đăng nhập đã tồn tại.";
                            break;
                        }
                        case EMAIL_ALREADY_EXISTS: {
                            $error = "Email này đã tồn tại.";
                            break;
                        }
                        default: {
                            $error = "Có lỗi không xác định xảy ra. Vui lòng báo cáo cho nhà phát triển của website.";
                            break;
                        }
                    }
                }
            }
        }
    }
}
else if (get("code")) {
    try {
        if (get("state") != $_SESSION["discord_csrf"]) $fatal_error = "Mã xác thực CSRF không đúng.";
        $result = request_token(get("code"));
        if (!$result->access_token) {
            switch ($result->error) {
                case "invalid_grant": {
                    $fatal_error = "Yêu cầu đăng nhập đã hết hạn. Vui lòng đăng nhập lại.";
                    break;
                }
                default: {
                    $fatal_error = "Có lỗi xảy ra khi yêu cầu thông tin từ Discord. Vui lòng đăng nhập lại.";
                    break;
                }
            }
        }
        else {
            $discord_user = get_discord_info($result->token_type, $result->access_token);
            if (!$discord_user->id) $fatal_error = "Có lỗi xảy ra khi yêu cầu thông tin từ Discord. Vui lòng đăng nhập lại.";
            else {
                $temp_user = get_user_from_discord_id($discord_user->id);
                if ($temp_user) {
                    if (!$user || !$user->id) {
                        $temp_user->apply_cookie();
                        header("Location: /");
                        die();
                    }
                    else $fatal_error = "Tài khoản Discord này đã được liên kết với một tài khoản khác rồi.";
                }
                else if ($user && $user->id) {
                    if ($user->discord_id) $fatal_error = "Tài khoản này đã được liên kết với một tài khoản Discord rồi. Hãy bỏ liên kết tài khoản hiện tại trước khi liên kết lại.";
                    else {
                        $user->update_discord_id($discord_user->id);
                        $_SESSION["discord_csrf"] = null;
                        $fatal_error = "Đã liên kết tài khoản Discord <b>" . $discord_user->username . "</b> với tài khoản hiện tại của bạn (<b>" . $user->username . "</b>).";
                    }
                }
                else {
                    $_SESSION["discord_token_type"] = $result->token_type;
                    $_SESSION["discord_access_token"] = $result->access_token;
                }
            }
        }
    }
    catch (Exception $ex) {
        switch ($ex->getMessage()) {
            case MISSING_INFORMATION: {
                $fatal_error = "Vui lòng nhập đầy đủ thông tin.";
                break;
            }
            default: {
                $fatal_error = "Có lỗi không xác định xảy ra. Vui lòng báo cáo cho nhà phát triển của website.";
                break;
            }
        }
    }
}
else {
    $_SESSION["discord_csrf"] = random_string(64);
    header('Location: https://discord.com/oauth2/authorize?client_id=' . $client_id . '&response_type=code&redirect_uri=' . $http . '%3A%2F%2F' . $host . '%2Fdiscord&scope=identify+email&state=' . $_SESSION["discord_csrf"]);
    die();
}
refresh_csrf();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php
        $title = "Kết Nối Với Discord";
        require __DIR__ . "/head.php";
    ?>
</head>
<body>
<div class="app-container">
    <?php require 'sidebar.php'; ?>
    <div class="main-content">
        <?php require "mobile-top-nav.php" ?>
        <div class="normal-hero"></div>
        <div class="m-3 mt-4">
            <div class="card-dark p-4 shadow-lg" style="max-width: 1080px; margin: auto">
                <h4 class="fw-bold mb-4">Kết Nối Với Discord</h4>
                <?php if ($fatal_error): ?>
                    <div class="alert alert-dark text-white border-secondary" role="alert">
                        <?php echo $fatal_error ?>
                    </div>
                    <div class="text-center mt-3">
                        <a href="/" class="btn btn-primary fw-bold">Về Trang Chủ</a>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-4">Tài khoản Discord của bạn chưa liên kết với tài khoản nào của trang web. Tuy nhiên thì bạn có thể đăng ký một tài khoản mới bằng biểu mẫu ở bên dưới, và liên kết với tài khoản Discord của bạn ngay sau đó.</p>
                    <form action="" method="POST">
                        <div class="form-check mb-2">
                            <input class="form-check-input bg-dark border-secondary" type="radio" name="type" value="1" id="radio1" onclick="updateRadio()">
                            <label class="form-check-label text-white" for="radio1">
                                Đặt theo tên đăng nhập trên Discord của bạn (<b><?php echo $discord_user->username ?></b>)
                            </label>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input bg-dark border-secondary" type="radio" name="type" value="2" id="radio2" onclick="updateRadio()" checked>
                            <label class="form-check-label text-white" for="radio2">
                                Chọn tên đăng nhập khác:
                            </label>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted"><b>Tên Người Dùng</b></label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control bg-dark border-secondary text-white" name="username" id="username" placeholder="Tên Đăng Nhập" onclick="setRadio()">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted"><b>Mật Khẩu</b></label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control bg-dark border-secondary text-white" name="password" placeholder="Mật Khẩu" required>
                            </div>
                        </div>
                        
                        <input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>" />
                        <?php if ($error) echo '<p class="text-danger fst-italic">' . $error . '</p>'; ?>
                        <button type="submit" name="submit" value="Submit" class="btn btn-primary w-100 py-2 fw-bold mb-3 mt-2">Đăng Ký</button>
                    </form>
                    
                    <div class="mt-4 pt-3 border-top border-secondary text-muted small">
                        <p><i>Để liên kết tài khoản Discord này với tài khoản có sẵn, hãy đăng nhập vào tài khoản đó trước và vào phần <b>Thay đổi thông tin -> Liên kết tài khoản Discord</b>.</i></p>
                    </div>
                <?php endif ?>
            </div>
        </div>
        <?php require 'footer.php'; ?>
    </div>
</div>
<script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/js/main.js"></script>
<script>
    function setRadio() {
        document.getElementById("radio1").checked = false;
        document.getElementById("radio2").checked = true;
        updateRadio();
    }

    function updateRadio() {
        if (document.getElementById("radio1").checked) document.getElementById("username").disabled = true;
        else document.getElementById("username").disabled = false;
    }
</script>
</body>
</html>
