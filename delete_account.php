<?php
require "api/functions.php";
require "api/users/functions.php";
require "api/users/cookies.php";
$fatal_error = "";

if (get("completed")) $fatal_error = "<b>Toàn bộ</b> dữ liệu trên tài khoản của bạn đã bị xóa vĩnh viễn và không thể khôi phục lại được nữa.<br>Cảm ơn bạn đã sử dụng các tính năng của <b>Nobihaza Vietnam Community Collection</b> trong suốt thời gian qua.";
else if (!$user) redirect_to_home();

$error = "";
$verification_string = "Tôi xác nhận việc muốn xóa tài khoản " . $user->username . " vĩnh viễn, cùng với những dữ liệu trong tài khoản này. Tôi hiểu rằng dữ liệu của tài khoản này sẽ hoàn toàn không thể khôi phục lại được sau khi đã xóa, dù chỉ là một phần nhỏ. Tất cả mọi người, kể cả Quản trị viên và Nhà phát triển, sẽ không chịu trách nhiệm bồi thường hay khôi phục tài khoản của tôi trong mọi trường hợp, kể cả khi đó là trường hợp xóa nhầm ngoài ý muốn.";

if (!get("completed")) {
    try {
        if (post("submit")) {
            if (!check_csrf(post("csrf_token"))) $error = "Mã xác thực CSRF không đúng.";
            else if (!post("password")) $error = "Vui lòng nhập mật khẩu.";
            else if (!$user->verify_passphrase(post("password"))) $error = "Mật khẩu không đúng.";
            else if (post("confirm") != $verification_string) $error = "Vui lòng sao chép toàn bộ câu xác nhận vào ô Xác Nhận (đúng từng kí tự và phân biệt hoa thường).";
            else {
                // Delete all game data related to that user
                db_query('DELETE c FROM `nbhzvn_comments` c JOIN `nbhzvn_games` g ON c.game_id = g.id WHERE g.uploader = ?', $user->id);
                db_query('DELETE r FROM `nbhzvn_gameratings` r JOIN `nbhzvn_games` g ON r.game_id = g.id WHERE g.uploader = ?', $user->id);
                db_query('DELETE f FROM `nbhzvn_gamefollows` f JOIN `nbhzvn_games` g ON f.game_id = g.id WHERE g.uploader = ?', $user->id);
                // Delete all games uploaded by that user
                db_query('DELETE FROM `nbhzvn_games` WHERE `uploader` = ?', $user->id);
                // Delete all comments made by that user
                db_query('DELETE FROM `nbhzvn_comments` WHERE `author` = ?', $user->id);
                // Delete all follows made by that user
                db_query('DELETE FROM `nbhzvn_gamefollows` WHERE `author` = ?', $user->id);
                // Delete all ratings made by that user
                db_query('DELETE FROM `nbhzvn_gameratings` WHERE `author` = ?', $user->id);
                // Delete all notifications sent to that user
                db_query('DELETE FROM `nbhzvn_notifications` WHERE `user_id` = ?', $user->id);
                // Delete all timeouts for that user
                db_query('DELETE FROM `nbhzvn_timeouts` WHERE `user_id` = ?', $user->id);
                // Finally, delete the user data
                db_query('DELETE FROM `nbhzvn_users` WHERE `id` = ?', $user->id);
                // Delete the cookie data
                setcookie("nbhzvn_username", "", time() - 3600);
                setcookie("nbhzvn_login_token", "", time() - 3600);
                header("Location: /delete_account?completed=1");
                die();
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
}
refresh_csrf();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php
        $title = "Xóa Tài Khoản";
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
            <div class="card-dark p-4 shadow-lg border border-danger" style="max-width: 1080px; margin: auto">
                <h4 class="fw-bold mb-4 text-danger"><i class="bi bi-exclamation-triangle-fill"></i> Xóa Tài Khoản</h4>
                
                <?php if ($fatal_error): ?>
                    <div class="alert alert-dark text-white border-secondary mb-4" role="alert">
                        <?php echo $fatal_error ?>
                    </div>
                    <div class="text-center mt-3">
                        <a href="/" class="btn btn-primary fw-bold">Về Trang Chủ</a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger bg-transparent text-danger border-danger mb-4" role="alert">
                        <h5 class="alert-heading fw-bold"><i class="bi bi-info-circle-fill"></i> NGUY HIỂM: Hành động này KHÔNG THỂ HOÀN TÁC!</h5>
                        <div class="text-white">
                            <p class="mb-2">Bạn chuẩn bị <b>xóa vĩnh viễn tài khoản của bạn</b> ra khỏi cơ sở dữ liệu của <b>Nobihaza Vietnam Collection</b>.</p>
                            <p class="mb-2">Để đảm bảo quyền riêng tư về dữ liệu của bạn, yêu cầu xóa vĩnh viễn sẽ được thực hiện ngay lập tức sau khi bạn đã xác nhận muốn xóa tài khoản mà không có thời gian chờ. Tuy nhiên cần phải cân nhắc thật kĩ trước khi nhấn nút xóa, vì tất cả những thứ sau đây sẽ bị xóa khỏi trang web vĩnh viễn:</p>
                            <ul class="mb-3">
                                <li><b>Các game bạn đã tải lên</b> (nếu bạn là Uploader, hoặc đã từng là Uploader) và các thông tin khác (như đánh giá, bình luận và lượt theo dõi) liên quan đến các game đó. Để giữ lại một game, hãy chuyển quyền quản lý game đó sang cho thành viên khác trước.</li>
                                <li><b>Các bình luận, đánh giá và các lượt theo dõi bạn đã thực hiện.</b></li>
                                <?php if ($user->discord_id): ?>
                                <li>Bạn đang liên kết với tài khoản Discord. Việc xóa tài khoản sẽ bỏ liên kết với tài khoản Discord của bạn, tuy nhiên <b>bạn sẽ cần phải xóa thủ công các quyền được cấp cho "NbhzVN Community Collection" trong phần Cài Đặt -> Ứng Dụng Được Cho Phép của tài khoản Discord để có thể xóa hoàn toàn việc liên kết</b>.</li>
                                <?php endif ?>
                                <?php if ($user->type > 1): ?>
                                <li>Bạn cũng đang là <b><?php echo $type_vocab[$user->type] ?></b> của trang web này. <b>Việc xóa tài khoản cũng sẽ xóa chức vụ này của bạn, và sẽ không được tự động cấp lại khi bạn tạo tài khoản mới.</b></li>
                                <?php endif ?>
                            </ul>
                        </div>
                        <p class="mb-0 fw-bold">Một khi đã xóa vĩnh viễn thì không ai sẽ có thể khôi phục lại tài khoản của bạn dù chỉ là một phần nhỏ! Vì vậy đây là cảnh báo cuối cùng, hãy cân nhắc thật kĩ trước khi tiếp tục thực hiện!</p>
                    </div>

                    <p class="text-muted small mb-3">Bạn cũng có thể xem <a href="https://github.com/Serena1432/NobihazaVietnamCollection/blob/main/delete_account.php" target="_blank" class="text-primary text-decoration-none">mã nguồn của phần xóa tài khoản</a> để có thể xác nhận kĩ hơn rằng <b>tất cả dữ liệu của bạn sẽ bị xóa vĩnh viễn sau khi đã nhấn nút "Tiến Hành Xóa Tài Khoản".</b></p>
                    
                    <hr class="border-secondary my-4">
                    
                    <p class="text-muted mb-2">Để xác nhận xóa vĩnh viễn tài khoản, hãy nhập mật khẩu của bạn và sao chép câu sau đây vào ô Xác Nhận bên dưới (không bao gồm dấu ngoặc kép):</p>
                    <div class="alert alert-dark border-secondary user-select-all font-monospace small mb-4" role="alert">
                        "<b><?php echo $verification_string ?></b>"
                    </div>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label text-muted"><b>Mật Khẩu Của Bạn</b></label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control bg-dark border-secondary text-white" name="password" placeholder="Mật Khẩu" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-danger fw-bold"><b>Chuỗi Xác Nhận Xóa</b></label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-exclamation-octagon-fill"></i></span>
                                <input type="text" class="form-control bg-dark border-secondary text-white" name="confirm" placeholder="Dán đoạn chữ xác nhận vào đây" required autocomplete="off">
                            </div>
                        </div>
                        
                        <input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>" />
                        <?php if ($error) echo '<p class="text-danger fst-italic mt-2">' . $error . '</p>'; ?>
                        
                        <button type="submit" name="submit" value="Submit" class="btn btn-danger fw-bold w-100 py-2"><i class="bi bi-trash-fill"></i> Tiến Hành Xóa Tài Khoản</button>
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
