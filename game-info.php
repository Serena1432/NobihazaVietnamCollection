<div class="container-fluid content-wrapper">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center border-bottom border-dark pb-3 mb-4">
                <div class="d-flex align-items-center mb-3 mb-lg-0">
                    <span class="game-header-badge me-3"><?= $engine_vocab[$game->engine] ?></span>
                    <h3 class="fw-bold mb-0 text-white" id="gameTitle"><?= htmlentities($game->name) ?></h3>
                </div>
                <div class="game-header-actions d-flex align-items-center align-self-end">
                    <span class="text-muted me-3"><i class="bi bi-eye"></i> <?= number_format($game->views, 0, ",", ".") ?></span>
                    <div class="game-stats-box me-2">
                        <button class="game-stats-btn"><i class="bi bi-eye text-muted me-2"></i> Theo dõi</button>
                        <span class="game-stats-count fw-bold"><?= number_format($follows, 0, ",", ".") ?></span>
                    </div>
                    <div class="game-stats-box">
                        <button class="game-stats-btn btn-dl-red"><i class="bi bi-download me-2"></i> Tải xuống</button>
                        <span class="game-stats-count fw-bold"><?= number_format($game->downloads, 0, ",", ".") ?></span>
                    </div>
                </div>
            </div>
            <?php
            $oses = explode(",", $game->supported_os);
            ?>
            <?php if (!in_array(strtolower(user_agent()), $oses)): ?>
            <div class="alert alert-dark text-white border-warning mb-4" role="alert">
                <i class="bi bi-slash-circle text-warning me-2"></i> Game này không hỗ trợ thiết bị của bạn.
            </div>
            <?php endif ?>
            <div class="row">
                <div class="col-lg-8 order-2 order-lg-1">
                    <div class="d-lg-none card-dark p-3 mb-4">
                        <div class="mb-3">
                            <h4 class="text-white fw-bold mb-0"><?php
                                $authors = explode(",", $game->author); $elements = [];
                                foreach ($authors as $author) array_push($elements, '<a href="/search?author=' . $author . '">' . $author . '</a>');
                                echo implode(", ", $elements);
                            ?></h4>
                            <div class="text-muted small">Nhà phát triển</div>
                        </div>
                        <div class="mb-3">
                            <h4 class="text-white fw-bold mb-0"><a href="/profile/<?php echo $game->uploader ?>"><?php $uploader = new Nbhzvn_User($game->uploader); echo $uploader->display_name() ?></a></h4>
                            <div class="text-muted small">Người tải lên</div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mb-3 text-muted" style="font-size: 0.9rem;">
                            <span class="badge bg-secondary"><a class="text-white" href="/search?engine=<?php echo $game->engine ?>"><i class="bi bi-calendar3"></i> <?php echo $game->release_year ?></a></span>
                            <?php if ($game->status >= STATUS_FINISHED): ?>
                                <span class="badge bg-success"><a class="text-white" href="/search?status=<?php echo $game->status ?>"><i class="bi bi-check-circle"></i> <?php echo $status_vocab[$game->status] ?></a></span>
                            <?php else: ?>
                                <span class="badge bg-warning"><a class="text-dark" href="/search?status=<?php echo $game->status ?>"><i class="bi bi-circle"></i> <?php echo $status_vocab[$game->status] ?></a></span>
                            <?php endif ?>
                            <span class="badge bg-info"><a class="text-dark" href="/search?language=<?php echo $game->language ?>"><i class="bi bi-translate"></i> <?php echo $language_vocab[$game->language] ?></a></span>
                            <?php
                                $os_icons = [
                                    "windows" => "windows",
                                    "mac" => "apple",
                                    "linux" => "tux",
                                    "android" => "android",
                                    "ios" => "apple"
                                ];
                                $oses = explode(",", $game->supported_os);
                                foreach ($oses as $os) {
                                    $icon = isset($os_icons[$os]) ? $os_icons[$os] : 'display';
                                    echo '<span class="badge bg-' . $os . '"><a class="text-dark" href="/search?supported_os=' . $os . '"><i class="bi bi-' . $icon . '"></i> ' . $os_vocab[$os] . '</a></span>';
                                }
                            ?>
                        </div>
                        <?php if ($game->tags && $game->tags != ""): ?>
                        <div class="mb-2">
                            <i class="bi bi-tags text-primary me-2"></i>
                            <?php
                                $tags = explode(",", $game->tags);
                                foreach ($tags as $tag) echo '<span class="badge border border-secondary" style="margin-right: 5px"><a class="text-muted" href="/search?tags=' . $tag . '">' . $tag . '</a></span>';
                            ?>
                        </div>
                        <?php endif ?>
                        <div class="mt-3 text-warning fs-5">
                            <?php
                                $average = $ratings->average;
                                $full = floor($average); $remain = $average - $full; $index = 0; $ostar = 4 - $full;
                                for ($i = 0; $i < $full; $i++) {
                                    $index++;
                                    echo '<i class="bi bi-star-fill"></i> ';
                                }
                                if ($index < 5) {
                                    $index++;
                                    echo '<i class="bi bi-star' . (($remain >= 0.5) ? '-half' : '') . '"></i>';
                                }
                                for ($i = 0; $i < $ostar; $i++) {
                                    $index++;
                                    echo '<i class="bi bi-star"></i> ';
                                }
                            ?>
                            <span class="text-white fs-6 ms-2">(<?= $ratings->total ?> đánh giá)</span>
                        </div>
                    </div>
                    <img src="/uploads/<?php echo $game->image ?>" class="w-100 rounded mb-4 border border-dark" alt="Game Thumbnail">
                    <div class="card-dark mb-4">
                        <div class="card-header border-bottom border-dark p-0">
                            <ul class="nav nav-tabs custom-tabs" id="gameTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active py-3 px-4" data-bs-toggle="tab" data-bs-target="#desc" type="button">Mô tả game</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-3 px-4" data-bs-toggle="tab" data-bs-target="#changelog" type="button">Nhật ký cập nhật</button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body p-4">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="desc">
                                    <?php echo $parsedown->text($game->description) ?>
                                </div>
                                <div class="tab-pane fade" id="changelog">
                                    <?php if ($user && $user->id == $game->uploader): ?>
                                    <div class="mb-4 text-end" id="addChangelogArea">
                                        <button class="btn btn-primary btn-sm fw-bold shadow-sm" onclick="addChangelog()"><i class="bi bi-plus-lg"></i> Thêm nhật ký cập nhật mới</button>
                                    </div>
                                    <?php endif; ?>
                                    <div id="changelogs">
                                        <?php
                                        if (count($changelogs) > 0) {
                                            $changelog_html = array_map(function($a, $i) use ($user) {return $a->to_html($user, $i == 0);}, $changelogs, array_keys($changelogs));
                                            echo implode('<hr class="border-secondary">', $changelog_html);
                                        }
                                        else echo '<p id="noChangelogText" class="text-muted"><i>Chưa có nhật ký cập nhật nào được thêm.</i></p>';
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h5 class="fw-bold border-start border-primary border-4 ps-2 mb-3">Ảnh chụp màn hình</h5>
                    <div class="game_screenshots_container mb-4">
                        <?php
                            foreach ($game->screenshots as $screenshot) {
                                $path = "/uploads/" . $screenshot;
                                echo '<a href="' . $path . '" data-fslightbox="gallery"><img src="' . $path . '" class="rounded border border-secondary shadow-sm" style="max-height: 200px; width: auto; object-fit: cover;" alt="Screenshot" /></a>';
                            }
                        ?>
                    </div>
                    <div class="card-dark p-4 mb-4 text-center">
                        <h4 class="fw-bold mb-3">Đánh giá Game</h4>
                        <div class="text-warning fs-1 mb-2" id="rating">
                            <?php
                                $average = $ratings->average;
                                $full = floor($average); $remain = $average - $full; $index = 0; $ostar = 4 - $full;
                                for ($i = 0; $i < $full; $i++) {
                                    $index++;
                                    echo '<a href="javascript:void(0)" onclick="rate(' . $game->id . ', ' . $index . ')" class="text-warning text-decoration-none"><i ' . ($rated ? 'data-rated="true"' : '') . ' id="star-' . $index . '" class="bi bi-star-fill"></i></a> ';
                                }
                                if ($index < 5) {
                                    $index++;
                                    echo '<a href="javascript:void(0)" onclick="rate(' . $game->id . ', ' . $index . ')" class="text-warning text-decoration-none"><i ' . ($rated ? 'data-rated="true"' : '') . ' id="star-' . $index . '" class="bi bi-star' . (($remain >= 0.5) ? '-half' : '') . '"></i></a> ';
                                }
                                for ($i = 0; $i < $ostar; $i++) {
                                    $index++;
                                    echo '<a href="javascript:void(0)" onclick="rate(' . $game->id . ', ' . $index . ')" class="text-warning text-decoration-none"><i ' . ($rated ? 'data-rated="true"' : '') . ' id="star-' . $index . '" class="bi bi-star"></i></a> ';
                                }
                            ?>
                        </div>
                        <?php if (!$user || !$user->id): ?>
                        <p class="text-muted">Bạn phải <a href="/login.php" class="text-decoration-none text-primary">đăng nhập</a> để đánh giá game này.</p>
                        <?php else: ?>
                        <p class="text-muted" id="ratingText"><?php echo number_format($ratings->total, 0, ",", ".") ?> lượt đánh giá</p>
                        <?php endif ?>
                    </div>
                    
                    <h5 class="fw-bold border-start border-primary border-4 ps-2 mb-3"><i class="bi bi-star"></i> Đánh giá (<?php echo count($all_ratings) ?>)</h5>
                    <div class="card-dark p-3 mb-4">
                        <p style="font-size: 11pt" class="text-muted mb-4"><i>Để đảm bảo an toàn, website sẽ không hiển thị tên đầy đủ của các thành viên đã đánh giá. Chỉ có Quản Trị Viên mới xem được tên hiển thị đầy đủ và thực hiện hành động đối với các đánh giá này.</i></p>
                        <div id="ratings">
                            <?php
                                if (count($all_ratings) > 0) {
                                    $i = 0;
                                    foreach ($all_ratings as $rating) {
                                        if ($i >= 5) break;
                                        echo $rating->to_html($user);
                                        $i++;
                                    }
                                }
                                else echo '<p class="text-muted"><i>Chưa có đánh giá nào.</i></p>';
                            ?>
                        </div>
                        <?php echo pagination(count($all_ratings), 5, 1, "Ratings"); ?>
                    </div>

                    <h5 class="fw-bold border-start border-primary border-4 ps-2 mb-3"><i class="bi bi-chat-left-text"></i> Bình luận (<?php echo count($comments) ?>)</h5>
                    <div class="card-dark p-3 mb-4">
                        <div id="comments">
                            <?php
                                $highlighted_comment_id = is_numeric(get("highlighted_comment")) ? intval(get("highlighted_comment")) : 0;
                                if ($highlighted_comment_id) {
                                    $highlighted_comment = new Nbhzvn_Comment($highlighted_comment_id);
                                    if ($highlighted_comment->id) echo $highlighted_comment->to_html(!!$highlighted_comment->replied_to, $user, false, is_numeric(get("reply_comment")) ? intval(get("reply_comment")) : $highlighted_comment_id);
                                }
                                foreach ($comments as $comment) if ($comment->id != $highlighted_comment_id) echo $comment->to_html(!!$comment->replied_to, $user);
                            ?>
                        </div>
                        <?php echo pagination(count($comments)); ?>
                        <div class="mt-4 border-top border-dark pt-3">
                            <h6 class="fw-bold mb-3">Viết bình luận mới</h6>
                            <form action="javascript:void(0)" onsubmit="comment()" class="p-3 bg-dark bg-opacity-50 border border-secondary rounded d-flex align-items-center">
                                <button type="button" class="btn btn-outline-secondary border-0 rounded-circle me-2 text-muted" title="Đính kèm ảnh"><i class="bi bi-image"></i></button>
                                
                                <div class="input-group">
                                    <input type="text" id="commentContent" class="form-control bg-dark border-secondary text-white rounded-pill px-4" placeholder="Nội dung bình luận..." required>
                                </div>
                                
                                <button id="commentBtn" class="btn btn-primary rounded-circle ms-2 shadow-sm" type="button" style="width: 42px; height: 42px; flex-shrink: 0;" onclick="comment()"><i class="bi bi-send-fill"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 order-1 order-lg-2">
                    <div class="d-none d-lg-block card-dark p-3 mb-4">
                        <div class="mb-3">
                            <h4 class="text-white fw-bold mb-0">Lorem Ipsum</h4>
                            <div class="text-muted small">Nhà phát triển</div>
                        </div>
                        <hr class="border-secondary">
                        <div class="mb-3">
                            <h4 class="text-white fw-bold mb-0">Lorem Ipsum</h4>
                            <div class="text-muted small">Người tải lên</div>
                        </div>
                        <hr class="border-secondary">
                        <div class="d-flex flex-column gap-2 mb-3 text-muted">
                            <div><i class="bi bi-calendar3 me-2 text-primary"></i> Năm ra mắt: <strong>2026</strong></div>
                            <div><i class="bi bi-check-circle me-2 text-success"></i> Trạng thái: <strong>Hoàn thành</strong></div>
                            <div><i class="bi bi-translate me-2 text-info"></i> Ngôn ngữ: <strong>Tiếng Việt</strong></div>
                            <div><i class="bi bi-laptop me-2 text-warning"></i> Nền tảng: <strong>PC, Mobile</strong></div>
                        </div>
                        <hr class="border-secondary">
                        <div class="mb-2">
                            <i class="bi bi-tags text-primary me-2"></i> Thẻ:
                            <div class="mt-2">
                                <span class="badge border border-secondary text-muted">Lorem Ipsum</span>
                                <span class="badge border border-secondary text-muted">Lorem Ipsum</span>
                                <span class="badge border border-secondary text-muted">Lorem Ipsum</span>
                            </div>
                        </div>
                        <hr class="border-secondary">
                        <div>
                            <span class="text-muted">Đánh giá chung:</span>
                            <div class="mt-1 text-warning fs-5">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-half"></i>
                                <span class="text-white fs-6 ms-2">4.8/5</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-dark p-0 mb-4">
                        <div class="card-header border-bottom border-dark bg-transparent fw-bold text-primary p-3">
                            <i class="bi bi-cloud-arrow-down"></i> Tệp Tải Xuống
                        </div>
                        <div class="list-group list-group-flush rounded-bottom">
                            <a href="#" class="list-group-item list-group-item-action bg-transparent border-dark text-white p-3">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1"><i class="bi bi-file-zip text-warning me-2"></i> Bản PC (Windows)</h6>
                                    <small class="text-muted">v1.2.0</small>
                                </div>
                                <div class="mb-1 text-muted small">Cập nhật: 20/05/2026</div>
                                <small class="text-primary">Dung lượng: 350 MB</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action bg-transparent border-dark text-white p-3">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1"><i class="bi bi-filetype-apk text-success me-2"></i> Bản Mobile (Android)</h6>
                                    <small class="text-muted">v1.2.0</small>
                                </div>
                                <div class="mb-1 text-muted small">Cập nhật: 20/05/2026</div>
                                <small class="text-primary">Dung lượng: 120 MB</small>
                            </a>
                        </div>
                    </div>
                    <div class="card-dark p-3 mb-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-people text-primary"></i> Đang theo dõi (1.2k)</h6>
                        <div class="avatar-list">
                            <img src="https://via.placeholder.com/32/3498db/ffffff?text=1" class="avatar-sm" title="User 1">
                            <img src="https://via.placeholder.com/32/e74c3c/ffffff?text=2" class="avatar-sm" title="User 2">
                            <img src="https://via.placeholder.com/32/2ecc71/ffffff?text=3" class="avatar-sm" title="User 3">
                            <img src="https://via.placeholder.com/32/f1c40f/ffffff?text=4" class="avatar-sm" title="User 4">
                            <img src="https://via.placeholder.com/32/9b59b6/ffffff?text=5" class="avatar-sm" title="User 5">
                            <img src="https://via.placeholder.com/32/e67e22/ffffff?text=6" class="avatar-sm" title="User 6">
                            <img src="https://via.placeholder.com/32/34495e/ffffff?text=+" class="avatar-sm" title="More...">
                        </div>
                    </div>
                    <div class="card-dark p-3 mb-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-lightbulb text-primary"></i> Gợi ý Game Khác</h6>
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://images.unsplash.com/photo-1493711662062-fa541adb3fc8?w=100" class="rounded me-3" width="60" height="40" style="object-fit:cover;" alt="Game">
                            <div>
                                <a href="#" class="text-white fw-bold text-decoration-none small">Lorem Ipsum</a>
                                <div class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-star-fill text-warning"></i> 4.5</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <img src="https://images.unsplash.com/photo-1552820728-8b83bb6b773f?w=100" class="rounded me-3" width="60" height="40" style="object-fit:cover;" alt="Game">
                            <div>
                                <a href="#" class="text-white fw-bold text-decoration-none small">Lorem Ipsum</a>
                                <div class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-star-fill text-warning"></i> 4.9</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>