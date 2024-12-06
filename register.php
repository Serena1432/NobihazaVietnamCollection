<?php
require "api/functions.php";
require "api/users/functions.php";
$error = "";
if (post("submit")) {
    $username = post("username"); $password = post("password"); $email = post("email");
    if (!check_csrf(post("csrf_token"))) $error = "Mã xác thực CSRF không đúng.";
    else if (!$username || !$email || !$password) $error = "Vui lòng nhập đầy đủ thông tin.";
    else if (!check_email_validity($email)) $error = "Email này không được hỗ trợ.";
    else if (strlen($username) < 6 || special_chars($username)) $error = "Tên đăng nhập không hơp lệ.";
    else if (strlen($password) < 8) $error = "Mật khẩu phải trên 8 kí tự.";
    else {
        try {
            $result = register($username, $email, $password);
            $error = "Đăng ký thành công.";
        }
        catch (Exception $ex) {
            switch ($ex->getMessage()) {
                case DB_CONNECTION_ERROR: {
                    $error = "Lỗi kết nối tới máy chủ. Vui lòng thử lại.";
                    break;
                }
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
        $title = "Đăng Ký Tài Khoản";
        require __DIR__ . "/head.php";
    ?>
</head>

<body>
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

    <!-- Header Section Begin -->
    <header class="header">
        <?php require "header.php"; ?>
    </header>
    <!-- Header End -->

    <!-- Normal Breadcrumb Begin -->
    <section class="normal-breadcrumb set-bg" data-setbg="img/normal-breadcrumb.jpg">
    </section>
    <!-- Normal Breadcrumb End -->

    <!-- Signup Section Begin -->
    <section class="signup spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="login__form">
                        <h3>Đăng Ký</h3>
                        <form action="" method="POST">
                            <div class="input__item">
                                <input type="text" name="username" placeholder="Tên Người Dùng" required>
                                <span class="icon_profile"></span>
                            </div>
                            <div class="input__item">
                                <input type="email" name="email" placeholder="Địa Chỉ Email" required>
                                <span class="icon_mail"></span>
                            </div>
                            <div class="input__item">
                                <input type="password" name="password" placeholder="Mật Khẩu" required>
                                <span class="icon_lock"></span>
                            </div>
                            <input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>" />
                            <p style="color: #e36666"><i><?php echo $error ?></i></p>
                            <button type="submit" name="submit" class="site-btn" value="Submit">Đăng Ký</button>
                        </form>
                        <br>
                        <p><i>Website sẽ chỉ sử dụng Địa Chỉ Email của bạn để xác nhận tài khoản và khi lấy lại mật khẩu, ngoài ra email của bạn sẽ không được sử dụng cho bất kì hành động nào khác của website.<br>Chỉ hỗ trợ email đến từ Gmail, Yahoo! Mail hoặc Outlook.</i></p>
                        <h5>Đã có tài khoản? <a href="/login">Đăng Nhập</a></h5>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="login__social__links">
                        <h3>Đăng Ký Bằng Mạng Xã Hội</h3>
                        <ul>
                            <li><a href="#" class="discord">Đăng Ký Bằng Discord</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Signup Section End -->

    <!-- Footer Section Begin -->
    <footer class="footer">
        <?php require "footer.php" ?>
      </footer>
      <!-- Footer Section End -->

      <!-- Search model Begin -->
      <div class="search-model">
        <div class="h-100 d-flex align-items-center justify-content-center">
            <div class="search-close-switch"><i class="icon_close"></i></div>
            <form class="search-model-form">
                <input type="text" id="search-input" placeholder="Search here.....">
            </form>
        </div>
    </div>
    <!-- Search model end -->

    <!-- Js Plugins -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/player.js"></script>
    <script src="js/jquery.nice-select.min.js"></script>
    <script src="js/mixitup.min.js"></script>
    <script src="js/jquery.slicknav.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>

</body>

</html>