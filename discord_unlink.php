<?php
require "api/functions.php";
require "api/users/functions.php";
require "api/users/cookies.php";
if (!$user || !$user->discord_id) redirect_to_home();

$error = "";
$fatal_error = "";
$notice = "";

try {
    if (post("submit")) {
        if (!check_csrf(post("csrf_token"))) $error = "Mã xác thực CSRF không đúng.";
        else if (!post("password")) $error = "Vui lòng nhập mật khẩu.";
        else if (!$user->verify_passphrase(post("password"))) $error = "Mật khẩu không đúng.";
        else {
            db_query('UPDATE `nbhzvn_users` SET `discord_id` = "" WHERE `id` = ?', $user->id);
            $fatal_error = "Bỏ liên kết tài khoản Discord thành công.";
        }
    }
}
catch (Exception $ex) {
    switch ($ex->getMessage()) {
        case MISSING_INFORMATION: {
            $error = "Vui lòng nhập đầy đủ thông tin.";
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
<html lang="vi">
<head>
    <?php
        $title = "Bỏ Liên Kết Discord";
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
                <h4 class="fw-bold mb-4">Bỏ Liên Kết Discord</h4>
                <?php if ($fatal_error): ?>
                    <div class="alert alert-dark text-white border-secondary" role="alert">
                        <?php echo $fatal_error ?>
                    </div>
                    <div class="text-center mt-3">
                        <a href="/" class="btn btn-primary fw-bold">Về Trang Chủ</a>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-4">Nhập mật khẩu để tiến hành bỏ liên kết với tài khoản Discord của bạn.</p>
                    <form action="" method="POST">
                        <div class="mb-4">
                            <label class="form-label text-muted"><b>Mật Khẩu</b></label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control bg-dark border-secondary text-white" name="password" placeholder="Mật Khẩu" required>
                            </div>
                        </div>
                        
                        <input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>" />
                        <?php if ($error) echo '<p class="text-danger fst-italic">' . $error . '</p>'; ?>
                        <button type="submit" name="submit" value="Submit" class="btn btn-primary w-100 py-2 fw-bold mb-2">Xác Nhận</button>
                    </form>
                <?php endif ?>
            </div>
        </div>
        <?php require 'footer.php'; ?>
    </div>
</div>
<script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/js/main.js"></script>
</body>
</html>
