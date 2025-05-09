<?php
require "api/functions.php";
require "api/users/functions.php";
require "api/users/cookies.php";
if (!$user || !get("id")) redirect_to_home();

$error = "";
$fatal_error = "";

function delete_files($thumbnail = "none", $links, $screenshots) {
    unlink("./uploads/" . $thumbnail);
    foreach (array_map(function($v) {return $v->path;}, $links) as $path) unlink("./uploads/" . $path);
    foreach ($screenshots as $path) unlink("./uploads/" . $path);
}

try {
    $game = new Nbhzvn_Game(intval(get("id"))); $reason = post("reason");
    if (!$game->id || ($game->uploader != $user->id && $user->type < 3)) redirect_to_home();
    $thumbnail = $game->image; $links = $game->links; $screenshots = $game->screenshots; $uploader = $game->uploader;
    $author = new Nbhzvn_User($uploader);
    if (post("submit")) {
        if (!check_csrf(post("csrf_token"))) $error = "Mã xác thực CSRF không đúng.";
        else if (!$user->verify_passphrase(post("password"))) $error = "Mật khẩu hiện tại không đúng.";
        else if ($author->id != $user->id && !$reason) $error = "Hãy nhập lý do tại sao bạn muốn xóa game này.";
        else {
            $game->delete();
            delete_files($thumbnail, $links, $screenshots);
            if ($author->id && $author->id != $user->id) $author->send_notification(null, "Một Quản Trị Viên vừa xoá game **" . $game->name . "** của bạn với lý do: " . $reason . "\n\nGame của bạn đã không còn tồn tại trên trang web này nữa.");
            $fatal_error = "Đã xoá game <b>" . htmlentities($game->name) . "</b> thành công.";
        }
    }
}
catch (Exception $ex) {
    switch ($ex->getMessage()) {
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
        $title = "Xoá Game";
        require __DIR__ . "/head.php";
    ?>
</head>

<body>
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
                <h3>Xoá Game</h3>
                <?php if ($fatal_error): ?>
                <p><?php echo $fatal_error ?></p>
                <p><a href="/"><button class="site-btn">Về Trang Chủ</button></p>
                <?php else: ?>
                <p>Nhập mật khẩu hiện tại của bạn để xác nhận xoá game <b><?php echo $game->name ?></b> (ID: <?php echo $game->id ?>).</p>
                <p><b><i>Việc xoá game này không thể được hoàn tác!</i></b></p>
                <form action="" method="POST">
                    <div class="input__item" style="width: 100%">
                        <input type="password" name="password" placeholder="Mật Khẩu Hiện Tại" required>
                        <span class="icon_lock"></span>
                    </div>
                    <?php if ($author->id != $user->id): ?>
                    <div class="input__item" style="width: 100%">
                        <input type="text" name="reason" placeholder="Lý Do" required>
                        <span class="icon_pencil"></span>
                    </div>
                    <?php endif ?>
                    <input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>" />
                    <p style="color: #e36666"><i><?php echo $error ?></i></p>
                    <button type="submit" name="submit" class="site-btn" value="Submit">Tiến Hành Xoá</button>
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

    <!-- Js Plugins -->
    <script src="/js/jquery-3.3.1.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/mixitup.min.js"></script>
    <script src="/js/jquery.slicknav.js"></script>
    <script src="/js/owl.carousel.min.js"></script>
    <script src="/js/main.js?v=<?=$res_version?>"></script>

</body>

</html>