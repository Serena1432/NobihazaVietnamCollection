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
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom border-dark pb-3 mb-4">
                <h3 class="fw-bold text-white mb-3 mb-md-0"><i class="bi bi-collection text-primary me-2"></i> Khám Phá Game</h3>
                <div class="d-flex align-items-center gap-2">
                    <select class="form-select bg-dark text-white border-secondary w-auto">
                        <option value="new">Mới cập nhật</option>
                        <option value="popular">Tải nhiều nhất</option>
                        <option value="rating">Đánh giá cao nhất</option>
                    </select>
                    <div class="bg-dark rounded border border-secondary px-2 py-1">
                        <button id="btn-grid-view" class="btn btn-link text-primary p-1"><i class="bi bi-grid-fill fs-5"></i></button>
                        <button id="btn-list-view" class="btn btn-link text-muted p-1"><i class="bi bi-list-task fs-5"></i></button>
                    </div>
                </div>
            </div>
            <div id="game-list-container">
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-md-4 col-xl-3">
                        <a href="game-info.php" class="text-decoration-none">
                            <div class="game-card">
                                <img src="https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=500&auto=format&fit=crop" class="game-card-img" alt="Game">
                                <div class="game-card-body">
                                    <h5 class="game-card-title text-white">Lorem Ipsum</h5>
                                    <p class="text-muted small mb-2 d-none d-md-block">Lorem Ipsum</p>
                                    <div class="game-card-meta mt-2">
                                        <span><i class="bi bi-eye"></i> 1.2k</span>
                                        <span class="text-primary"><i class="bi bi-download"></i> 340</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4 col-xl-3">
                        <a href="game-info.php" class="text-decoration-none">
                            <div class="game-card">
                                <img src="https://images.unsplash.com/photo-1538481199705-c710c4e965fc?w=500&auto=format&fit=crop" class="game-card-img" alt="Game">
                                <div class="game-card-body">
                                    <h5 class="game-card-title text-white">Lorem Ipsum</h5>
                                    <p class="text-muted small mb-2 d-none d-md-block">Lorem Ipsum</p>
                                    <div class="game-card-meta mt-2">
                                        <span><i class="bi bi-eye"></i> 5.8k</span>
                                        <span class="text-primary"><i class="bi bi-download"></i> 2.1k</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4 col-xl-3">
                        <a href="game-info.php" class="text-decoration-none">
                            <div class="game-card">
                                <img src="https://images.unsplash.com/photo-1542751371-adc38448a05e?w=500&auto=format&fit=crop" class="game-card-img" alt="Game">
                                <div class="game-card-body">
                                    <h5 class="game-card-title text-white">Lorem Ipsum</h5>
                                    <p class="text-muted small mb-2 d-none d-md-block">Lorem Ipsum</p>
                                    <div class="game-card-meta mt-2">
                                        <span><i class="bi bi-eye"></i> 950</span>
                                        <span class="text-primary"><i class="bi bi-download"></i> 120</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4 col-xl-3">
                        <a href="game-info.php" class="text-decoration-none">
                            <div class="game-card">
                                <img src="https://images.unsplash.com/photo-1511512578047-dfb367046420?w=500&auto=format&fit=crop" class="game-card-img" alt="Game">
                                <div class="game-card-body">
                                    <h5 class="game-card-title text-white">Lorem Ipsum</h5>
                                    <p class="text-muted small mb-2 d-none d-md-block">Lorem Ipsum</p>
                                    <div class="game-card-meta mt-2">
                                        <span><i class="bi bi-eye"></i> 8.4k</span>
                                        <span class="text-primary"><i class="bi bi-download"></i> 4.1k</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4 col-xl-3">
                        <a href="game-info.php" class="text-decoration-none">
                            <div class="game-card">
                                <img src="https://images.unsplash.com/photo-1493711662062-fa541adb3fc8?w=500&auto=format&fit=crop" class="game-card-img" alt="Game">
                                <div class="game-card-body">
                                    <h5 class="game-card-title text-white">Lorem Ipsum</h5>
                                    <p class="text-muted small mb-2 d-none d-md-block">Lorem Ipsum</p>
                                    <div class="game-card-meta mt-2">
                                        <span><i class="bi bi-eye"></i> 10.2k</span>
                                        <span class="text-primary"><i class="bi bi-download"></i> 5.4k</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <nav aria-label="Page navigation" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link bg-dark border-secondary text-muted" href="#" tabindex="-1" aria-disabled="true">Trang trước</a>
                        </li>
                        <li class="page-item active"><a class="page-link bg-primary border-primary text-white" href="#">1</a></li>
                        <li class="page-item"><a class="page-link bg-dark border-secondary text-white" href="#">2</a></li>
                        <li class="page-item"><a class="page-link bg-dark border-secondary text-white" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link bg-dark border-secondary text-white" href="#">Trang sau</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <?php require 'footer.php'; ?>
    </div>
</div>
<script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/js/main.js"></script>
</body>
</html>
