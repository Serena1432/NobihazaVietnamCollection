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
<html lang="vi">
<head>
    <?php require "head.php" ?>
</head>
<body>
<div class="app-container">
    <?php require 'sidebar.php'; ?>
    <div class="main-content">
        <?php require "mobile-top-nav.php" ?>
        <?php require "game-info.php" ?>
        <?php require 'footer.php'; ?>
    </div>
</div>
<script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/js/jquery-3.3.1.min.js"></script>
<script src="/js/base64.min.js"></script>
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
    let commentData = JSON.parse(Base64.decode(`<?= base64_encode(json_encode($highlighted_comment ? array_merge($comments, $highlighted_comment->fetch_replies()) : $comments)) ?>`));
</script>
<?php endif ?>
<script src="/js/fslightbox.js"></script>
</body>
</html>
