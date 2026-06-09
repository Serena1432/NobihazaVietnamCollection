
<div class="sidebar-container d-none d-lg-flex">
    <a class="sidebar-brand" href="/">
        <img class="header-logo" src="/img/logo2.png" />
    </a>
    <div class="px-3 mb-3">
        <div class="input-group">
            <input type="text" class="form-control bg-dark text-white border-dark" placeholder="Tìm kiếm...">
            <button class="btn btn-primary" type="button"><i class="bi bi-search"></i></button>
        </div>
    </div>
    <ul class="sidebar-menu">
        <li class="has-submenu">
            <a href="#"><i class="bi bi-collection"></i> Danh Sách Game <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem;"></i></a>
            <ul class="sidebar-submenu">
                <li><a href="game-list.html"><i class="bi bi-star"></i> Game Phổ Biến</a></li>
                <li><a href="game-list.html"><i class="bi bi-phone"></i> Game Dành Cho Điện Thoại</a></li>
                <li><a href="game-list.html"><i class="bi bi-translate"></i> Game Tiếng Việt</a></li>
                <li><a href="game-list.html"><i class="bi bi-disc"></i> RPG Maker 2000/2003</a></li>
                <li><a href="game-list.html"><i class="bi bi-disc"></i> RPG Maker XP/VX/VX Ace</a></li>
                <li><a href="game-list.html"><i class="bi bi-disc"></i> RPG Maker MV/MZ</a></li>
            </ul>
        </li>
        <li><a href="#"><i class="bi bi-book"></i> Hướng Dẫn Chơi</a></li>
        <li class="has-submenu">
            <a href="#"><i class="bi bi-box"></i> Phần Mềm <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem;"></i></a>
            <ul class="sidebar-submenu">
                <li><a href="#"><i class="bi bi-download"></i> EasyRPG</a></li>
                <li><a href="#"><i class="bi bi-download"></i> JoiPlay</a></li>
                <li><a href="#"><i class="bi bi-download"></i> MKXP</a></li>
            </ul>
        </li>
        <li><a href="#"><i class="bi bi-journal-text"></i> Blog</a></li>
        <li class="has-submenu">
            <a href="#"><i class="bi bi-link-45deg"></i> Liên Kết Nhóm <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem;"></i></a>
            <ul class="sidebar-submenu">
                <li><a href="#"><i class="bi bi-facebook"></i> Facebook</a></li>
                <li><a href="#"><i class="bi bi-discord"></i> Discord</a></li>
            </ul>
        </li>
    </ul>
    <div class="px-3 mt-auto mb-2">
        <?php if ($user && $user->type >= 2): ?>
        <a href="/upload"><button class="btn btn-primary w-100 mb-3"><i class="bi bi-cloud-upload"></i>&nbsp; Tải Game Lên</button></a>
        <?php endif ?>
        <div class="d-flex justify-content-between mb-3 text-muted">
            <?php if ($user): ?>
            <a href="#" class="text-muted fs-5"><i class="bi bi-chat-dots"></i></a>
            <a href="#" class="text-muted fs-5"><i class="bi bi-bell"></i></a>
            <?php endif ?>
            <a href="#" class="text-muted fs-5"><i class="bi bi-globe"></i></a>
        </div>
    </div>
    <div class="sidebar-footer dropup">
        <?php if ($user && $user->id): ?>
        <div class="user-profile-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="<?= $user->avatar_url ?>" alt="Avatar">
            <div>
                <div class="text-white fw-bold" style="font-size: 0.9rem;"><?= $user->display_name ?? $user->username ?></div>
                <div class="text-muted" style="font-size: 0.75rem;">@<?= $user->username ?></div>
            </div>
        </div>  
        <ul class="dropdown-menu dropdown-menu-dark w-100">
            <li><a class="dropdown-item" href="/profile"><i class="bi bi-person me-2"></i> Hồ sơ</a></li>
            <li><a class="dropdown-item" href="/change_info"><i class="bi bi-gear me-2"></i> Thay đổi thông tin</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="/logout"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</a></li>
        </ul>
        <?php else: ?>
        <p><a href="/login"><button class="btn btn-primary w-100 mb-3"><i class="bi bi-box-arrow-in-right"></i>&nbsp; Đăng Nhập</button></a></p>
        <p class="text-center"><a href="/register">Đăng Ký</p>
        <?php endif ?>
    </div>
</div>
<div class="bottom-navbar d-lg-none">
    <a href="index.html" class="bottom-nav-item active">
        <i class="bi bi-house-door-fill"></i>
        <span>Trang chủ</span>
    </a>
    <a href="game-list.html" class="bottom-nav-item">
        <i class="bi bi-search"></i>
        <span>Khám phá</span>
    </a>
    <a href="#" class="bottom-nav-item">
        <i class="bi bi-chat-dots-fill"></i>
        <span>Tin nhắn</span>
    </a>
    <a href="user-profile.html" class="bottom-nav-item">
        <i class="bi bi-person-fill"></i>
        <span>Cá nhân</span>
    </a>
    <a class="bottom-nav-item" data-bs-toggle="offcanvas" href="#mobileMenu" role="button">
        <i class="bi bi-list"></i>
        <span>Menu</span>
    </a>
</div>
<div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="mobileMenu">
  <div class="offcanvas-header border-bottom border-dark">
    <h5 class="offcanvas-title text-primary"><i class="bi bi-controller"></i> GamePortal</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <ul class="nav flex-column mb-auto">
      <li class="nav-item mb-2"><a href="#" class="nav-link text-white"><i class="bi bi-cloud-upload text-primary me-2"></i> Tải Game Lên</a></li>
      <li class="nav-item mb-2"><a href="#" class="nav-link text-white"><i class="bi bi-bell text-primary me-2"></i> Thông báo</a></li>
      <li class="nav-item mb-2"><a href="#" class="nav-link text-white"><i class="bi bi-globe text-primary me-2"></i> Đổi ngôn ngữ</a></li>
      <hr>
      <li class="nav-item mb-2"><a href="game-list.html" class="nav-link text-white"><i class="bi bi-collection me-2"></i> Danh Sách Game</a></li>
      <li class="nav-item mb-2"><a href="#" class="nav-link text-white"><i class="bi bi-book me-2"></i> Hướng Dẫn Chơi</a></li>
      <li class="nav-item mb-2"><a href="#" class="nav-link text-white"><i class="bi bi-box me-2"></i> Phần Mềm</a></li>
    </ul>
    <hr>
    <a href="login.html" class="btn btn-outline-danger w-100">Đăng xuất</a>
  </div>
</div>
