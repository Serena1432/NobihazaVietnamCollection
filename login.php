<?php
require "api/functions.php";
require "api/users/functions.php";
require "api/users/cookies.php";
if ($user) redirect_to_home();
$error = "";
if (post("submit")) {
    $username = post("username"); $password = post("password");
    if (!check_csrf(post("csrf_token"))) $error = "Mã xác thực CSRF không đúng.";
    else if (!$username || !$password) $error = "Vui lòng nhập đầy đủ thông tin.";
    else if (strlen($username) < 6 || special_chars($username)) $error = "Tên đăng nhập không hợp lệ.";
    else if (strlen($password) < 8) $error = "Mật khẩu phải trên 8 kí tự.";
    else {
        try {
            $result = login($username, $password);
            if ($result == SUCCESS) {
                $user = new Nbhzvn_User($username);
                $user->apply_cookie();
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
<html lang="vi">
<head>
    <?php
        $title = "Đăng Nhập";
        require "head.php";
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
                <h4 class="fw-bold mb-4">Đăng Nhập</h4>
                <p class="text-muted">Đăng nhập vào Nobihaza Vietnam Community Collection để có thể theo dõi, bình luận và đánh giá game mà bạn thích.</p>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label text-muted"><b>Tên Người Dùng</b></label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="username" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted"><b>Mật Khẩu</b></label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control bg-dark border-secondary text-white" name="password" required>
                        </div>
                    </div>
                    <input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>" />
                    <p style="color: #e36666"><i><?php echo $error ?></i></p>
                    <div class="text-end mb-3"><a href="/forgot_password" class="text-primary small text-decoration-none">Quên mật khẩu?</a></div>
                    <button type="submit" name="submit" value="Submit" class="btn btn-primary w-100 py-2 fw-bold mb-3">Đăng Nhập</button>
                </form>
                <div>
                    <a href="/discord"><button class="btn btn-discord w-100 py-2 fw-bold mb-3">Đăng Nhập Bằng Discord</button></a>
                </div>
                <div class="text-center">
                    <span class="text-muted small">Chưa có tài khoản? </span>
                    <a href="register" class="text-primary small fw-bold text-decoration-none">Đăng ký ngay</a>
                </div>
            </div>
        </div>
        <?php require 'footer.php'; ?>
    </div>
</div>
<script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/js/main.js"></script>
</body>
</html>
