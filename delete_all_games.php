<?php
require "api/functions.php";
require "api/users/functions.php";
require "api/users/cookies.php";
$fatal_error = "";

if (get("completed")) $fatal_error = "<b>Toàn bộ</b> các game do bạn tải lên cùng với các dữ liệu liên quan đã bị xóa vĩnh viễn khỏi hệ thống.<br>Bạn có thể trở về trang chủ hoặc tiếp tục sử dụng các tính năng khác.";
else if (!$user) redirect_to_home();
else if ($user->type < 2) redirect_to_home(); // Only uploaders and admins can upload/delete games

$error = "";
$verification_string = "Tôi xác nhận việc muốn xóa toàn bộ game do tài khoản " . $user->username . " tải lên. Tôi hiểu rằng dữ liệu của các game này (bao gồm cả bình luận, đánh giá và lượt theo dõi của người khác) sẽ hoàn toàn không thể khôi phục lại được sau khi đã xóa, dù chỉ là một phần nhỏ.";

if (!get("completed")) {
    try {
        if (post("submit")) {
            if (!check_csrf(post("csrf_token"))) $error = "Mã xác thực CSRF không đúng.";
            else if (!post("password")) $error = "Vui lòng nhập mật khẩu.";
            else if (!$user->verify_passphrase(post("password"))) $error = "Mật khẩu không đúng.";
            else if (post("confirm") != $verification_string) $error = "Vui lòng sao chép toàn bộ câu xác nhận vào ô Xác Nhận (đúng từng kí tự và phân biệt hoa thường).";
            else {
                // Delete all game data related to the games uploaded by that user
                db_query('DELETE c FROM `nbhzvn_comments` c JOIN `nbhzvn_games` g ON c.game_id = g.id WHERE g.uploader = ?', $user->id);
                db_query('DELETE r FROM `nbhzvn_gameratings` r JOIN `nbhzvn_games` g ON r.game_id = g.id WHERE g.uploader = ?', $user->id);
                db_query('DELETE f FROM `nbhzvn_gamefollows` f JOIN `nbhzvn_games` g ON f.game_id = g.id WHERE g.uploader = ?', $user->id);
                // Delete all games uploaded by that user
                db_query('DELETE FROM `nbhzvn_games` WHERE `uploader` = ?', $user->id);
                
                header("Location: /delete_all_games?completed=1");
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
        $title = "Xóa Toàn Bộ Game";
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
                <h4 class="fw-bold mb-4 text-danger"><i class="bi bi-trash-fill"></i> Xóa Toàn Bộ Game Đã Tải Lên</h4>
                
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
                            <p class="mb-2">Bạn chuẩn bị <b>xóa vĩnh viễn toàn bộ game mà bạn đã tải lên</b> ra khỏi cơ sở dữ liệu của <b>Nobihaza Vietnam Collection</b>.</p>
                            <p class="mb-2">Yêu cầu xóa vĩnh viễn sẽ được thực hiện ngay lập tức sau khi bạn đã xác nhận mà không có thời gian chờ. Hãy cân nhắc thật kĩ trước khi nhấn nút xóa, vì tất cả những thứ sau đây sẽ bị xóa khỏi trang web vĩnh viễn:</p>
                            <ul class="mb-3">
                                <li><b>Tất cả các game bạn đã từng tải lên hệ thống.</b></li>
                                <li><b>Toàn bộ bình luận, lượt đánh giá và lượt theo dõi của những người dùng khác dành cho các game của bạn.</b></li>
                            </ul>
                        </div>
                        <p class="mb-0 fw-bold">Một khi đã xóa vĩnh viễn thì không ai sẽ có thể khôi phục lại các game này dù chỉ là một phần nhỏ! Vì vậy đây là cảnh báo cuối cùng, hãy cân nhắc thật kĩ trước khi tiếp tục thực hiện!</p>
                    </div>

                    <p class="text-muted small mb-3">Tài khoản của bạn cùng với các bình luận và lượt đánh giá bạn đã thực hiện trên các game của người khác <b>sẽ KHÔNG BỊ XÓA</b>.</p>
                    
                    <hr class="border-secondary my-4">
                    
                    <p class="text-muted mb-2">Để xác nhận việc xóa, hãy nhập mật khẩu của bạn và sao chép câu sau đây vào ô Xác Nhận bên dưới (không bao gồm dấu ngoặc kép):</p>
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
                        
                        <button type="submit" name="submit" value="Submit" class="btn btn-danger fw-bold w-100 py-2"><i class="bi bi-trash-fill"></i> Tiến Hành Xóa Toàn Bộ Game</button>
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
