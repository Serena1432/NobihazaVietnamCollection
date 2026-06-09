<?php
require "api/functions.php";
require "api/users/functions.php";
require "api/users/cookies.php";
require "api/games/functions.php";
require "api/discord/webhook.php";
if (!$user || $user->type < 2) redirect_to_home();

$error = "";
$notice = "";

function process() {
    global $error;
    global $notice;
    global $user;
    global $conn;
    if (!check_csrf(post("csrf_token"))) return $error = "Mã xác thực CSRF không đúng.";
    if ($user->check_timeout("upload") && $user->type < 3) return $error = "Bạn cần đợi ít nhất 10 phút từ lần thêm cuối cùng trước khi thêm một game mới.";
    $inputs = ["name", "image", "links", "beta_links", "beta_users", "screenshots", "description", "engine", "release_year", "author", "language", "status", "supported_os"];
    $data = array();
    foreach ($inputs as $input) {
        if (!post($input)) return $error = "Vui lòng nhập đầy đủ thông tin.";
        $data[$input] = post($input);
    }
    $data["links"] = base64_decode($data["links"]);
    $data["beta_links"] = base64_decode($data["beta_links"]);
    $data["beta_users"] = base64_decode($data["beta_users"]);
    $data["screenshots"] = base64_decode($data["screenshots"]);
    $links = json_decode($data["links"]); $beta_links = json_decode($data["beta_links"]);
    if (count($links) < 1 && count($beta_links) < 1) return $error = "Vui lòng tải ít nhất một tệp tin game lên.";
    foreach ($links as $link) {
        if (!$link || !$link->path) return $error = "Có ít nhất một tệp tin bị lỗi trong quá trình tải lên, vui lòng kiểm tra lại các tệp tin đã tải lên và thử lại.";
    }
    foreach ($beta_links as $beta_link) {
        if (!$beta_link || !$beta_link->path) return $error = "Có ít nhất một tệp tin bị lỗi trong quá trình tải lên, vui lòng kiểm tra lại các tệp tin đã tải lên và thử lại.";
    }
    $screenshots = json_decode($data["screenshots"]);
    if (count($screenshots) < 1) return $error = "Vui lòng tải ít nhất một ảnh chụp màn hình lên.";
    foreach ($screenshots as $screenshot) {
        if (!$screenshot) return $error = "Có ít nhất một ảnh chụp màn hình bị lỗi trong quá trình tải lên, vui lòng kiểm tra lại các ảnh chụp màn hình đã tải lên và thử lại.";
    }
    $data["tags"] = post("tags");
    $data["translator"] = post("translator");
    $data["uploader"] = $user->id;
    $data = json_decode(json_encode($data));
    $result = add_game($data, ($user->type == 3));
    $game_id = $conn->insert_id;
    $user->update_timeout("upload", time() + 600);
    if ($user->type < 3) {
        $admins = get_admins();
        foreach ($admins as $admin) $admin->send_notification("/games/" . $game_id, "Game **" . post("name") . "** vừa mới được tải lên và cần các Quản Trị Viên phê duyệt.");
        send_moderation_webhook(new Nbhzvn_Game($game_id));
    }
    else send_newgame_webhook(new Nbhzvn_Game($game_id));
    if ($result == SUCCESS) $notice = "Đã tải lên game thành công" . ($user->type < 3 ? ", game của bạn sẽ được hiển thị trên trang web sau khi Quản Trị Viên đã duyệt game của bạn." : ".");
}

try {
    if (post("submit")) process();
}
catch (Exception $ex) {
    switch ($ex->getMessage()) {
        case MISSING_INFORMATION: {
            $error = "Vui lòng nhập đầy đủ thông tin.";
            break;
        }
        default: {
            $error = "Có lỗi không xác định xảy ra. Vui lòng báo cáo cho nhà phát triển của website.<br>" . htmlentities($ex->getMessage());
            break;
        }
    }
}
if ($notice) die('
    <script>
        window.localStorage.removeItem("nbhzvn_upload_autosave");
        alert("' . $notice . '");
        document.location.href = "/";
    </script>
    <p>' . $notice . ' <a href="/">Tiếp tục</a></p>
');
refresh_csrf();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php
        $title = "Thêm Game Mới";
        require "head.php";
    ?>
    <link rel="stylesheet" href="/css/toastr.css" />
</head>
<body>
<div class="app-container">
    <?php require 'sidebar.php'; ?>
    <div class="main-content">
        <?php require "mobile-top-nav.php" ?>
        <div class="normal-hero"></div>
        <div class="container-fluid content-wrapper mt-4">
            <h4 class="fw-bold mb-4">Thêm Game Mới</h4>
            <form action="" method="POST" onsubmit="return processSubmit()">
                <div class="mb-3">
                    <label class="form-label text-muted"><b>Tên Game</b></label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-file-earmark-text"></i></span>
                        <input type="text" class="form-control bg-dark border-secondary text-white" name="name" placeholder="Tên Game" required value="<?php echo base64_encode(post("name")) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted"><b>Ảnh Đại Diện</b></label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-image"></i></span>
                        <input readonly class="form-control bg-dark border-secondary text-white" style="cursor: pointer" name="image" placeholder="Nhấn vào đây để tải ảnh đại diện" required onclick="uploadThumbnail()" id="thumbnail" value="<?php echo post("image") ?>">
                    </div>
                    <div class="mt-2">
                        <img class="thumbnail_image img-fluid rounded" id="thumbnailImage" style="display: none; max-height: 200px; object-fit: cover" />
                    </div>
                    <div class="progress mt-2" id="thumbnailProgressBar" style="display: none;">
                        <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted"><b>Danh Sách Tệp Tin Game</b></label>
                    <div class="card-dark p-3 border-secondary">
                        <ul class="nav nav-tabs custom-tabs mb-3" id="tabList" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="gameFilesTab" data-bs-toggle="pill" data-bs-target="#gameFilesTabContent" type="button" role="tab" aria-controls="gameFilesTabContent" aria-selected="true">Chính Thức</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="betaGameFilesTab" data-bs-toggle="pill" data-bs-target="#betaGameFilesTabContent" type="button" role="tab" aria-controls="betaGameFilesTabContent" aria-selected="false">Thử Nghiệm (Beta)</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="tabContent">
                            <div class="tab-pane fade show active" id="gameFilesTabContent" role="tabpanel" aria-labelledby="gameFilesTab">
                                <div class="text-end mb-2">
                                    <button type="button" onclick="addGameFile()" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Thêm tệp tin</button>
                                </div>
                                <div id="gameFiles"></div>
                            </div>
                            <div class="tab-pane fade" id="betaGameFilesTabContent" role="tabpanel" aria-labelledby="betaGameFilesTab">
                                <p class="text-muted small">Bạn có bản beta của game mà chỉ muốn cho một số thành viên nhất định tải xuống? Bạn có thể thêm nó vào đây!</p>
                                <div class="text-end mb-2">
                                    <button type="button" onclick="addBetaGameFile()" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Thêm tệp tin</button>
                                </div>
                                <div id="betaGameFiles" class="mb-4"></div>
                                
                                <h5 class="fw-bold mb-3">Danh Sách Tester</h5>
                                <div class="text-end mb-2">
                                    <button type="button" onclick="addBetaUser()" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Thêm Tester</button>
                                </div>
                                <div id="betaUsers"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted"><b>Ảnh Chụp Màn Hình Game</b></label>
                    <div class="text-end mb-2">
                        <button type="button" onclick="addScreenshot()" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Thêm ảnh</button>
                    </div>
                    <div id="screenshots" class="upload_screenshots"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted"><b>Mô Tả</b></label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-pencil"></i></span>
                        <textarea class="form-control bg-dark border-secondary text-white" name="description" placeholder="Mô tả có hỗ trợ Markdown." required rows="6"><?php echo post("description") ?></textarea>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted"><b>Phần Mềm Làm Game</b></label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-tools"></i></span>
                        <select class="form-select bg-dark border-secondary text-white" name="engine" required>
                            <?php
                                foreach ($engine_vocab as $value => $vocab) {
                                    echo '<option value="' . $value . '"' . ((intval(post("engine")) == $value) ? " selected" : "") . '>' . $vocab . '</option>';
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted"><b>Thẻ</b></label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-tags"></i></span>
                        <input type="text" class="form-control bg-dark border-secondary text-white" name="tags" placeholder="Các thẻ cách nhau bằng dấu phẩy viết liền. Không bắt buộc." value="<?php echo post("tags") ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted"><b>Năm Phát Hành</b></label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-calendar"></i></span>
                        <input type="number" class="form-control bg-dark border-secondary text-white" name="release_year" placeholder="Năm Phát Hành" value="<?php echo post("release_year") ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted"><b>Tác Giả Gốc</b></label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control bg-dark border-secondary text-white" name="author" placeholder="Nhà phát triển của game gốc (chưa được dịch). Nếu có nhiều tác giả thì bạn có thể tách bằng dấu phẩy viết liền." required value="<?php echo post("author") ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted"><b>Ngôn Ngữ</b></label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-globe2"></i></span>
                        <select class="form-select bg-dark border-secondary text-white" name="language" required>
                            <?php
                                foreach ($language_vocab as $value => $vocab) {
                                    echo '<option value="' . $value . '"' . ((intval(post("language")) == $value) ? " selected" : "") . '>' . $vocab . '</option>';
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted"><b>Dịch Giả</b></label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-person-fill"></i></span>
                        <input type="text" class="form-control bg-dark border-secondary text-white" name="translator" placeholder="Bỏ trống nếu game chưa được dịch sang ngôn ngữ nào khác. Nếu có nhiều dịch giả thì bạn có thể tách bằng dấu phẩy viết liền." value="<?php echo post("translator") ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted"><b>Trạng Thái</b></label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-info-circle"></i></span>
                        <select class="form-select bg-dark border-secondary text-white" name="status" required>
                            <?php
                                foreach ($status_vocab as $value => $vocab) {
                                    echo '<option value="' . $value . '"' . ((intval(post("status")) == $value) ? " selected" : "") . '>' . $vocab . '</option>';
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted d-block"><b>Nền Tảng Được Hỗ Trợ</b></label>
                    <?php
                        $supported_oses = explode(",", post("supported_os"));
                        foreach ($os_vocab as $value => $vocab) {
                            echo '<div class="form-check form-check-inline">';
                            echo '<input type="checkbox" class="form-check-input bg-dark border-secondary supported_os_checkbox" value="' . $value . '"' . (in_array($value, $supported_oses) ? " checked" : "") . ' id="os_' . $value . '">';
                            echo '<label class="form-check-label text-white" for="os_' . $value . '">' . $vocab . '</label>';
                            echo '</div>';
                        }
                    ?>
                </div>

                <input type="hidden" name="links" value='<?php echo post("links") ?>' id="linksInput" />
                <input type="hidden" name="beta_links" value='<?php echo post("beta_links") ?>' id="betaLinksInput" />
                <input type="hidden" name="beta_users" value='<?php echo post("beta_users") ?>' id="betaUsersInput" />
                <input type="hidden" name="screenshots" value='<?php echo post("screenshots") ?>' id="screenshotsInput" />
                <input type="hidden" name="supported_os" value='<?php echo post("supported_os") ?>' id="supportedOSInput" />
                <input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>" />

                <?php if ($error) echo '<p class="text-danger fst-italic">' . $error . '</p>'; ?>

                <div class="d-flex gap-2">
                    <button type="submit" name="submit" class="btn btn-primary fw-bold" value="Submit"><i class="bi bi-cloud-arrow-up"></i> Thêm Game Mới</button>
                    <button type="button" class="btn btn-secondary fw-bold" onclick="AutoSave.delete()" id="deleteDraftBtn"><i class="bi bi-trash"></i> Xoá bản nháp</button>
                </div>
            </form>
        </div>
        
        <input type="file" id="thumbnailFile" class="d-none" accept=".jpg, .png, .jpeg, .webp|image/*" />
        <div id="files"></div>

        <?php require 'footer.php'; ?>
    </div>
</div>

<!-- Modal container for beta users -->
<div class="modal fade" id="betaUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content card-dark">
      <div class="modal-header border-secondary">
        <h5 class="modal-title text-primary" id="modalTitle">Thêm Tester</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalBody">
        <!-- Content injected by modal.js -->
      </div>
      <div class="modal-footer border-secondary" id="modalFooter">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
      </div>
    </div>
  </div>
</div>

<!-- Js Plugins -->
<script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/js/jquery-3.3.1.min.js"></script>
<script src="/js/base64.min.js"></script>
<script src="/js/main.js"></script>
<script src="/js/toastr.js"></script>
<script src="/js/api.js"></script>
<script src="/js/modal.js"></script>
<script src="/js/uploader.js"></script>
</body>
</html>
