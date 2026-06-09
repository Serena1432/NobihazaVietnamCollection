var requests = {}, chunkName = {}, fileName = {};
const CHUNK_SIZE = 8388608;

function hashString(string) {
    let hash = 0;
    if (string.length == 0) return hash;
    for (i = 0; i < string.length; i++) {
        char = string.charCodeAt(i);
        hash = ((hash << 5) - hash) + char;
        hash = hash & hash;
    }
    return hash;
}

async function apiRequest(data, code) {
    return new Promise((resolve, reject) => {
        var temp = $.ajax(data)
        .done(function(data) {
            resolve(data);
        })
        .fail(function(data) {
            var res = data.responseText;
            try {res = JSON.parse(data.responseText)} catch {}
            if (typeof(res) == "object") toastr.error(res.message, `Lỗi ${res.status_code}`)
            else if (code && !cancelling[code]) toastr.error("Không thể kết nối tới máy chủ. Vui lòng kiểm tra thông tin lỗi bằng nút F12.", `Lỗi không xác định`);
            reject(res);
        });
        if (code) requests[code] = temp;
    });
}

/**
 * @param {File} file
 * @param {HTMLElement} progressBar
 */
async function uploadFile(file, progressBar, code) {
    var progressBarInner = progressBar.getElementsByClassName("progress-bar")[0] || progressBar.getElementsByClassName("progressbar")[0];
    if (progressBar && progressBarInner) {
        progressBar.style.display = "flex";
        progressBarInner.style.width = "0%";
    }
    const chunks = Math.ceil(file.size / CHUNK_SIZE);
    var beginCursor = 0, hash = `nbhzvn_checkpoint_${hashString(file.name).toString()}`;
    var checkpointJson = window.localStorage.getItem(hash);
    fileName[code] = hash;
    try {
        if (checkpointJson) {
            var checkpoint = JSON.parse(checkpointJson);
            if (checkpoint.size == file.size) {
                beginCursor = checkpoint.cursor + 1;
                chunkName[code] = checkpoint.chunkName;
                toastr.success("Tiếp tục tải lên phần còn lại của tệp tin này từ vị trí đã dừng trước đó.", "Thông báo");
            }
            else window.localStorage.removeItem(hash);
        }
    }
    catch (err) {console.error(err)}
    for (var cursor = beginCursor; cursor < chunks; cursor++) {
        const start = cursor * CHUNK_SIZE, end = Math.min(file.size, start + CHUNK_SIZE), chunk = file.slice(start, end);
        var formData = new FormData();
        formData.append("type", "chunk");
        formData.append("cursor", cursor);
        formData.append("chunks", chunks);
        formData.append("chunk", chunk);
        formData.append("file_name", file.name);
        if (chunkName[code]) formData.append("generated_name", chunkName[code]);
        try {
            var response = await apiRequest({
                url: "/api/upload",
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", (function(cursor, chunks, evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            var part = 100 / chunks;
                            if (progressBar && progressBarInner) progressBarInner.style.width = `${(part * cursor) + (part * percentComplete)}%`;
                        }
                    }).bind(this, cursor, chunks), false);
                    return xhr;
                },
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData
            }, code);
            if (response?.success) {
                if (cursor < chunks - 1) {
                    window.localStorage.setItem(hash, JSON.stringify({size: file.size, chunkName: response.data, cursor}));
                    chunkName[code] = response.data;
                }
                else {
                    if (progressBar && progressBarInner) progressBar.style.display = "none";
                    window.localStorage.removeItem(hash);
                    toastr.success(response.message, "Thông báo");
                    delete chunkName[code];
                    return response.data;
                }
            }
        }
        catch (err) {
            console.error(err);
            if (progressBar && progressBarInner) {
                progressBar.innerHTML = `<i style="color: #af1932">Lỗi!</i>`;
                progressBar.classList.remove("progress");
                progressBar.style.display = "inline";
            }
            return null;
        }
    }
}

let Pagination = {
    page: function(distantId = "") {
        return parseInt($(`#currentPage${distantId}`).val());
    },
    maxPages: function(distantId = "") {
        return parseInt($(`#currentPage${distantId}`).prop("max"));
    },
    previous: async function(apiUrl, distantId = "") {
        var page = this.page(distantId);
        if (page < 2) return null;
        page--;
        return await this.jump(apiUrl, page, distantId);
    },
    next: async function(apiUrl, distantId = "") {
        var page = this.page(distantId);
        if (page >= this.maxPages(distantId)) return null;
        page++;
        return await this.jump(apiUrl, page, distantId);
    },
    jump: async function(apiUrl, page = 1, distantId = "") {
        $(`#currentPage${distantId}`).val(page.toString());
        var response = await apiRequest({
            url: apiUrl.replaceAll("{page}", page.toString()),
            type: "GET",
            cache: false,
            contentType: false,
            processData: false
        });
        if (response?.success) return response.data;
        return null;
    }
}