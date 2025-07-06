<?php
require "api/functions.php";
require "api/users/functions.php";
require "api/users/cookies.php";
if ($user) {
    if (get("speedrun")) {
        setcookie("nbhzvn_username", "", time() - 3600, "/", "." . $_SERVER["HTTP_HOST"]);
        setcookie("nbhzvn_login_token", "", time() - 3600, "/", "." . $_SERVER["HTTP_HOST"]);
        header("Location: /login?speedrun=1");
    }
    redirect_to_home();
}
$error = "";
if (post("submit")) {
    $username = post("username"); $password = post("password");
    if (!check_csrf(post("csrf_token"))) $error = "Mã xác thực CSRF không đúng.";
    else if (!$username || !$password) $error = "Vui lòng nhập đầy đủ thông tin.";
    else if (strlen($username) < 6 || special_chars($username)) $error = "Tên đăng nhập không hơp lệ.";
    else if (strlen($password) < 8) $error = "Mật khẩu phải trên 8 kí tự.";
    else {
        try {
            $result = login($username, $password);
            if ($result == SUCCESS) {
                $user = new Nbhzvn_User($username);
                $user->apply_cookie();
                header(get("speedrun") ? "Location: https://speedrun.nbhzvn.one" : "Location: /");
                die();
            }
        }
        catch (Exception $ex) {
            switch ($ex->getMessage()) {
                case MISSING_INFORMATION: {
                    $error = "Vui lòng nhập đầy đủ thông tin.";
                    break;
                }
                case INCORRECT_CREDENTIALS: {
                    $error = "Tên đăng nhập hoặc mật khẩu không đúng.";
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
refresh_csrf();
?>
<!DOCTYPE html>
<html lang="zxx">

<head>
    <?php
        $title = "Đăng Nhập";
        require __DIR__ . "/head.php";
    ?>
</head>

<body>
    <!-- Header Section Begin -->
    <header class="header">
        <?php require "header.php" ?>
    </header>
    <!-- Header End -->

    <!-- Normal Breadcrumb Begin -->
    <section class="normal-breadcrumb set-bg" data-setbg="/img/normal-breadcrumb.jpg">
    </section>
    <!-- Normal Breadcrumb End -->

    <!-- Login Section Begin -->
    <section class="login spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="login__form">
                        <h3>Đăng Nhập</h3>
                        <form action="" method="POST">
                            <div class="input__item">
                                <input type="text" name="username" placeholder="Tên Người Dùng" required>
                                <span class="icon_profile"></span>
                            </div>
                            <div class="input__item">
                                <input type="password" name="password" placeholder="Mật Khẩu" required>
                                <span class="icon_lock"></span>
                            </div>
                            <input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>" />
                            <p style="color: #e36666"><i><?php echo $error ?></i></p>
                            <p><small>Bằng cách tiếp tục sử dụng trang web này, bạn đã đồng ý với <a href="/tos">Điều Khoản Sử Dụng</a> và <a href="/privacy_policy">Chính Sách Bảo Mật</a> của trang web.</small></p>
                            <button type="submit" name="submit" class="site-btn" value="Submit">Đăng Nhập</button>
                        </form>
                        <a href="/forgot_password" class="forget_pass">Quên mật khẩu?</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="login__register">
                        <h3>Chưa Có Tài Khoản?</h3>
                        <a href="/register" class="primary-btn">Đăng Ký</a>
                    </div>
                </div>
            </div>
            <div class="login__social">
                <div class="row d-flex justify-content-center">
                    <div class="col-lg-6">
                        <div class="login__social__links">
                            <span>hoặc là</span>
                            <ul>
                                <li><a href="/discord" class="discord">Đăng Nhập Bằng Discord</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Login Section End -->

    <!-- Footer Section Begin -->
    <footer class="footer">
        <?php require "footer.php" ?>
      </footer>
      <!-- Footer Section End -->

    <!-- Js Plugins -->
    <script src="/js/jquery-3.3.1.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/mixitup.min.js"></script>
    <script src="/js/jquery.slicknav.js"></script>
    <script src="/js/owl.carousel.min.js"></script>
    <script src="/js/main.js?v=<?=$res_version?>"></script>


</body>

</html>