<?php
require "api/functions.php";
require "api/users/functions.php";
require "api/users/cookies.php";
require "api/games/functions.php";
$parsedown = new Parsedown();
$parsedown->setSafeMode(true);
$parsedown->setMarkupEscaped(true);
$parsedown->setBreaksEnabled(true);
if (!$user && !get("id")) redirect_to_home();
$profile_user = $user;
if (get("id") && is_numeric(get("id"))) $profile_user = new Nbhzvn_User(intval(get("id")));
if (!$profile_user->id) redirect_to_home();
if (!get("repo")) {
    $followed_games = $profile_user->followed_games();
    $comments = $profile_user->comments();
    if ($profile_user->type >= 2) {
        $uploaded_games = $profile_user->uploaded_games();
        $popular_games = array_merge([], $uploaded_games);
        usort($popular_games, function($a, $b) {
            return $b->downloads <=> $a->downloads;
        });
        $total_views = 0; $total_downloads = 0;
        foreach ($uploaded_games as $tmp_game) {
            $total_views += $tmp_game->views;
            $total_downloads += $tmp_game->downloads;
        }
    }
    if ($profile_user->id == $user->id) {
        $email_end_pos = strpos($user->email, "@");
        $censored_email = substr($user->email, 0, 2) . "••••••" . substr($user->email, $email_end_pos - 2, strlen($user->email) - $email_end_pos + 2);
    }
    else $overwrite_title = $profile_user->display_name();
}
else {
    switch (get("repo")) {
        case "uploads": {
            if ($profile_user->type < 2) redirect_to_home();
            $repo = $profile_user->uploaded_games();
            $overwrite_title = "Game Đã Tải Lên";
            break;
        }
        case "follows": {
            $repo = $profile_user->followed_games();
            $overwrite_title = "Danh Sách Theo Dõi";
            break;
        }
        case "unapproved": {
            if ($user->type < 2) redirect_to_home();
            $repo = unapproved_games($user);
            $overwrite_title = "Game Đang Chờ Duyệt";
            break;
        }
        case "popular": {
            if ($profile_user->type < 2) redirect_to_home();
            $repo = $profile_user->popular_games();
            $overwrite_title = "Game Phổ Biến";
            break;
        }
        default: {
            redirect_to_home();
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zxx">

<head>
    <?php
        $title = $overwrite_title ? $overwrite_title : "Thông Tin Tài Khoản";
        require __DIR__ . "/head.php";
    ?>
</head>

<body>
    <!-- Header Section Begin -->
    <header class="header">
        <?php require "header.php"; ?>
    </header>
    <!-- Header End -->

    <!-- Breadcrumb Begin -->
    <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="/"><i class="fa fa-home"></i> Trang Chủ</a>
                        <?php if ($overwrite_title): ?>
                        <a href="/profile/<?php echo $profile_user->id ?>">Thông Tin Tài Khoản</a>
                        <span><?php echo $overwrite_title ?></span>
                        <?php else: ?>
                        <span>Thông Tin Tài Khoản</span>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Anime Section Begin -->
    <section class="anime-details spad">
        <div class="container">
            <?php if ($repo): ?>
                <h3 class="nbhzvn_title"><b><?php echo $overwrite_title ?> <?php if (get("repo") != "unapproved") echo ' Của ' . $profile_user->display_name() ?></b></h3>
                <div class="row" id="games">
                    <?php
                        $limit = 0;
                        foreach ($repo as $game) {
                            echo echo_search_game($game, true);
                            $limit++;
                            if ($limit == 20) break;
                        }
                    ?>
                </div>
                <?php echo pagination(count($repo)) ?>
            <?php else: ?>
            <div class="anime__details__content">
                <div class="anime__details__text">
                    <div class="anime__details__title">
                        <h3><?php echo $profile_user->display_name() ?> <?php echo $profile_user->badge_html(false, true) ?></h3>
                        <span><?php echo $profile_user->username ?> (ID: <?php echo $profile_user->id ?>)</span>
                    </div>
                    <div class="anime__details__widget">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <ul>
                                    <li><span>Chức vụ:</span> <?php
                                        $account_type = array(
                                            1 => "Tài khoản thường",
                                            2 => "Uploader",
                                            3 => "Quản trị viên"
                                        );
                                        echo $account_type[$profile_user->type]
                                    ?></li>
                                    <?php if ($profile_user->type >= 2): ?>
                                    <li><span>Game đã tải lên:</span> <?php echo count($uploaded_games ? $uploaded_games : []) ?></li>
                                    <?php else: ?>
                                    <li><span>Game đã theo dõi:</span> <?php echo count($followed_games ? $followed_games : []) ?></li>
                                    <?php endif ?>
                                    <?php if ($profile_user->type >= 2): ?>
                                    <li><span>Tổng lượt xem:</span> <?php echo number_format($total_views, 0, ",", ".") ?></li>
                                    <?php endif ?>
                                    <?php if ($profile_user->id == $user->id): ?>
                                        <li><span>Email:</span> <?php echo $censored_email ?></li>
                                    <?php endif ?>
                                </ul>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <ul>
                                    <li><span>Ngày tạo tài khoản:</span> <?php echo timestamp_to_string($profile_user->timestamp) ?></li>
                                    <li><span>Số bình luận đã gửi:</span> <?php echo count($comments ? $comments : []) ?></li>
                                    <?php if ($profile_user->type >= 2): ?>
                                    <li><span>Tổng lượt tải xuống:</span> <?php echo number_format($total_downloads, 0, ",", ".") ?></li>
                                    <?php endif ?>
                                    <?php if ($profile_user->id == $user->id): ?>
                                        <li><span>ID Discord:</span> <?php echo $user->discord_id ? $user->discord_id : "Chưa liên kết" ?></li>
                                    <?php endif ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php if ($profile_user->description) echo $parsedown->text($profile_user->description); else echo '<p><i>Tài khoản này chưa đặt mô tả.</i></p>' ?><br>
                    <div class="anime__details__btn"><p>
                        <?php if ($profile_user->id == $user->id): ?>
                        <a href="/change_info" class="nbhzvn_btn"><span><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;&nbsp;Thay đổi thông tin</span></a>
                        <?php else: ?>
                        <?php if ($user->type == 3): ?>
                            <a href="/send_notification/<?php echo $profile_user->id ?>" class="nbhzvn_btn"><span>Gửi thông báo</span></a>
                        <?php endif ?>
                        <?php if ($user->type == 3 && $profile_user->type < 3): ?>
                            <a href="/assign/<?php echo $profile_user->id ?>" class="nbhzvn_btn"><span>Thay đổi chức vụ</span></a>
                            <?php if ($profile_user->ban_information): ?>
                            <a href="/unban/<?php echo $profile_user->id ?>" class="nbhzvn_btn"><span>Bỏ cấm thành viên này</span></a>
                            <?php else: ?>
                            <a href="/ban/<?php echo $profile_user->id ?>" class="nbhzvn_btn"><span>Cấm thành viên này</span></a>
                            <?php endif ?>
                        <?php endif ?>
                        <?php endif ?>
                    </p></div><br>
                </div>
                <div class="row">
                    <div class="col-lg-8 col-md-8">
                        <?php $unapproved_games = unapproved_games($user) ?>
                        <?php if ($user->id == $profile_user->id && $user->type >= 2 && count($unapproved_games)): ?>
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-8">
                                <div class="section-title">
                                    <h4>Game Đang Chờ Duyệt</h4>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="btn__all">
                                    <a href="/unapproved/<?php echo $profile_user->id ?>" class="primary-btn">Xem tất cả <span class="arrow_right"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <?php
                                foreach ($unapproved_games as $tmp_game) echo echo_search_game($tmp_game, true);
                            ?>
                        </div><br>
                        <?php endif ?>
                        <?php if ($profile_user->type >= 2): ?>
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-8">
                                <div class="section-title">
                                    <h4>Game Đã Tải Lên</h4>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="btn__all">
                                    <a href="/uploads/<?php echo $profile_user->id ?>" class="primary-btn">Xem tất cả <span class="arrow_right"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <?php
                                $limit = 0;
                                foreach ($uploaded_games as $tmp_game) {
                                    echo echo_search_game($tmp_game, true);
                                    $limit++;
                                    if ($limit == 6) break;
                                }
                            ?>
                        </div><br>
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-8">
                                <div class="section-title">
                                    <h4>Game Phổ Biến</h4>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="btn__all">
                                    <a href="/popular/<?php echo $profile_user->id ?>" class="primary-btn">Xem tất cả <span class="arrow_right"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <?php
                                $limit = 0;
                                foreach ($popular_games as $tmp_game) {
                                    echo echo_search_game($tmp_game, true);
                                    $limit++;
                                    if ($limit == 6) break;
                                }
                            ?>
                        </div><br>
                        <?php endif ?>
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-8">
                                <div class="section-title">
                                    <h4>Danh Sách Theo Dõi</h4>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="btn__all">
                                    <a href="/follows/<?php echo $profile_user->id ?>" class="primary-btn">Xem tất cả <span class="arrow_right"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <?php
                                $limit = 0;
                                foreach ($followed_games as $tmp_game) {
                                    echo echo_search_game($tmp_game, true);
                                    $limit++;
                                    if ($limit == 6) break;
                                }
                            ?>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="anime__details__sidebar">
                            <div class="section-title">
                                <h5>Bình luận gần đây</h5>
                            </div>
                            <?php
                                $limit = 0;
                                foreach ($comments as $comment) {
                                    echo $comment->to_html(false, $user, true);
                                    $limit++;
                                    if ($limit == 6) break;
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <?php endif ?>
            </div>
        </section>
        <!-- Anime Section End -->

        <!-- Footer Section Begin -->
        <footer class="footer">
            <?php require "footer.php" ?>
        </footer>
          <!-- Footer Section End -->

        <!-- Js Plugins -->
        <script src="/js/jquery-3.3.1.min.js"></script>
    <script src="/js/base64.min.js"></script>
        <script src="/js/bootstrap.min.js"></script>
        <script src="/js/mixitup.min.js"></script>
        <script src="/js/jquery.slicknav.js"></script>
        <script src="/js/owl.carousel.min.js"></script>
        <script src="/js/main.js?v=<?=$res_version?>"></script>
        <?php if ($repo): ?>
        <script>var userId = <?php echo $profile_user->id ?>, repo = "<?php if (get("repo") != "unapproved") echo "users"; else echo "games" ?>/<?php echo addslashes(get("repo")) ?>";</script>
        <script src="/js/api.js?v=<?=$res_version?>"></script>
        <script src="/js/profile.js?v=<?=$res_version?>"></script>
        <?php endif ?>

    </body>

    </html>