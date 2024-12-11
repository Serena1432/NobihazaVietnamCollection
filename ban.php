<?php
require "api/functions.php";
require "api/users/functions.php";
require "api/users/cookies.php";
if (!$user || $user->type < 3 || !get("id")) redirect_to_home();

$error = "";
$fatal_error = "";

try {
    $ban_user = new Nbhzvn_User(intval(get("id")));
    if (!$ban_user->id || $ban_user->id == $user->id || $ban_user->type == 3) redirect_to_home();
    else if ($ban_user->ban_information) $fatal_error = "Thành viên <b>" . htmlentities($ban_user->username) . "</b> đang bị cấm rồi.";
    else if (post("submit")) {
        $reason = post("reason");
        if (!check_csrf(post("csrf_token"))) $error = "Mã xác thực CSRF không đúng.";
        else if (!$user->verify_passphrase(post("password"))) $error = "Mật khẩu hiện tại không đúng.";
        else if (!$reason) $error = "Vui lòng nhập lý do bạn muốn cấm thành viên này.";
        else {
            $ban_user->ban($reason);
            $fatal_error = "Đã cấm thành viên <b>" . htmlentities($ban_user->username) . "</b> thành công.";
        }
    }
}
catch (Exception $ex) {
    switch ($ex->getMessage()) {
        case DB_CONNECTION_ERROR: {
            $error = "Lỗi kết nối tới máy chủ. Vui lòng thử lại.";
            break;
        }
        default: {
            $error = "Có lỗi không xác định xảy ra. Vui lòng báo cáo cho nhà phát triển của website.";
            break;
        }
    }
}
refresh_csrf();
?>
<!DOCTYPE html>
<html lang="zxx">

<head>
    <?php
        $title = "Cấm Thành Viên";
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
    <section class="normal-breadcrumb set-bg" data-setbg="/img/normal-breadcrumb.jpg">
    </section>
    <!-- Normal Breadcrumb End -->

    <!-- Signup Section Begin -->
    <section class="signup spad">
        <div class="container">
            <div class="login__form page">
                <h3>Cấm Thành Viên</h3>
                <?php if ($fatal_error): ?>
                <p><?php echo $fatal_error ?></p>
                <p><a href="/"><button class="site-btn">Về Trang Chủ</button></p>
                <?php else: ?>
                <p>Bạn đang chuẩn bị cấm thành viên <b><?php echo $ban_user->username ?></b> (ID: <?php echo $ban_user->id ?>).</p>
                <p>Hãy nhập lý do bạn muốn cấm thành viên này để tiếp tục:</p>
                <form action="" method="POST">
                    <div class="input__item" style="width: 100%">
                        <input type="password" name="password" placeholder="Mật Khẩu Hiện Tại" required>
                        <span class="icon_lock"></span>
                    </div>
                    <div class="input__item" style="width: 100%">
                        <input type="text" name="reason" placeholder="Lý Do" required>
                        <span class="icon_pencil"></span>
                    </div>
                    <input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>" />
                    <p style="color: #e36666"><i><?php echo $error ?></i></p>
                    <button type="submit" name="submit" class="site-btn" value="Submit">Tiến Hành Cấm</button>
                </form>
                <?php endif ?>
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
    <script src="/js/jquery-3.3.1.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/player.js"></script>
    <script src="/js/jquery.nice-select.min.js"></script>
    <script src="/js/mixitup.min.js"></script>
    <script src="/js/jquery.slicknav.js"></script>
    <script src="/js/owl.carousel.min.js"></script>
    <script src="/js/main.js"></script>

</body>

</html>