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
<html lang="zxx">

<head>
    <?php
        require("head.php");
    ?>
</head>

<body>
    <!-- Header Section Begin -->
    <header class="header">
        <?php require "header.php"; ?>
    </header>
    <!-- Header End -->

    <?php if (count($featured_games)): ?>
    <!-- Hero Section Begin -->
    <section class="hero">
        <div class="container">
            <div class="hero__slider owl-carousel">
                <?php
                    foreach ($featured_games as $tmp_game) {
                        echo '
                            <div class="hero__items set-bg" data-setbg="/uploads/' . $tmp_game->image . '" style="height: 100%">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="hero__text">
                                            <div class="label">' . htmlentities($engine_vocab[$tmp_game->engine]) . '</div>
                                            <h2>' . htmlentities($tmp_game->name) . '</h2>
                                            <p>' . explode("\n", Html2Text::convert($parsedown->text($tmp_game->description)))[0] . '</p>
                                            <a href="/games/' . $tmp_game->id . '"><span>Xem thông tin game</span> <i class="fa fa-angle-right"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ';
                    }
                ?>
            </div>
        </div>
    </section>
    <!-- Hero Section End -->
    <?php endif ?>

    <!-- Product Section Begin -->
    <section class="product spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="trending__product">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="section-title">
                                    <h4>Được Đề Xuất</h4>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <?php
                                foreach (trending_games(6) as $tmp_game) echo echo_homepage_game($tmp_game);
                            ?>
                        </div>
                    </div>
                    <?php $recently_updated_games = recently_updated_games(6) ?>
                    <?php if (count($recently_updated_games) > 0): ?>
                    <div class="popular__product">
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-8">
                                <div class="section-title">
                                    <h4>Mới Cập Nhật Gần Đây</h4>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="btn__all">
                                    <a href="/games?category=recently_updated" class="primary-btn">Xem Tất Cả <span class="arrow_right"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <?php
                                $recently_updated_games = recently_updated_games(6);
                                foreach ($recently_updated_games as $tmp_game) echo echo_homepage_game($tmp_game);
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="popular__product">
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-8">
                                <div class="section-title">
                                    <h4>Game Phổ Biến</h4>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="btn__all">
                                    <a href="/games?category=popular" class="primary-btn">Xem Tất Cả <span class="arrow_right"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <?php
                                foreach (popular_games(6) as $tmp_game) echo echo_homepage_game($tmp_game);
                            ?>
                        </div>
                    </div>
                    <div class="recent__product">
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-8">
                                <div class="section-title">
                                    <h4>Game Mới Tải Lên</h4>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="btn__all">
                                    <a href="/games?category=recent" class="primary-btn">Xem Tất Cả <span class="arrow_right"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <?php
                                foreach (recent_games(6) as $tmp_game) echo echo_homepage_game($tmp_game);
                            ?>
                        </div>
                    </div>
                    <div class="live__product">
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-8">
                                <div class="section-title">
                                    <h4>Game Dành Cho Điện Thoại</h4>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="btn__all">
                                    <a href="/games?category=mobile" class="primary-btn">Xem Tất Cả <span class="arrow_right"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <?php
                                foreach (mobile_games(6) as $tmp_game) echo echo_homepage_game($tmp_game);
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="product__sidebar">
                        <div class="product__sidebar__view">
                            <div class="section-title">
                                <h5>Top Theo Dõi</h5>
                            </div>
                            <?php
                                foreach (most_followed_games(6) as $tmp_game_info) {
                                    $tmp_game = $tmp_game_info->data; $follows = $tmp_game_info->follow_count;
                                    echo '
                                    <div class="product__sidebar__view__item set-bg mix month week"
                                    data-setbg="/uploads/' . $tmp_game->image . '">
                                        <div class="ep">' . $engine_vocab[$tmp_game->engine] . '</div>
                                        <div class="view"><i class="fa fa-heart"></i> ' . number_format($follows, 0, ",", ".") . '</div>
                                        <h5><a href="/games/' . $tmp_game->id . '">' . htmlentities($tmp_game->name) . '</a></h5>
                                    </div>
                                    ';
                                }
                            ?>
        </div>
    </div>
    <div class="product__sidebar__comment">
        <div class="section-title">
            <h5>Game Ngẫu Nhiên</h5>
        </div>
        <?php
            foreach (random_games(0, 10) as $tmp_game) echo echo_search_game($tmp_game);
        ?>
    </div>
</div>
</div>
</div>
</div>
</section>
<!-- Product Section End -->

<!-- Footer Section Begin -->
<footer class="footer">
    <?php require "footer.php"; ?>
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


</body>

</html>