<?php
$total = get_total();
?>
<div class="footer text-center text-lg-start">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <h5 class="text-primary fw-bold"><img src="/img/logo.png" style="width: 48px; height: 48px" /></h5>
                <p class="text-muted mt-3">
                    <b>Thống kê website hiện tại:</b><br>
                    <i class="bi bi-controller"></i>&nbsp; <?php echo number_format(intval($total->total_games), 0, ",", ".") ?> <span style="color: gray">•</span> <i class="bi bi-eye"></i>&nbsp; <?php echo number_format(intval($total->total_views), 0, ",", ".") ?> <span style="color: gray">•</span> <i class="bi bi-download"></i>&nbsp; <?php echo number_format(intval($total->total_downloads), 0, ",", ".") ?>
                </p>
            </div>
            <div class="col-lg-2 col-6 mb-4 mb-lg-0">
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="/" class="text-muted text-decoration-none">Trang Chủ</a></li>
                    <li class="mb-2"><a href="/games" class="text-muted text-decoration-none">Danh Sách Game</a></li>
                    <li class="mb-2"><a href="/faq" class="text-muted text-decoration-none">FAQ</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-6 mb-4 mb-lg-0">
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="/terms" class="text-muted text-decoration-none">Điều Khoản Sử Dụng</a></li>
                    <li class="mb-2"><a href="/privacy" class="text-muted text-decoration-none">Chính Sách Bảo Mật</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h6 class="text-white text-uppercase mb-3">Cộng Đồng Nobihaza Việt Nam</h6>
                <div>
                    <a target="_blank" href="https://www.facebook.com/groups/nobihazavietnam" class="btn btn-outline-secondary btn-sm me-2 rounded-circle" style="width: 35px; height: 35px;"><i class="bi bi-facebook"></i></a>
                    <a target="_blank" href="https://discord.gg/QpMuX3gQ5u" class="btn btn-outline-secondary btn-sm me-2 rounded-circle" style="width: 35px; height: 35px;"><i class="bi bi-discord"></i></a>
                </div>
            </div>
        </div>
        <hr class="border-secondary mt-4 mb-3">
        <div class="text-center text-muted" style="font-size: 0.9rem;">
            Developed in 2026 by <a href="https://s1432.org" target="_blank">Serena1432</a> / Designed with ❤︎ by KnzkSer0817
        </div>
    </div>
</div>
