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
        <div class="container-fluid content-wrapper mt-3">
            <div class="row">
                <div class="col-lg-4 col-xl-3 mb-4">
                    <div class="text-center text-lg-start">
                        <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=400&auto=format&fit=crop" class="profile-avatar-lg mb-3 mx-auto mx-lg-0 d-block" alt="Avatar">
                        <h3 class="fw-bold text-white mb-0">Phương Nam</h3>
                        <p class="text-muted fs-5 mb-2">@toraphamnam</p>
                        <div class="mb-3">
                            <span class="badge badge-role fs-6 px-3 py-2"><i class="bi bi-cloud-arrow-up"></i> Uploader</span>
                        </div>
                    </div>
                    <div class="d-none d-lg-block">
                        <button class="btn btn-outline-secondary w-100 mb-2 text-white"><i class="bi bi-pencil-square"></i> Thay đổi thông tin</button>
                        <div class="d-flex gap-2 mb-3">
                            <button class="btn btn-primary w-50"><i class="bi bi-chat-dots"></i> Nhắn tin</button>
                            <button class="btn btn-secondary w-50"><i class="bi bi-bell"></i> Thông báo</button>
                        </div>
                    </div>
                    <div class="d-lg-none d-flex flex-wrap gap-2 mb-3 justify-content-center">
                        <button class="btn btn-outline-secondary text-white"><i class="bi bi-pencil-square"></i> Thay đổi</button>
                        <button class="btn btn-primary"><i class="bi bi-chat-dots"></i> Nhắn tin</button>
                        <button class="btn btn-secondary"><i class="bi bi-bell"></i> Thông báo</button>
                    </div>
                    <div class="card-dark p-2 mb-3 border-danger">
                        <div class="text-danger small mb-1 fw-bold">Admin Panel:</div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-warning w-50">Đổi chức vụ</button>
                            <button class="btn btn-sm btn-outline-danger w-50"><i class="bi bi-ban"></i> Cấm</button>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-2 text-muted mb-4 border-bottom border-dark pb-4">
                        <div><i class="bi bi-envelope text-white me-2"></i> toraphamnam@gmail.com</div>
                        <div><i class="bi bi-discord text-info me-2"></i> serena1432</div>
                        <div><i class="bi bi-geo-alt text-danger me-2"></i> Việt Nam</div>
                    </div>
                    <h6 class="fw-bold text-white mb-3">Thống kê hoạt động</h6>
                    <ul class="list-group list-group-flush bg-transparent">
                        <li class="list-group-item bg-transparent text-white border-dark px-0 d-flex justify-content-between align-items-center">
                            Game đã tải lên <span class="badge bg-primary rounded-pill">12</span>
                        </li>
                        <li class="list-group-item bg-transparent text-white border-dark px-0 d-flex justify-content-between align-items-center">
                            Tổng lượt tải xuống <span class="badge bg-primary rounded-pill">45.2k</span>
                        </li>
                        <li class="list-group-item bg-transparent text-white border-dark px-0 d-flex justify-content-between align-items-center">
                            Tổng lượt xem <span class="badge bg-primary rounded-pill">120k</span>
                        </li>
                        <li class="list-group-item bg-transparent text-white border-dark px-0 d-flex justify-content-between align-items-center">
                            Bình luận đã gửi <span class="badge bg-secondary rounded-pill">34</span>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-8 col-xl-9">
                    <div class="card-dark p-4 mb-4">
                        <h5 class="fw-bold border-start border-primary border-4 ps-2 mb-3">Giới thiệu</h5>
                        <p class="text-light" style="line-height: 1.6;">
                           Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                           Cras enim tortor, tristique interdum enim sit amet, ultrices euismod felis. 
                           Curabitur efficitur, urna eu tempus lacinia, eros tortor placerat enim, ut vehicula odio risus auctor nulla. 
                           Proin a nibh suscipit, iaculis risus eget, venenatis tellus
                            <br><br>
                             Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                        </p>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center border-bottom border-dark pb-2 mb-3">
                            <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-star"></i> Game Phổ Biến của ......</h5>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card-dark p-3 h-100 border border-primary">
                                    <div class="d-flex align-items-center mb-2">
                                        <a href="game-info.php" class="text-white fw-bold fs-5 text-decoration-none">Lorem ipsum</a>
                                        <span class="badge border border-secondary text-muted ms-auto">RPG</span>
                                    </div>
                                    <p class="text-muted small mb-3">Lorem ipsum</p>
                                    <div class="d-flex justify-content-between text-muted small mt-auto">
                                        <span><i class="bi bi-download"></i> 15.2k</span>
                                        <span><i class="bi bi-star-fill text-warning"></i> 4.8</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card-dark p-3 h-100">
                                    <div class="d-flex align-items-center mb-2">
                                        <a href="game-info.php" class="text-white fw-bold fs-5 text-decoration-none">Lorem ipsum</a>
                                        <span class="badge border border-secondary text-muted ms-auto">Hành động</span>
                                    </div>
                                    <p class="text-muted small mb-3">Lorem ipsum</p>
                                    <div class="d-flex justify-content-between text-muted small mt-auto">
                                        <span><i class="bi bi-download"></i> 8.4k</span>
                                        <span><i class="bi bi-star-fill text-warning"></i> 4.5</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center border-bottom border-dark pb-2 mb-3">
                            <h5 class="fw-bold mb-0"><i class="bi bi-collection"></i> Game đã tải lên (12)</h5>
                            <a href="#" class="text-primary small text-decoration-none">Xem tất cả</a>
                        </div>
                        <div class="list-group list-group-flush">
                            <a href="game-info.php" class="list-group-item bg-transparent text-white border-dark p-3 d-flex align-items-center">
                                <i class="bi bi-joystick text-primary fs-3 me-3"></i>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">Lorem ipsum</h6>
                                    <small class="text-muted">Cập nhật 2 ngày trước</small>
                                </div>
                                <span class="badge bg-secondary">Mobile</span>
                            </a>
                            <a href="game-info.php" class="list-group-item bg-transparent text-white border-dark p-3 d-flex align-items-center">
                                <i class="bi bi-joystick text-primary fs-3 me-3"></i>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">Lorem ipsum</h6>
                                    <small class="text-muted">Cập nhật 1 tháng trước</small>
                                </div>
                                <span class="badge bg-secondary">PC</span>
                            </a>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h5 class="fw-bold border-bottom border-dark pb-2 mb-3"><i class="bi bi-activity"></i> Hoạt động gần đây</h5>
                        <div class="card-dark p-0 border-0">
                            <div class="d-flex mb-3">
                                <div class="me-3 mt-1">
                                    <i class="bi bi-chat-left-text-fill text-primary"></i>
                                </div>
                                <div>
                                    <div class="text-white">Đã bình luận trong <a href="game-info.php" class="fw-bold">Lorem ipsum</a></div>
                                    <div class="text-muted small mt-1 bg-dark p-2 rounded border border-secondary">"Lorem ipsum"</div>
                                    <small class="text-muted">3 ngày trước</small>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="me-3 mt-1">
                                    <i class="bi bi-cloud-upload-fill text-success"></i>
                                </div>
                                <div>
                                    <div class="text-white">Đã tải lên game mới <a href="game-info.php" class="fw-bold text-success">Lorem ipsum</a></div>
                                    <small class="text-muted">2 ngày trước</small>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="me-3 mt-1">
                                    <i class="bi bi-star-fill text-warning"></i>
                                </div>
                                <div>
                                    <div class="text-white">Đã đánh giá 5 sao cho <a href="game-info.php" class="fw-bold text-warning">Lorem ipsum</a></div>
                                    <small class="text-muted">1 tuần trước</small>
                                </div>
                            </div>
                        </div>
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
