<?php
require "api/functions.php";
require "api/users/functions.php";
require "api/users/cookies.php";
if ($user) redirect_to_home();
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
                case USERNAME_ALREADY_EXISTS: {
                    $error = "Tên đăng nhập không hợp lệ.";
                    break;
                }
                case EMAIL_ALREADY_EXISTS: {
                    $error = "Email này không được hỗ trợ.";
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
        $title = "Đăng Ký Tài Khoản";
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
                <h4 class="fw-bold mb-4">Đăng Ký Tài Khoản</h4>
                <p class="text-muted">Đăng ký tài khoản tại Nobihaza Vietnam Community Collection để có thể theo dõi, bình luận và đánh giá game mà bạn thích.</p>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label text-muted"><b>Tên Người Dùng</b></label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="username" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted"><b>Địa Chỉ Email</b></label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control bg-dark border-secondary text-white" name="email" required>
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
                    <?php if ($error) echo '<p class="text-danger fst-italic">' . $error . '</p>'; ?>
                    <button type="submit" name="submit" value="Submit" class="btn btn-primary w-100 py-2 fw-bold mb-3">Đăng Ký</button>
                </form>
                
                <div class="mb-4 text-muted small">
                    <p><i>Website sẽ chỉ sử dụng Địa Chỉ Email của bạn để xác nhận tài khoản và khi lấy lại mật khẩu, ngoài ra email của bạn sẽ không được sử dụng cho bất kì hành động nào khác của website.<br>Chỉ hỗ trợ email đến từ Gmail, Yahoo! Mail hoặc Outlook.</i></p>
                    <p>Bằng cách tiếp tục sử dụng trang web này, bạn đã đồng ý với <a href="/tos" class="text-primary text-decoration-none">Điều Khoản Sử Dụng</a> và <a href="/privacy_policy" class="text-primary text-decoration-none">Chính Sách Bảo Mật</a> của trang web.</p>
                </div>
                
                <div>
                    <a href="/discord"><button class="btn btn-discord w-100 py-2 fw-bold mb-3">Đăng Ký Bằng Discord</button></a>
                </div>
                
                <div class="text-center">
                    <span class="text-muted small">Đã có tài khoản? </span>
                    <a href="/login" class="text-primary small fw-bold text-decoration-none">Đăng nhập ngay</a>
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
