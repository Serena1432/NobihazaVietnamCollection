<?php
require "api/functions.php";
require "api/users/cookies.php";
require "api/games/functions.php";
$parsedown = new Parsedown();
$parsedown->setSafeMode(true);
$parsedown->setMarkupEscaped(true);
?>
<!DOCTYPE html>
<html lang="zxx">

<head>
    <?php
        $title = "FAQ";
        require "head.php"
    ?>
</head>

<body>
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

    <!-- Header Section Begin -->
    <header class="header">
        <?php require "header.php" ?>
    </header>
    <!-- Header End -->

    <!-- Blog Details Section Begin -->
    <section class="blog-details spad">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-lg-8">
                    <div class="blog__details__content">
                        <div class="blog__details__text faq">
                            <?php
                                try {
                                    $file = fopen(__DIR__ . "/FAQ.md", "r");
                                    if (!$file) echo '<p>Không thể mở được tệp tin FAQ.md. Vui lòng kiểm tra lại tệp tin đó ở thư mục gốc của website.</p>';
                                    else {
                                        $markdown = fread($file, filesize(__DIR__ . "/FAQ.md"));
                                        echo $parsedown->text($markdown);
                                        fclose($file);
                                    }
                                }
                                catch (Exception $ex) {
                                    echo '<p>Không thể mở được tệp tin FAQ.md. Vui lòng kiểm tra lại tệp tin đó ở thư mục gốc của website.</p><p>' . $ex->getMessage() . '</p>';
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Blog Details Section End -->

        <!-- Footer Section Begin -->
        <footer class="footer">
            <?php require "footer.php" ?>
          </footer>
          <!-- Footer Section End -->

        <!-- Js Plugins -->
        <script src="/js/jquery-3.3.1.min.js"></script>
        <script src="/js/bootstrap.min.js"></script>
        <script src="/js/mixitup.min.js"></script>
        <script src="/js/jquery.slicknav.js"></script>
        <script src="/js/owl.carousel.min.js"></script>
        <script src="/js/main.js"></script>

    </body>

    </html>