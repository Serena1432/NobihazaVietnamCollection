<?php
require "api/functions.php";
require "api/users/cookies.php";
require "api/games/functions.php";
$parsedown = new Parsedown();
$parsedown->setSafeMode(true);
$parsedown->setMarkupEscaped(true);
$parsedown->setBreaksEnabled(true);
use Soundasleep\Html2Text;

$featured_games = featured_games();
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
        <div id="heroCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <?php
                    for ($i = 0; $i < count($featured_games); $i++)
                        echo '<button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="' . $i . '"' . (($i == 0) ? ' class="active"' : '') . '></button>';
                ?>
            </div>
            <div class="carousel-inner">
                <?php
                    $i = 0;
                    foreach ($featured_games as $tmp_game) {
                        echo '
                            <div class="carousel-item' . (($i == 0) ? ' active' : '') . '">
                                <a href="/games/' . $tmp_game->id . '">
                                    <img src="/uploads/' . $tmp_game->image . '" class="d-block w-100" alt="' . htmlentities($tmp_game->name) . '">
                                    <div class="carousel-caption">
                                        <h3 class="fw-bold text-primary">' . htmlentities($tmp_game->name) . '</h3>
                                        <p>' . explode("\n", Html2Text::convert($parsedown->text($tmp_game->description)))[0] . '</p>
                                    </div>
                                </a>
                            </div>
                        ';
                        $i++;
                    }
                ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
        <div class="container-fluid content-wrapper">
            <div class="row">
                <div class="col-lg-8">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold border-start border-primary border-4 ps-2 mb-0">Game Đề Xuất</h4>
                        <div>
                            <button id="btn-grid-view" class="btn btn-link text-primary p-1"><i class="bi bi-grid-fill fs-5"></i></button>
                            <button id="btn-list-view" class="btn btn-link text-muted p-1"><i class="bi bi-list-task fs-5"></i></button>
                        </div>
                    </div>
                    <div id="game-list-container">
                        <!-- Trending -->
                        <div class="row g-3 mb-5">
                            <?php
                                foreach (trending_games(6) as $tmp_game) echo echo_homepage_game($tmp_game);
                            ?>
                        </div>
                        <!-- Recently Updated -->
                        <?php $recently_updated_games = recently_updated_games(6) ?>
                        <?php if (count($recently_updated_games) > 0): ?>
                        <h4 class="fw-bold border-start border-primary border-4 ps-2 mb-3 mt-4">Mới Cập Nhật Gần Đây</h4>
                        <div class="row g-3 mb-5">
                            <?php foreach ($recently_updated_games as $tmp_game) echo echo_homepage_game($tmp_game); ?>
                        </div>
                        <?php endif ?>
                        <!-- Recent Games -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="fw-bold border-start border-primary border-4 ps-2 mb-0">Game Mới Tải Lên</h4>
                            <div>
                                <a href="/games?category=recent"><button class="btn btn-link text-white p-1"><i class="bi bi-three-dots fs-5"></i></button></a>
                            </div>
                        </div>
                        <div class="row g-3 mb-5">
                            <?php foreach (recent_games(6) as $tmp_game) echo echo_homepage_game($tmp_game); ?>
                        </div>
                        <!-- Popular -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="fw-bold border-start border-primary border-4 ps-2 mb-0">Game Phổ Biến</h4>
                            <div>
                                <a href="/games?category=popular"><button class="btn btn-link text-white p-1"><i class="bi bi-three-dots fs-5"></i></button></a>
                            </div>
                        </div>
                        <div class="row g-3 mb-5">
                            <?php foreach (popular_games(6) as $tmp_game) echo echo_homepage_game($tmp_game); ?>
                        </div>
                        <!-- Mobile -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="fw-bold border-start border-primary border-4 ps-2 mb-0">Game Dành Cho Điện Thoại</h4>
                            <div>
                                <a href="/games?category=mobile"><button class="btn btn-link text-white p-1"><i class="bi bi-three-dots fs-5"></i></button></a>
                            </div>
                        </div>
                        <div class="row g-3 mb-5">
                            <?php foreach (mobile_games(6) as $tmp_game) echo echo_homepage_game($tmp_game); ?>
                        </div>
                    </div> 
                </div> 
                <div class="col-lg-4">
                    <div class="card-dark mb-4 p-3">
                        <h5 class="text-primary fw-bold mb-3"><i class="bi bi-journal-text"></i> Tin tức & Cập nhật</h5>
                        <div class="mb-3">
                            <h6 class="fw-bold mb-1"><a href="#" class="text-white">Lorem Ipsum</a></h6>
                            <div class="text-muted small mb-2"><i class="bi bi-clock"></i> 2 giờ trước bởi Admin</div>
                            <p class="text-muted small mb-0">Lorem Ipsum</p>
                        </div>
                        <hr class="border-secondary">
                        <div class="mb-2">
                            <h6 class="fw-bold mb-1"><a href="#" class="text-white">Lorem Ipsum</a></h6>
                            <div class="text-muted small mb-2"><i class="bi bi-clock"></i> Hôm qua bởi Admin</div>
                            <p class="text-muted small mb-0">Lorem Ipsum</p>
                        </div>
                        <a href="#" class="btn btn-outline-primary btn-sm mt-3 w-100">Xem tất cả</a>
                    </div>
                    <div class="card-dark mb-4">
                        <div class="card-header border-bottom border-dark p-0">
                            <ul class="nav nav-tabs custom-tabs nav-fill" id="rightTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active py-3" id="top-follow-tab" data-bs-toggle="tab" data-bs-target="#top-follow" type="button">Theo dõi</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-3" id="random-tab" data-bs-toggle="tab" data-bs-target="#random" type="button">Ngẫu nhiên</button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body p-3">
                            <div class="tab-content" id="rightTabsContent">
                                <div class="tab-pane fade show active" id="top-follow" role="tabpanel">
                                    <?php foreach (most_followed_games(10) as $item): ?>
                                        <?php $tmp_game = $item->data ?>
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="/uploads/<?= $tmp_game->image ?>" class="rounded me-3" width="50" height="50" style="object-fit:cover;" alt="<?= htmlentities($tmp_game->name) ?>">
                                            <div>
                                                <a href="/games/<?= $tmp_game->id ?>" class="text-white fw-bold text-decoration-none"><?= htmlentities($tmp_game->name) ?></a>
                                                <?php $tmp_game_rating = $tmp_game->ratings(); ?>
                                                <div class="text-muted small">
                                                    <?php if ($tmp_game_rating->total > 0): ?>
                                                        <i class="bi bi-star-fill text-warning"></i> <?= number_format($tmp_game_rating->average, 1, ",", ".") ?> (<?= $tmp_game_rating->total ?>) ●
                                                    <?php endif ?>
                                                <?= $item->follow_count ?> người theo dõi</div>
                                            </div>
                                        </div>
                                    <?php endforeach ?>
                                </div>
                                <div class="tab-pane fade" id="random" role="tabpanel">
                                    <?php foreach (random_games(0, 10) as $tmp_game): ?>
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="/uploads/<?= $tmp_game->image ?>" class="rounded me-3" width="50" height="50" style="object-fit:cover;" alt="<?= htmlentities($tmp_game->name) ?>">
                                            <div>
                                                <a href="/games/<?= $tmp_game->id ?>" class="text-white fw-bold text-decoration-none"><?= htmlentities($tmp_game->name) ?></a>
                                                <div class="text-muted small"><i class="bi bi-download"></i> <?= number_format($tmp_game->downloads, 0, ",", ".") ?> lượt tải</div>
                                            </div>
                                        </div>
                                    <?php endforeach ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-dark mb-4 p-3">
                        <h5 class="text-primary fw-bold mb-3"><i class="bi bi-cloud-arrow-up"></i> Top Uploader</h5>
                        <p class="text-muted fs-6"><i>Bảng xếp hạng này sẽ không bao gồm các Quản Trị Viên.</i></p>
                        <?php $i = 1 ?>
                        <?php foreach (top_uploaders() as $uploader): ?>
                        <div class="d-flex align-items-center mb-3 p-2 rounded"<?= ($i == 1) ? 'style="background-color: var(--bg-panel-hover);"' : '' ?>>
                            <img src="<?= $uploader->avatar ? ("/uploads/" . $uploader->avatar) : "/img/default_avatar.png" ?>" class="avatar-sm me-3" alt="<?= htmlentities($uploader->display_name) ?>">
                            <div class="flex-grow-1">
                                <a href="/profile/<?= $uploader->user_id ?>" class="text-white fw-bold text-decoration-none"><?= htmlentities($uploader->display_name) ?></a>
                                <div class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-controller"></i> <?= number_format($uploader->game_count, 0, ",", ".") ?> ● <i class="bi bi-eye"></i> <?= number_format($uploader->total_views, 0, ",", ".") ?> ● <i class="bi bi-download"></i> <?= number_format($uploader->total_downloads, 0, ",", ".") ?></div>
                            </div>
                            <span class="<?= ($i == 1) ? 'badge badge-role' : 'text-muted fw-bold' ?>">#<?= $i ?></span>
                        </div>
                        <?php $i++ ?>
                        <?php endforeach ?>
                    </div>
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
