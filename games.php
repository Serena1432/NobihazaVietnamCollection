<?php
require "api/functions.php";
require "api/users/functions.php";
require "api/users/cookies.php";
require "api/games/functions.php";
$parsedown = new Parsedown();
$parsedown->setSafeMode(true);
$parsedown->setMarkupEscaped(true);
$parsedown->setBreaksEnabled(true);

if (is_numeric(get("id"))) {
    $game = new Nbhzvn_Game(intval(get("id")));
    if (!$game->id) redirect_to_home();
    if (!$game->approved && $game->uploader != $user->id && $user->type < 3) redirect_to_home();
    $title = $game->name;
    $game->add_views();
    $comments = $game->comments();
    $follows = $game->follow_count();
    $ratings = $game->ratings();
    $all_ratings = $game->all_ratings();
    $changelogs = $game->changelogs();
    $rated = ($user && $user->id) ? $game->check_rating($user->id) : false;
}
else if (get("category")) {
    switch (get("category")) {
        case "popular": {
            $title = "Game Phổ Biến";
            $repo = popular_games();
            break;
        }
        case "recent": {
            $title = "Game Mới Tải Lên";
            $repo = recent_games();
            break;
        }
        case "mobile": {
            $title = "Game Dành Cho Điện Thoại";
            $repo = mobile_games();
            break;
        }
        case "recently_updated": {
            $title = "Game Mới Cập Nhật Gần Đây";
            $repo = recently_updated_games();
            break;
        }
        default: {
            $repo = all_games();
            break;
        }
    }
}
else $repo = all_games();
?>
<!DOCTYPE html>
<html lang="zxx">

<head>
    <?php
        if (!get("category")) $title = $game->name; 
        require "head.php" 
    ?>
</head>

<body>
    <!-- Header Section Begin -->
    <header class="header">
        <?php require "header.php" ?>
        <link rel="stylesheet" href="/css/toastr.css" />
    </header>
    <!-- Header End -->

    <!-- Breadcrumb Begin -->
    <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="/"><i class="fa fa-home"></i> Trang Chủ</a>
                        <span>Thông Tin Game</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Anime Section Begin -->
    <section class="anime-details spad">
        <div class="container">
            <?php if ($game && $game->id): ?>
            <?php if (!$game->approved): ?>
                <p style="padding: 18px 30px 16px 20px; border-radius: 10px; background: #af1932; color: #fff; margin-bottom: 30px">
                    Game này vẫn chưa được phê duyệt. Chỉ có Quản Trị Viên và người quản lý game mới có thể xem thông tin game này ở thời điểm hiện tại.
                </p>
            <?php endif ?>
            <div class="anime__details__content">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="anime__details__pic set-bg" data-setbg="/uploads/<?php echo $game->image ?>">
                            <div class="comment"><i class="fa fa-comments"></i> <?php echo count($comments) ?></div>
                            <div class="view"><i class="fa fa-eye"></i> <?php echo number_format($game->views, 0, ",", ".") ?></div>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="anime__details__text">
                            <div class="anime__details__title" id="gameTitle">
                                <h3><?php echo $game->name ?></h3>
                            </div>
                            <div class="anime__details__rating">
                                <div class="rating" id="rating">
                                    <?php
                                        $average = $ratings->average;
                                        $full = floor($average); $remain = $average - $full; $index = 0; $ostar = 4 - $full;
                                        for ($i = 0; $i < $full; $i++) {
                                            $index++;
                                            echo '<a href="javascript:void(0)" onclick="rate(' . $game->id . ', ' . $index . ')"><i ' . ($rated ? 'data-rated="true"' : '') . ' id="star-' . $index . '" class="fa fa-star"></i></a> ';
                                        }
                                        if ($index < 5) {
                                            $index++;
                                            echo '<a href="javascript:void(0)" onclick="rate(' . $game->id . ', ' . $index . ')"><i ' . ($rated ? 'data-rated="true"' : '') . ' id="star-' . $index . '" class="fa fa-star' . (($remain >= 0.5) ? '-half' : '') . '-o"></i></a> ';
                                        }
                                        for ($i = 0; $i < $ostar; $i++) {
                                            $index++;
                                            echo '<a href="javascript:void(0)" onclick="rate(' . $game->id . ', ' . $index . ')"><i ' . ($rated ? 'data-rated="true"' : '') . ' id="star-' . $index . '" class="fa fa-star-o"></i></a> ';
                                        }
                                    ?>
                                </div>
                                <span id="ratingText"><?php echo number_format($ratings->total, 0, ",", ".") ?> lượt đánh giá</span>
                            </div>
                            <div class="anime__details__widget">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <ul>
                                            <li><span>Phần mềm làm game:</span> <a href="/search?engine=<?php echo $game->engine ?>"><?php echo $engine_vocab[$game->engine] ?></a></li>
                                            <li><span>Năm ra mắt:</span> <a href="/search?release_year=<?php echo $game->release_year ?>"><?php echo $game->release_year ?></a></li>
                                            <li><span>Nhà phát triển:</span> <?php
                                                $authors = explode(",", $game->author); $elements = [];
                                                foreach ($authors as $author) array_push($elements, '<a href="/search?author=' . $author . '">' . $author . '</a>');
                                                echo implode(", ", $elements);
                                            ?></li>
                                            <?php if ($game->translator): ?>
                                            <li><span>Dịch giả:</span> <?php
                                                $translators = explode(",", $game->translator); $elements = [];
                                                foreach ($translators as $translator) array_push($elements, '<a href="/search?translator=' . $translator . '">' . $translator . '</a>');
                                                echo implode(", ", $elements);
                                            ?></li>
                                            <?php endif ?>
                                            <li><span>Trạng thái:</span> <a href="/search?status=<?php echo $game->status ?>"><?php echo $status_vocab[$game->status] ?></a></li>
                                        </ul>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <ul>
                                            <li><span>Người tải lên:</span> <a href="/profile/<?php echo $game->uploader ?>"><?php $uploader = new Nbhzvn_User($game->uploader); echo $uploader->display_name() ?></a></li>
                                            <li><span>Ngôn ngữ:</span> <a href="/search?language=<?php echo $game->language ?>"><?php echo $language_vocab[$game->language] ?></a></li>
                                            <li><span>Hỗ trợ:</span> <?php
                                                $oses = explode(",", $game->supported_os); $elements = [];
                                                foreach ($oses as $os) array_push($elements, '<a href="/search?supported_os=' . $os . '">' . $os_vocab[$os] . '</a>');
                                                echo implode(", ", $elements);
                                            ?></li>
                                            <?php if ($game->tags): ?>
                                            <li><span>Thẻ:</span> <?php
                                                $tags = explode(",", $game->tags); $elements = [];
                                                foreach ($tags as $tag) array_push($elements, '<a href="/search?tags=' . $tag . '">' . $tag . '</a>');
                                                echo implode(", ", $elements);
                                            ?></li>
                                            <?php endif ?>
                                            <li><span>Lượt tải xuống:</span> <?php echo number_format($game->downloads, 0, ",", ".") ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="anime__details__btn">
                                <a href="#downloadSection" class="download-btn"><i class="fa fa-download"></i>&nbsp;&nbsp;Tải xuống</a>
                                <?php if ($game->approved): ?>
                                <a href="javascript:void(0)" onclick="toggleFollow(<?php echo $game->id ?>)" class="follow-btn"><span id="followText"><?php echo ($user && $user->id && $game->check_follow($user->id)) ? "Bỏ theo dõi" : "Theo dõi" ?></span> <label id="followCount" style="margin-right: 10px"><?php echo number_format($follows, 0, ",", ".") ?></label></a>
                                <?php if ($user->type == 3): ?>
                                <a href="/unapprove/<?php echo $game->id ?>" class="download-btn"><i class="fa fa-eye-slash"></i>&nbsp;&nbsp;Ẩn</a>
                                <?php endif ?>
                                <?php elseif ($user->type == 3): ?>
                                <a href="/approve/<?php echo $game->id ?>" class="download-btn"><i class="fa fa-check"></i>&nbsp;&nbsp;Phê duyệt</a>
                                <?php endif ?>
                                <?php if ($user->id == $game->uploader): ?>
                                <a href="/edit_game/<?php echo $game->id ?>" class="download-btn"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Chỉnh sửa</a>
                                <a href="/transfer/<?php echo $game->id ?>" class="download-btn"><i class="fa fa-exchange"></i>&nbsp;&nbsp;Chuyển quyền quản lý</a>
                                <?php endif ?>
                                <?php if ($user->type == 3): ?>
                                <a href="/feature/<?php echo $game->id ?>" class="download-btn"><?php if (!$game->is_featured) echo 'Thêm vào'; else echo "Loại bỏ khỏi" ?> mục Tiêu Điểm</a>
                                <?php endif ?>
                                <?php if ($user->id == $game->uploader || $user->type == 3): ?>
                                <a href="/delete_game/<?php echo $game->id ?>" class="download-btn"><i class="fa fa-trash"></i>&nbsp;&nbsp;Xoá</a>
                                <?php endif ?>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 col-md-8">
                        <ul class="nav nav-tabs mb-3" id="tabList" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="descriptionTab" data-toggle="pill" data-target="#descriptionTabContent" type="button" role="tab" aria-controls="descriptionTabContent" aria-selected="true">Mô Tả Game</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="changelogsTab" data-toggle="pill" data-target="#changelogsTabContent" type="button" role="tab" aria-controls="changelogsTabContent" aria-selected="false">Nhật Ký Cập Nhật</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="tabContent">
                            <div class="tab-pane fade show active" id="descriptionTabContent" role="tabpanel" aria-labelledby="descriptionTab">
                                <div class="game_description"><?php echo $parsedown->text($game->description) ?></div>
                            </div>
                            <div class="tab-pane fade" id="changelogsTabContent" role="tabpanel" aria-labelledby="changelogsTab">
                                <?php if ($game->uploader == $user->id): ?>
                                <div id="addChangelogArea">
                                    <p style="text-align: right">
                                        <a href="javascript:void(0)" onclick="addChangelog()" class="changelog-btn"><i class="fa fa-plus"></i>&nbsp;&nbsp;Thêm nhật ký mới</a>
                                    </p>
                                </div>
                                <?php endif ?>
                                <div id="changelogs">
                                    <?php
                                        $i = 0;
                                        if (count($changelogs) > 0) {
                                            foreach ($changelogs as $changelog) {
                                                if ($i >= 5) break;
                                                echo $changelog->to_html($user);
                                                $i++;
                                            }
                                        }
                                        else echo '<p id="noChangelogText"><i>Chưa có nhật ký cập nhật nào được thêm.</i></p>';
                                    ?>
                                </div>
                                <?php echo pagination(count($changelogs), 5, 1, "Changelogs"); ?>
                            </div>
                        </div><br>
                        <div>
                            <div class="section-title">
                                <h5>Ảnh chụp màn hình</h5>
                            </div>
                            <div class="game_screenshots_container">
                                <?php
                                    foreach ($game->screenshots as $screenshot) {
                                        $path = "/uploads/" . $screenshot;
                                        echo '<a href="' . $path . '" target="_blank"><img src="' . $path . '" /></a>';
                                    }
                                ?>
                            </div>
                        </div><br>
                        <?php if ($game->has_beta): ?>
                        <div id="betaDownloadSection" class="nbhzvn_beta_zone">
                            <?php if ($game->beta_links != null && count($game->beta_links) > 0): ?>
                                <h5>Tải Phiên Bản Beta</h5>
                                <p>Đây sẽ là phiên bản thử nghiệm trước dành cho những người dùng đã được người tải lên chọn, trước khi công bố công khai cho tất cả mọi người.</p>
                            <?php else: ?>
                                <h5>Phiên Bản Beta Có Sẵn</h5>
                                <p>Người tải lên game này cũng tải lên phiên bản Beta, là các phiên bản trải nghiệm trước những thay đổi mới, tính năng mới của game dành cho những thành viên thử nghiệm (Tester) đã được người tải lên chọn.<br>Bạn có thể đăng nhập vào tài khoản đã được chọn để tải xuống bản Beta này.</p>
                            <?php endif ?>
                            <?php
                                $index = 0;
                                foreach ($game->beta_links as $link) {
                                    $index++;
                                    $path = "./uploads/" . $link->path . "/" . $link->name;
                                    echo '
                                        <div class="game_file">
                                            <div class="row">
                                                <div class="col-sm-9 game_file_name"><a href="/download_beta/' . $game->id . '/' . $index . '">' . $link->name . '</a></div>
                                                <div class="col-sm-3 game_file_size">' . bytes_to_string(filesize($path)) .  '<br>
                                                    <small><b>CN lần cuối:</b> ' . timestamp_to_string(filemtime($path)) . '</small>
                                                </div>
                                            </div>
                                        </div>
                                    ';
                                }
                            ?>
                        </div><br>
                        <?php endif ?>
                        <div id="downloadSection"><br>
                            <?php if ($changelogs[0]): ?>
                            <div class="section-title">
                                <h5>Cập nhật mới nhất</h5>
                            </div>
                            <?php
                                echo $changelogs[0]->to_html(new Nbhzvn_User(0));
                            ?>
                            <?php endif ?>
                            <br>
                            <div class="section-title">
                                <h5>Tải game xuống</h5>
                            </div>
                            <p><i><b>Sử dụng WinRAR hoặc 7-Zip để giải nén!</b> Trình giải nén mặc định của Windows có khả năng bị lỗi rất cao và mình không khuyến khích các bạn dùng nó để giải nén.<br>Đối với điện thoại thì bạn nên dùng ứng dụng RAR hoặc ZArchiver để giải nén.</i></p>
                            <?php
                                if (count($game->links) > 0) {
                                    $index = 0;
                                    foreach ($game->links as $link) {
                                        $index++;
                                        $path = "./uploads/" . $link->path . "/" . $link->name;
                                        echo '
                                            <div class="game_file">
                                                <div class="row">
                                                    <div class="col-sm-9 game_file_name"><a href="/download/' . $game->id . '/' . $index . '">' . $link->name . '</a></div>
                                                    <div class="col-sm-3 game_file_size">' . bytes_to_string(filesize($path)) .  '<br>
                                                        <small><b>CN lần cuối:</b> ' . timestamp_to_string(filemtime($path)) . '</small>
                                                    </div>
                                                </div>
                                            </div>
                                        ';
                                    }
                                }
                                else echo '<p>Chưa có tệp tin nào được tải lên.</p>';
                            ?>
                        </div><br>
                        <div class="anime__details__review">
                            <div class="section-title">
                                <h5>Đánh giá game (<?php echo count($all_ratings) ?>)</h5>
                            </div>
                            <p style="font-size: 11pt"><i>Để đảm bảo an toàn, website sẽ không hiển thị tên đầy đủ của các thành viên đã đánh giá. Chỉ có Quản Trị Viên mới xem được tên hiển thị đầy đủ và thực hiện hành động đối với các đánh giá này.</i></p>
                            <div id="ratings">
                                <?php
                                    if (count($all_ratings) > 0) {
                                        $i = 0;
                                        foreach ($all_ratings as $rating) {
                                            if ($i >= 5) break;
                                            echo $rating->to_html($user);
                                            $i++;
                                        }
                                    }
                                    else echo '<p>Chưa có đánh giá nào.</p>';
                                ?>
                            </div>
                            <?php echo pagination(count($all_ratings), 5, 1, "Ratings"); ?>
                        </div>
                        <div class="anime__details__review">
                            <div class="section-title">
                                <h5>Bình luận (<?php echo count($comments) ?>)</h5>
                            </div>
                            <div id="comments">
                                <?php
                                    $highlighted_comment_id = is_numeric(get("highlighted_comment")) ? intval(get("highlighted_comment")) : 0;
                                    if ($highlighted_comment_id) {
                                        $highlighted_comment = new Nbhzvn_Comment($highlighted_comment_id);
                                        if ($highlighted_comment->id) echo $highlighted_comment->to_html(!!$highlighted_comment->replied_to, $user, false, is_numeric(get("reply_comment")) ? intval(get("reply_comment")) : $highlighted_comment_id);
                                    }
                                    foreach ($comments as $comment) if ($comment->id != $highlighted_comment_id) echo $comment->to_html(!!$comment->replied_to, $user);
                                ?>
                            </div>
                            <?php echo pagination(count($comments)); ?>
                        </div>
                        <div class="anime__details__form">
                            <div class="section-title">
                                <h5>Viết bình luận mới</h5>
                            </div>
                            <textarea id="commentContent" placeholder="Nội dung bình luận" required></textarea>
                            <button id="commentBtn" onclick="comment()"><i class="fa fa-location-arrow"></i> Bình luận</button>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="anime__details__sidebar">
                            <div class="section-title">
                                <h5>Các game khác</h5>
                            </div>
                            <?php
                                foreach (random_games($game->id) as $tmp_game) echo echo_tiled_game($tmp_game);
                            ?>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <h3 class="nbhzvn_title"><?php echo $title ? $title : "Danh Sách Game" ?></h3>
                <div class="row" id="games">
                    <?php
                        $limit = 0;
                        foreach ($repo as $tmp_game) {
                            echo echo_search_game($tmp_game, true);
                            $limit++;
                            if ($limit == 20) break;
                        }
                    ?>
                </div>
                <?php echo pagination(count($repo)) ?>
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
        <script src="/js/modal.js?v=<?=$res_version?>"></script>
        <script src="/js/main.js?v=<?=$res_version?>"></script>
        <script src="/js/toastr.js"></script>
        <script src="/js/api.js?v=<?=$res_version?>"></script>
        <?php if ($game && $game->id): ?>
        <script>gameId = <?php echo $game->id ?></script>
        <script src="/js/game.js?v=<?=$res_version?>"></script>
        <?php else: ?>
        <script>repo = "<?php echo addslashes(get("category")) ?>"</script>
        <script src="/js/game_list.js?v=<?=$res_version?>"></script>
        <?php endif ?>
        <?php if ($comments && count($comments)): ?>
        <script>
            let commentData = JSON.parse(Base64.decode(`<?= base64_encode(json_encode(array_merge($comments, $highlighted_comment->fetch_replies()))) ?>`));
        </script>
        <?php endif ?>

    </body>

    </html>