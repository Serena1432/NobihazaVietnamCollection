/*
===========================================================
Rating icon hovering mechanics
===========================================================
*/

function highlightStars(cursor) {
    for (var i = 1; i <= cursor; i++) $(`#star-${i}`).attr("class", "fa fa-star");
    for (var i = cursor + 1; i <= 5; i++) $(`#star-${i}`).attr("class", "fa fa-star-o");
}

function unhighlightStars() {
    for (var i = 1; i <= 5; i++) $(`#star-${i}`).attr("class", $(`#star-${i}`).attr("data-original-class"));
}

var hovering = {};
for (var i = 1; i <= 5; i++) {
    var element = $(`#star-${i}`);
    element.attr("star-id", i.toString());
    element.attr("data-original-class", element.attr("class"));
    if (!element.attr("data-rated")) {
        element.hover(function() {
            if ($(this).attr("data-rated")) return;
            var starId = parseInt($(this).attr("star-id"));
            hovering[starId] = true;
            highlightStars(starId);
        });
        element.mouseout(function() {
            var starId = parseInt($(this).attr("star-id"));
            hovering[starId] = false;
            if (!Object.values(hovering).filter(val => val).length) unhighlightStars();
        });
    }
}

/*
===========================================================
Follow command
===========================================================
*/

async function toggleFollow(id) {
    var response = await apiRequest({
        url: "/api/games/follow",
        type: "POST",
        cache: false,
        contentType: false,
        processData: false,
        data: JSON.stringify({
            game_id
        }),
        json: true
    });
    if (response?.success) {
        $("#followText").text(response.data.type == "follow" ? "Bỏ theo dõi" : "Theo dõi");
        $("#followCount").text(response.data.followers.toLocaleString());
    }
}

/*
===========================================================
Comment function
===========================================================
*/

async function comment() {
    try {
        var content = $("#commentContent").val(), button = $("#commentBtn");
        if (!content) return toastr.error("Vui lòng nhập nội dung bình luận.", "Thông báo");
        button.prop("disabled", true);
        var response = await apiRequest({
            url: "/api/games/comments/",
            type: "PUT",
            cache: false,
            contentType: false,
            processData: false,
            data: JSON.stringify({
                game_id: gameId,
                content
            }),
            json: true
        });
        button.prop("disabled", false);
        if (response?.success) {
            $("#comments").prepend(response.data);
            $("#commentContent").val("");
            document.location.href = "#comments";
        }
    }
    catch (err) {
        console.error(err);
        button.prop("disabled", false);
    }
}

/*
===========================================================
View replies command
===========================================================
*/

async function viewReplies(id) {
    $(`#comment-${id}-repliesbtn`).remove();
    var response = await apiRequest({
        url: `/api/games/comments/replies?id=${id}&include_html=true`,
        type: "GET",
        cache: false,
        contentType: false,
        processData: false
    });
    if (response?.success) {
        $(`#comment-${id}-replies`).html(response.data.html);
        commentData = commentData.concat(response.data.list);
        delete replying[id];
    }
}

/*
===========================================================
Comment managing function
===========================================================
*/

async function deleteComment(id) {
    if (!confirm("Xác nhận xoá bình luận này?")) return;
    var response = await apiRequest({
        url: "/api/games/comments/",
        type: "DELETE",
        cache: false,
        contentType: false,
        processData: false,
        data: JSON.stringify({
            id
        }),
        json: true
    });
    if (response?.success) {
        toastr.success(response.message, "Thông báo");
        $(`#comment-${id}`).remove();
    }
}

var originalContent = {};

function safeEdit(el, text) {
    const div = $("<div>").text(text);
    const safeHtml = div.html().replace(/\n/g, "<br>");
    $(el).html(safeHtml);
}

function editComment(id) {
    originalContent[id] = {
        content: commentData.find(comment => comment.id == id)?.content,
        options: $(`#comment-${id}-options`).html()
    };
    $(`#comment-${id}-content`).html(`<textarea class="comment_edit_box" id="comment-${id}-editbox"></textarea>`);
    $(`#comment-${id}-editbox`).val(originalContent[id].content);
    $(`#comment-${id}-options`).html(`<a href="javascript:void(0)" onclick="processCommentEdit(${id})">Chỉnh sửa</a> • <a href="javascript:void(0)" onclick="cancelCommentEdit(${id})">Huỷ</a>`);
}

function cancelCommentEdit(id) {
    if (!originalContent[id]) return;
    safeEdit(`#comment-${id}-content`, originalContent[id].content.replace("\n", "\r\n"));
    $(`#comment-${id}-options`).html(originalContent[id].options);
    delete originalContent[id];
}

async function processCommentEdit(id) {
    var content = $(`#comment-${id}-editbox`).val();
    if (!content) return toastr.error("Vui lòng nhập nội dung bình luận.", "Thông báo");
    var response = await apiRequest({
        url: "/api/games/comments/",
        type: "POST",
        cache: false,
        contentType: false,
        processData: false,
        data: JSON.stringify({
            id,
            content
        }),
        json: true
    });
    if (response?.success) {
        toastr.success(response.message, "Thông báo");
        var dummy = document.createElement("div");
        dummy.innerHTML = response.data;
        $(`#comment-${id}`).html(dummy.getElementsByClassName("comment_container")[0].innerHTML);
        dummy.remove();
    }
}

var replying = {};

function replyComment(id, mention) {
    if (replying[id]) return $(`#comment-${id}-reply-box`).val(mention ? `@${mention} ` : "");
    replying[id] = true;
    var div = document.createElement("div");
    div.id = `comment-${id}-reply-container`;
    div.innerHTML = `
        <textarea id="comment-${id}-reply-box" class="comment_reply_box">${mention ? `@${mention} ` : ""}</textarea>
        <button onclick="processCommentReply(${id})" class="comment_reply_btn"><i class="fa fa-location-arrow"></i> Trả lời</button> <button onclick="cancelCommentReply(${id})" class="comment_reply_btn cancel"><i class="fa fa-times"></i> Huỷ</button> 
    `;
    document.getElementById(`comment-${id}-replies`).appendChild(div);
}

function cancelCommentReply(id) {
    $(`#comment-${id}-reply-container`).remove();
    delete replying[id];
}

async function processCommentReply(id) {
    var content = $(`#comment-${id}-reply-box`).val();
    if (!content) return toastr.error("Vui lòng nhập nội dung bình luận.", "Thông báo");
    cancelCommentReply(id);
    var response = await apiRequest({
        url: "/api/games/comments/",
        type: "PUT",
        cache: false,
        contentType: false,
        processData: false,
        data: JSON.stringify({
            game_id: gameId,
            content,
            replied_to: id
        }),
        json: true
    });
    if (response?.success) $(`#comment-${id}-replies`).append(response.data);
}

/*
===========================================================
Page navigation
===========================================================
*/

async function previousPage(distantId = "") {
    var elementId = `#${distantId.toLowerCase() || "comments"}`, api = `${distantId.toLowerCase() || "comments/"}`;
    if (Pagination.page(distantId) < 2) return;
    $(elementId).html("");
    $(elementId).html(await Pagination.previous(`/api/games/${api}?game_id=${gameId}&page={page}&html=true`, distantId));
}

async function nextPage(distantId = "") {
    var elementId = `#${distantId.toLowerCase() || "comments"}`, api = `${distantId.toLowerCase() || "comments/"}`;
    if (Pagination.page(distantId) >= Pagination.maxPages(distantId)) return;
    $(elementId).html("");
    $(elementId).html(await Pagination.next(`/api/games/${api}?game_id=${gameId}&page={page}&html=true`, distantId));
}

async function jumpToPage(distantId = "") {
    var elementId = `#${distantId.toLowerCase() || "comments"}`, api = `${distantId.toLowerCase() || "comments/"}`;
    if (Pagination.page(distantId) >= Pagination.maxPages(distantId)) return;
    $(elementId).html("");
    $(elementId).html(await Pagination.jump(`/api/games/${api}?game_id=${gameId}&page={page}&html=true`, Pagination.page(distantId), distantId));
}

/*
===========================================================
Rate with modal
===========================================================
*/

var modal = new Modal();

async function rate(id, rating) {
    if ($("#star-1").attr("data-rated")) return;
    modal.title = `Đánh Giá Game`;
    modal.body = `
        <p>Ghi rõ lý do tại sao bạn lại đánh giá ${rating} sao cho game <b>${$("#gameTitle > h3").text()}:</p>
        <div class="anime__details__form">
            <textarea id="ratingReason" style="height: 300px"></textarea>
        </div>
    `;
    modal.footer = `
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy bỏ</button>
        <button type="button" class="btn btn-primary" style="background-color: #af1932; border: 1px solid rgb(209, 55, 81)" onclick="processRate(${id}, ${rating})">Gửi đánh giá</button>
    `;
    modal.update();
    modal.show();
}

function updateReason(id, rating, reason) {
    modal.body = `
        <p>Ghi rõ lý do tại sao bạn lại đánh giá ${rating} sao cho game <b>${$("#gameTitle > h3").text()}:</p>
        <div class="anime__details__form">
            <textarea id="ratingReason" style="height: 300px">${reason}</textarea>
        </div>
    `;
    modal.footer = `
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy bỏ</button>
        <button type="button" class="btn btn-primary" style="background-color: #af1932; border: 1px solid rgb(209, 55, 81)" onclick="processRate(${id}, ${rating})">Gửi đánh giá</button>
    `;
    modal.update();
}

async function processRate(id, rating) {
    var reason = $("#ratingReason").val();
    modal.body = `<p><i>Đang gửi đánh giá, bạn vui lòng chờ một lát...</i></p>`;
    modal.footer = ``;
    modal.update();
    try {
        var response = await apiRequest({
            url: "/api/games/ratings",
            type: "PUT",
            cache: false,
            contentType: false,
            processData: false,
            data: JSON.stringify({
                game_id: id,
                rating,
                reason
            }),
            json: true
        });
        if (response?.success) {
            toastr.success(response.message, "Thông báo");
            modal.hide();
            var average = response.data.average,
                full = Math.floor(average), remain = average - full, index = 0, ostar = 4 - full, html = "";
            for (var i = 0; i < full; i++) {
                index++;
                html += `<a href="javascript:void(0)"><i data-rated="true" id="star-${i}" class="fa fa-star"></i></a> `;
            }
            if (index < 5) {
                index++;
                html += `<a href="javascript:void(0)"><i data-rated="true" id="star-${i}" class="fa fa-star${(remain >= 0.5) ? "-half" : ""}-o"></i></a> `;
            }
            for (var i = 0; i < ostar; i++) {
                index++;
                html += `<a href="javascript:void(0)"><i data-rated="true" id="star-${i}" class="fa fa-star-o"></i></a> `;
            }
            $("#rating").html(html);
            $(`#ratingText`).text(`${response.data.total} lượt đánh giá`);
        }
        else updateReason(id, rating, reason);
    }
    catch (err) {
        console.error(err);
        updateReason(id, rating, reason);
    }
}

/*
===========================================================
Delete rating function
===========================================================
*/

function updateDeleteRatingBody(id, reason) {
    modal.title = "Xóa Đánh Giá";
    modal.body = `
        <p>Ghi rõ lý do tại sao bạn muốn xóa đánh giá này:</p>
        <div class="anime__details__form">
            <textarea id="deleteRatingReason" style="height: 300px">${reason || ""}</textarea>
        </div>
    `;
    modal.footer = `
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy bỏ</button>
        <button type="button" class="btn btn-primary" style="background-color: #af1932; border: 1px solid rgb(209, 55, 81)" onclick="processDeleteRating(${id})">Xóa đánh giá</button>
    `;
    modal.update();
}

function deleteRating(id) {
    updateDeleteRatingBody(id);
    modal.show();
}

async function processDeleteRating(id) {
    var reason = $("#deleteRatingReason").val();
    modal.body = `<p><i>Đang xóa đánh giá, bạn vui lòng chờ một lát...</i></p>`;
    modal.footer = ``;
    modal.update();
    try {
        var response = await apiRequest({
            url: "/api/games/ratings",
            type: "DELETE",
            cache: false,
            contentType: false,
            processData: false,
            data: JSON.stringify({
                id,
                reason
            }),
            json: true
        });
        if (response?.success) {
            toastr.success(response.message, "Thông báo");
            $(`#rating-${id}`).remove();
            modal.hide();
        }
        else updateDeleteRatingBody(id, reason);
    }
    catch (err) {
        console.error(err);
        updateDeleteRatingBody(id, reason);
    }
}

/*
===========================================================
Add changelog function
===========================================================
*/

function cancelAddChangelog() {
    $("#addChangelogArea").html(`
        <p style="text-align: right">
            <a href="javascript:void(0)" onclick="addChangelog()" class="changelog-btn"><i class="fa fa-plus"></i>&nbsp;&nbsp;Thêm nhật ký mới</a>
        </p>
    `);
}

function addChangelog() {
    $("#addChangelogArea").html(`
        <p><input type="text" class="form-control" id="newChangelogVersion" placeholder="Số phiên bản (ví dụ như 1.0.0 hoặc 1.0.1)" /></p>
        <p><textarea class="form-control" id="newChangelogDescription" style="height: 300px" placeholder="Nội dung cập nhật (hỗ trợ Markdown)" /></p>
        <p style="text-align: right" id="changelogButtonList">
            <a href="javascript:void(0)" onclick="processAddChangelog()" class="changelog-btn"><i class="fa fa-plus"></i>&nbsp;&nbsp;Thêm nhật ký mới</a>
            <a href="javascript:void(0)" onclick="cancelAddChangelog()" class="changelog-btn"><i class="fa fa-times"></i>&nbsp;&nbsp;Huỷ bỏ</a>
        </p>
    `);
}

async function processAddChangelog() {
    var version = $("#newChangelogVersion").val(),
        description = $("#newChangelogDescription").val();
    if (!version || !description) return toastr.error("Vui lòng nhập đầy đủ thông tin.", "Lỗi");
    $("#changelogButtonList").html(`<i>Đang thêm nhật ký cập nhật mới, vui lòng đợi...</i>`);
    try {
        var response = await apiRequest({
            url: "/api/games/changelogs",
            type: "PUT",
            cache: false,
            contentType: false,
            processData: false,
            data: JSON.stringify({
                game_id: gameId,
                version,
                description
            }),
            json: true
        });
        if (response?.success) {
            toastr.success(response.message, "Thông báo");
            if ($("#noChangelogText")) $("#noChangelogText").html("");
            $("#changelogs").html(`${response.data}${$("#changelogs").html()}`);
            cancelAddChangelog();
        }
    }
    catch (err) {
        console.error(err);
        $("#changelogButtonList").html(`
            <a href="javascript:void(0)" onclick="processAddChangelog()" class="changelog-btn"><i class="fa fa-plus"></i>&nbsp;&nbsp;Thêm nhật ký mới</a>
            <a href="javascript:void(0)" onclick="cancelAddChangelog()" class="changelog-btn"><i class="fa fa-times"></i>&nbsp;&nbsp;Huỷ bỏ</a>
        `);
    }
}

/*
===========================================================
Edit changelogs function
===========================================================
*/

async function editChangelog(id) {
    modal.title = `Chỉnh Sửa Mô Tả`;
    modal.body = `
        <p><b>Mô tả cập nhật mới:</b></p>
        <p><textarea class="form-control" id="editChangelogDescription" style="height: 300px" /></p>
    `;
    modal.footer = `
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy bỏ</button>
        <button type="button" class="btn btn-primary" style="background-color: #af1932; border: 1px solid rgb(209, 55, 81)" onclick="processEditChangelog(${id})">Chỉnh sửa</button>
    `;
    modal.update();
    modal.show();
    var response = await apiRequest({
        url: `/api/games/changelogs?game_id=${id}`,
        type: "GET",
        cache: false,
        contentType: false,
        processData: false
    });
    if (response?.success) $("#editChangelogDescription").val(response.data[0].description);
}

function updateEditChangelog(id, description = "") {
    modal.body = `
        <p><b>Mô tả cập nhật mới:</b></p>
        <p><textarea class="form-control" id="editChangelogDescription" style="height: 300px">${description}</textarea></p>
    `;
    modal.footer = `
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy bỏ</button>
        <button type="button" class="btn btn-primary" style="background-color: #af1932; border: 1px solid rgb(209, 55, 81)" onclick="processEditChangelog(${id})">Chỉnh sửa</button>
    `;
    modal.update();
}

async function processEditChangelog(id) {
    var description = $("#editChangelogDescription").val();
    modal.body = `<p><i>Đang thực hiện sửa đổi, bạn vui lòng chờ một lát...</i></p>`;
    modal.footer = ``;
    modal.update();
    try {
        var response = await apiRequest({
            url: "/api/games/changelogs",
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,
            data: JSON.stringify({
                id,
                description
            }),
            json: true
        });
        if (response?.success) {
            toastr.success(response.message, "Thông báo");
            $(`#changelog-${id}`).html(response.data);
            modal.hide();
        }
        else updateEditChangelog(id, description);
    }
    catch (err) {
        console.error(err);
        updateEditChangelog(id, description);
    }
}

/*
===========================================================
Delete changelogs function
===========================================================
*/

async function deleteChangelog(id) {
    if (!confirm("Xác nhận xoá nội dung cập nhật này?")) return;
    var response = await apiRequest({
        url: "/api/games/changelogs",
        type: "DELETE",
        cache: false,
        contentType: false,
        processData: false,
        data: JSON.stringify({
            id
        }),
        json: true
    });
    if (response?.success) {
        toastr.success(response.message, "Thông báo");
        $(`#changelog-${id}`).remove();
    }
}