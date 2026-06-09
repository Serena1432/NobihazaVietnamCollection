<?php
require "api/functions.php";
require "api/users/functions.php";
require "api/users/cookies.php";
$error = "";
if (!$user) redirect_to_home();
if (post("submit")) {
    $current_password = post("current_password"); $password = post("password"); $confirm_password = post("confirm_password");
    $display_name = post("display_name"); $email = post("email"); $description = post("description"); $avatar = post("avatar"); $delete_avatar = post("delete_avatar");
    if (!check_csrf(post("csrf_token"))) $error = "Mã xác thực CSRF không đúng.";
    else if (!$current_password) $error = "Vui lòng nhập mật khẩu hiện tại.";
    else if (!$user->verify_passphrase($current_password)) $error = "Mật khẩu hiện tại không đúng.";
    else {
        if ($password && $confirm_password) {
            if (strlen($password) < 8) $error = "Mật khẩu phải trên 8 kí tự.";
            else if ($password != $confirm_password) $error = "Mật khẩu ở 2 ô không giống nhau.";
            else $change_password = $password;
        }
        if ($display_name) {
            if (strlen($display_name) < 6) $error = "Tên hiển thị phải trên 6 kí tự.";
            else if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $display_name)) $error = "Tên hiển thị không được chứa kí tự đặc biệt.";
            else $change_display_name = $display_name;
        }
        if ($delete_avatar) {
            $do_change_avatar = true;
            $change_avatar = null;
        } else if ($avatar) {
            $do_change_avatar = true;
            $change_avatar = $avatar;
        }
        if ($email) {
            if (!check_email_validity($email)) $error = "Email này không được hỗ trợ.";
            if (email_exists($email)) $error = "Email này đã được sử dụng.";
            else $change_email = $email;
        }
        if (!$error) {
            $changed = false;
            if ($change_password) {
                $user->change_passphrase($change_password);
                $changed = true;
            }
            if ($change_display_name) {
                $user->change_display_name($change_display_name);
                $changed = true;
            }
            if (isset($do_change_avatar) && $do_change_avatar) {
                $user->change_avatar($change_avatar);
                $changed = true;
            }
            if ($change_email) {
                $user->change_email($change_email);
                $changed = true;
            }
            if ($description) {
                $user->change_description($description);
                $changed = true;
            }
            if ($changed) {
                if (isset($change_password) && $change_password) {
                    setcookie("nbhzvn_username", "", time() - 3600);
                    setcookie("nbhzvn_login_token", "", time() - 3600);
                    db_query('UPDATE `nbhzvn_users` SET `login_token` = "" WHERE `id` = ?', $user->id);
                    $notice = "Hoàn tất đổi mật khẩu, vui lòng đăng nhập lại với mật khẩu mới để tiếp tục.";
                } else {
                    $notice = "Đổi thông tin hoàn tất.";
                }
            }
            else $error = "Không có thông tin nào được đổi.";
        }
    }
}
if ($notice) {
    $redirect_url = (isset($change_password) && $change_password) ? "/" : "/change_info";
}
refresh_csrf();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php
        $title = "Thay Đổi Thông Tin";
        require __DIR__ . "/head.php";
    ?>
    <link rel="stylesheet" href="/css/toastr.css" />
    <?php
    ?>
</head>
<body>
<div class="app-container">
    <?php require 'sidebar.php'; ?>
    <div class="main-content">
        <?php require "mobile-top-nav.php" ?>
        <div class="normal-hero"></div>
        
        <div class="container-fluid content-wrapper mt-4">
            <?php if ($notice): ?>
                <div class="card-dark p-4 shadow-lg" style="max-width: 1080px; margin: auto">
                    <div class="alert alert-dark text-white border-secondary mb-4" role="alert">
                        <i class="bi bi-check-circle-fill text-success me-2"></i> <?php echo $notice ?>
                    </div>
                    <div class="text-center mt-3">
                        <a href="<?php echo $redirect_url ?>" class="btn btn-primary fw-bold">Tiếp Tục</a>
                    </div>
                </div>
            <?php else: ?>
            <div class="row">
                <div class="col-lg-7 mb-4">
                    <div class="card-dark p-4 shadow-lg h-100">
                        <h4 class="fw-bold mb-4">Thay Đổi Thông Tin</h4>
                        <form action="" method="POST" autocomplete="off" autocorrect="off">
                            <p class="text-muted small mb-3">Nhập mật khẩu hiện tại trước khi muốn thay đổi thông tin.</p>
                            
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" class="form-control bg-dark border-secondary text-white" name="current_password" placeholder="Mật Khẩu Hiện Tại" required>
                                </div>
                            </div>
                            
                            <hr class="border-secondary my-4">
                            <p class="text-muted small mb-3">Bỏ trống một thông tin nếu bạn không muốn thay đổi thông tin đó.</p>
                            
                            
                            <div class="mb-3">
                                <label class="form-label text-muted"><b>Ảnh Đại Diện</b></label>
                                <div class="mb-3">
                                    <img class="thumbnail_image img-fluid rounded-circle shadow" id="thumbnailImage" style="width: 120px; height: 120px; object-fit: cover; border: 2px solid var(--primary-color);" src="<?php echo htmlspecialchars($user->avatar_url) ?>" />
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-image"></i></span>
                                    <input readonly class="form-control bg-dark border-secondary text-white" style="cursor: pointer" name="avatar" placeholder="Nhấn vào đây để tải ảnh đại diện" onclick="uploadThumbnail()" id="thumbnail" value="">
                                </div>
                                
                                <div class="form-check mt-2">
                                    <input class="form-check-input bg-dark border-secondary" type="checkbox" name="delete_avatar" value="1" id="deleteAvatarCheck">
                                    <label class="form-check-label text-muted small" for="deleteAvatarCheck">
                                        Xóa ảnh đại diện hiện tại (trở về mặc định)
                                    </label>
                                </div>

                                <div class="progress mt-2" id="thumbnailProgressBar" style="display: none;">
                                    <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>

                            <?php if (!$user->verification_required): ?>
                            <div class="mb-3">
                                <label class="form-label text-muted"><b>Tên Hiển Thị</b></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control bg-dark border-secondary text-white" name="display_name" placeholder="Tên Hiển Thị" value="<?php echo htmlspecialchars($user->display_name) ?>">
                                </div>
                            </div>
                            <?php endif ?>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted"><b>Địa Chỉ Email</b></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control bg-dark border-secondary text-white" name="email" placeholder="Email Mới">
                                </div>
                            </div>
                            
                            <?php if (!$user->verification_required): ?>
                            <div class="mb-3">
                                <label class="form-label text-muted"><b>Mô Tả Bản Thân</b></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-pencil"></i></span>
                                    <textarea class="form-control bg-dark border-secondary text-white" placeholder="Mô Tả" name="description" rows="4"><?php echo htmlspecialchars($user->description) ?></textarea>
                                </div>
                                <div class="form-text text-muted small mt-1"><i>Mô tả có hỗ trợ Markdown.</i></div>
                            </div>
                            <?php endif ?>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted"><b>Mật Khẩu Mới</b></label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control bg-dark border-secondary text-white" name="password" placeholder="Mật Khẩu Mới">
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control bg-dark border-secondary text-white" name="confirm_password" placeholder="Nhập Lại Mật Khẩu Mới">
                                </div>
                            </div>
                            
                            <?php if ($user->verification_required): ?>
                            <div class="alert alert-dark text-white border-secondary small py-2 mb-3" role="alert">
                                Một số tuỳ chọn có thể bị ẩn đi đối với những tài khoản chưa được xác minh.
                            </div>
                            <?php endif ?>
                            
                            <input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>" />
                            <?php if ($error) echo '<p class="text-danger fst-italic mt-2">' . $error . '</p>'; ?>
                            
                            <button type="submit" name="submit" value="Submit" class="btn btn-primary fw-bold mt-2"><i class="bi bi-check2-circle"></i> Thay Đổi</button>
                        </form>
                    </div>
                </div>
                
                <div class="col-lg-5 mb-4">
                    <div class="card-dark p-4 shadow-lg h-100">
                        <h4 class="fw-bold mb-4">Liên Kết Mạng Xã Hội</h4>
                        <?php if (!$user->verification_required): ?>
                            <?php if ($user->discord_id): ?>
                                <p class="text-muted small">Tài khoản của bạn hiện đang được liên kết với Discord.</p>
                                <a href="/discord_unlink" class="btn btn-secondary fw-bold w-100 mb-3"><i class="bi bi-discord"></i> Bỏ Liên Kết Discord</a>
                            <?php else: ?>
                                <p class="text-muted small">Liên kết tài khoản của bạn với Discord để có thể đăng nhập nhanh chóng và bảo mật hơn.</p>
                                <a href="/discord" class="btn btn-discord fw-bold w-100 mb-3"><i class="bi bi-discord"></i> Liên Kết Với Discord</a>
                            <?php endif ?>
                        <?php else: ?>
                            <div class="alert alert-dark text-white border-secondary small py-2" role="alert">
                                Vui lòng xác minh tài khoản trước khi liên kết với mạng xã hội khác.
                            </div>
                        <?php endif ?>
                        
                        <hr class="border-secondary my-4">
                        
                        <h4 class="fw-bold mb-4 text-danger"><i class="bi bi-exclamation-triangle-fill"></i> NGUY HIỂM!</h4>
                        <p class="text-muted small">Các thao tác ở khu vực này không thể hoàn tác. Vui lòng cân nhắc kỹ trước khi tiếp tục.</p>
                        <?php if ($user->type >= 2): ?>
                        <a href="/delete_all_games" class="btn btn-warning fw-bold w-100 mb-3"><i class="bi bi-trash"></i> Xóa Toàn Bộ Game Đã Tải Lên</a>
                        <?php endif; ?>
                        <a href="/delete_all_comments" class="btn btn-warning fw-bold w-100 mb-3"><i class="bi bi-chat-dots-fill"></i> Xóa Toàn Bộ Bình Luận</a>
                        <a href="/delete_account" class="btn btn-danger fw-bold w-100"><i class="bi bi-trash-fill"></i> Xóa Tài Khoản</a>
                    </div>
                </div>
                
            </div>
            <?php endif ?>
        </div>

        <input type="file" id="thumbnailFile" class="d-none" accept=".jpg, .png, .jpeg, .webp|image/*" />
        <?php require 'footer.php'; ?>
    </div>
</div>
<script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/js/jquery-3.3.1.min.js"></script>
<script src="/js/base64.min.js"></script>
<script src="/js/main.js"></script>
<script src="/js/toastr.js"></script>
<script src="/js/api.js"></script>
<script src="/js/uploader.js"></script>
</body>
</html>
