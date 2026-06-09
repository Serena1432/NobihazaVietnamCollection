<?php
require "api/functions.php";
require "api/users/functions.php";
require "api/users/cookies.php";

if (!$user) redirect_to_home();

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php
        $title = "Tin Nhắn";
        require __DIR__ . "/head.php";
    ?>
    <style>
        /* Tùy chỉnh thanh cuộn cho khu vực chat */
        .chat-scrollarea::-webkit-scrollbar {
            width: 6px;
        }
        .chat-scrollarea::-webkit-scrollbar-track {
            background: transparent;
        }
        .chat-scrollarea::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }
        .chat-scrollarea::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.4);
        }
        
        .chat-list-item:hover {
            background-color: var(--bg-panel-hover) !important;
        }
    </style>
</head>
<body>
<div class="app-container">
    <?php require 'sidebar.php'; ?>
    <div class="main-content">
        <?php require "mobile-top-nav.php" ?>
        
        <div class="container-fluid content-wrapper mt-4">
            <div class="row gx-3 h-100" style="min-height: 75vh;">
                <!-- Cột trái: Danh sách cuộc trò chuyện -->
                <div class="col-md-4 mb-4">
                    <div class="card-dark p-3 shadow-lg h-100 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-secondary pb-2">
                            <h5 class="fw-bold mb-0">Tin Nhắn</h5>
                            <button class="btn btn-sm btn-outline-light rounded-circle" title="Tạo tin nhắn mới"><i class="bi bi-pencil-square"></i></button>
                        </div>
                        
                        <div class="mb-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control bg-dark border-secondary text-white" placeholder="Tìm kiếm người dùng...">
                            </div>
                        </div>

                        <div class="chat-list flex-grow-1 overflow-auto chat-scrollarea" style="max-height: 60vh;">
                            <!-- Người dùng 1 (Đang chọn) -->
                            <div class="d-flex align-items-center p-2 rounded mb-2 bg-secondary bg-opacity-25 chat-list-item" style="cursor: pointer;">
                                <img src="/img/default_avatar.png" class="rounded-circle me-3 shadow-sm" width="45" height="45" style="object-fit: cover; border: 1px solid #333;">
                                <div class="overflow-hidden w-100">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 text-white fw-bold">Admin Nobihaza</h6>
                                        <small class="text-muted" style="font-size: 0.75rem;">12:45</small>
                                    </div>
                                    <small class="text-muted text-truncate d-block">Ok bạn. Chúc bạn chơi game vui vẻ nha.</small>
                                </div>
                            </div>
                            
                            <!-- Người dùng 2 -->
                            <div class="d-flex align-items-center p-2 rounded mb-2 chat-list-item" style="cursor: pointer;">
                                <img src="/img/default_avatar.png" class="rounded-circle me-3 shadow-sm" width="45" height="45" style="object-fit: cover; border: 1px solid #333;">
                                <div class="overflow-hidden w-100">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 text-white fw-bold">Suneo Pro</h6>
                                        <small class="text-muted" style="font-size: 0.75rem;">Hôm qua</small>
                                    </div>
                                    <small class="text-muted text-truncate d-block fw-bold text-white">Bạn hướng dẫn mình qua đoạn này với?</small>
                                </div>
                                <span class="badge bg-danger rounded-pill ms-2">1</span>
                            </div>
                            
                            <!-- Người dùng 3 -->
                            <div class="d-flex align-items-center p-2 rounded mb-2 chat-list-item" style="cursor: pointer;">
                                <img src="/img/default_avatar.png" class="rounded-circle me-3 shadow-sm" width="45" height="45" style="object-fit: cover; border: 1px solid #333;">
                                <div class="overflow-hidden w-100">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 text-white fw-bold">Shizuka_chan</h6>
                                        <small class="text-muted" style="font-size: 0.75rem;">12/05</small>
                                    </div>
                                    <small class="text-muted text-truncate d-block">Bạn tải game về thử xem nha!</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cột phải: Nội dung cuộc trò chuyện -->
                <div class="col-md-8 mb-4">
                    <div class="card-dark p-0 shadow-lg h-100 d-flex flex-column border border-secondary" style="overflow: hidden;">
                        <!-- Header -->
                        <div class="p-3 border-bottom border-secondary d-flex align-items-center bg-dark bg-opacity-50">
                            <img src="/img/default_avatar.png" class="rounded-circle me-3 shadow-sm" width="40" height="40" style="object-fit: cover; border: 1px solid #333;">
                            <div>
                                <h6 class="mb-0 text-white fw-bold">Admin Nobihaza</h6>
                                <small class="text-success"><i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Đang hoạt động</small>
                            </div>
                            <div class="ms-auto">
                                <button class="btn btn-sm btn-outline-secondary rounded-circle"><i class="bi bi-three-dots-vertical"></i></button>
                            </div>
                        </div>
                        
                        <!-- Lịch sử tin nhắn -->
                        <div class="p-4 flex-grow-1 overflow-auto d-flex flex-column chat-scrollarea" style="max-height: 55vh; background: linear-gradient(180deg, var(--bg-panel) 0%, rgba(30,30,30,0.5) 100%);">
                            
                            <div class="text-center mb-4">
                                <small class="text-muted bg-dark px-3 py-1 rounded-pill border border-secondary">Hôm nay, 11:30</small>
                            </div>

                            <!-- Tin nhắn của người kia (Trái) -->
                            <div class="d-flex mb-3 justify-content-start align-items-end">
                                <img src="/img/default_avatar.png" class="rounded-circle me-2 shadow-sm" width="30" height="30" style="object-fit: cover; border: 1px solid #555;">
                                <div class="bg-secondary bg-opacity-25 text-white p-2 px-3 shadow-sm" style="max-width: 75%; border-radius: 18px 18px 18px 4px;">
                                    Chào bạn, bạn đã cài đặt game thành công chưa? Có gặp lỗi gì trong quá trình giải nén không?
                                </div>
                            </div>
                            
                            <!-- Tin nhắn của mình (Phải) -->
                            <div class="d-flex mb-3 justify-content-end align-items-end">
                                <div class="bg-primary text-white p-2 px-3 shadow-sm" style="max-width: 75%; border-radius: 18px 18px 4px 18px;">
                                    Mình đã cài đặt thành công rồi nhé. Không gặp lỗi gì cả, game chạy rất mượt. Cảm ơn Admin!
                                </div>
                            </div>

                            <div class="text-center mb-4 mt-2">
                                <small class="text-muted bg-dark px-3 py-1 rounded-pill border border-secondary">12:45</small>
                            </div>

                            <!-- Tin nhắn của người kia (Trái) -->
                            <div class="d-flex mb-3 justify-content-start align-items-end">
                                <img src="/img/default_avatar.png" class="rounded-circle me-2 shadow-sm" width="30" height="30" style="object-fit: cover; border: 1px solid #555;">
                                <div class="bg-secondary bg-opacity-25 text-white p-2 px-3 shadow-sm" style="max-width: 75%; border-radius: 18px 18px 18px 4px;">
                                    Ok bạn. Chúc bạn chơi game vui vẻ nha.
                                </div>
                            </div>

                            <!-- Tin nhắn của mình có kèm chữ và hình ảnh (Phải) -->
                            <div class="d-flex mb-3 justify-content-end align-items-end">
                                <div class="bg-primary text-white p-2 px-3 shadow-sm" style="max-width: 75%; border-radius: 18px 18px 4px 18px;">
                                    <div class="mb-2">Bạn xem thử thiết kế này xem có được không nhé.</div>
                                    <img src="https://images.unsplash.com/photo-1605810230434-7631ac76ec81?w=400" class="img-fluid" alt="Sent Image">
                                </div>
                            </div>

                            <!-- Tin nhắn của người kia có kèm chữ và hình ảnh (Trái) -->
                            <div class="d-flex mb-3 justify-content-start align-items-end">
                                <img src="/img/default_avatar.png" class="rounded-circle me-2 shadow-sm" width="30" height="30" style="object-fit: cover; border: 1px solid #555;">
                                <div class="bg-secondary bg-opacity-25 text-white p-2 px-3 shadow-sm" style="max-width: 75%; border-radius: 18px 18px 18px 4px;">
                                    <div class="mb-2">Tuyệt vời quá! Nhưng bạn thử thêm một chút hiệu ứng vào đây xem sao:</div>
                                    <img src="https://images.unsplash.com/photo-1542831371-29b0f74f9713?w=400" class="img-fluid" alt="Received Image With Text">
                                </div>
                            </div>

                            <!-- Tin nhắn của người kia chỉ gửi hình ảnh (Trái) -->
                            <div class="d-flex mb-3 justify-content-start align-items-end">
                                <img src="/img/default_avatar.png" class="rounded-circle me-2 shadow-sm" width="30" height="30" style="object-fit: cover; border: 1px solid #555;">
                                <img src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=400" class="img-fluid shadow-sm" style="max-width: 75%;" alt="Received Image">
                            </div>

                            <!-- Typing Indicator Mockup -->
                            <!--
                            <div class="d-flex mb-3 justify-content-start align-items-end">
                                <img src="/img/default_avatar.png" class="rounded-circle me-2 shadow-sm" width="30" height="30" style="object-fit: cover; border: 1px solid #555;">
                                <div class="bg-secondary bg-opacity-25 text-white p-2 px-3 shadow-sm d-flex align-items-center" style="max-width: 75%; border-radius: 18px 18px 18px 4px; height: 38px;">
                                    <div class="spinner-grow spinner-grow-sm text-muted me-1" role="status" style="width: 0.3rem; height: 0.3rem;"></div>
                                    <div class="spinner-grow spinner-grow-sm text-muted me-1" role="status" style="width: 0.3rem; height: 0.3rem; animation-delay: 0.1s;"></div>
                                    <div class="spinner-grow spinner-grow-sm text-muted" role="status" style="width: 0.3rem; height: 0.3rem; animation-delay: 0.2s;"></div>
                                </div>
                            </div>
                            -->

                        </div>

                        <!-- Khu vực nhập tin nhắn -->
                        <div class="p-3 bg-dark bg-opacity-50 border-top border-secondary mt-auto">
                            <form action="javascript:void(0)" class="d-flex align-items-center">
                                <button type="button" class="btn btn-outline-secondary border-0 rounded-circle me-2 text-muted" title="Đính kèm ảnh"><i class="bi bi-image"></i></button>
                                
                                <div class="input-group">
                                    <input type="text" class="form-control bg-dark border-secondary text-white rounded-pill px-4" placeholder="Nhập tin nhắn..." required>
                                </div>
                                
                                <button class="btn btn-primary rounded-circle ms-2 shadow-sm" type="button" style="width: 42px; height: 42px; flex-shrink: 0;"><i class="bi bi-send-fill"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php require 'footer.php'; ?>
    </div>
</div>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/js/jquery-3.3.1.min.js"></script>
<script src="/js/main.js"></script>
</body>
</html>
