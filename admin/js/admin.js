

function showAdminMessage(elId, text, isError) {
    var el = document.getElementById(elId);
    if (!el) return;
    el.textContent = text;
    el.className = "admin-message " + (isError ? "error" : "success");
    el.style.display = "block";
}

// ---------- Products ----------

function addProduct() {
    var title = document.getElementById("newProductTitle").value.trim();
    var categoryId = document.getElementById("newProductCategory").value;
    var sellerId = document.getElementById("newProductSeller").value;
    var price = document.getElementById("newProductPrice").value;
    var quantity = document.getElementById("newProductQuantity").value;
    var description = document.getElementById("newProductDescription").value.trim();
    var btn = document.getElementById("addProductBtn");

    var form = new FormData();
    form.append("title", title);
    form.append("category_id", categoryId);
    form.append("seller_id", sellerId);
    form.append("price", price);
    form.append("quantity", quantity);
    form.append("description", description);

    var imageInput = document.getElementById("newProductImage");
    if (imageInput && imageInput.files && imageInput.files[0]) {
        form.append("image", imageInput.files[0]);
    }

    btn.disabled = true;

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            btn.disabled = false;
            if (request.status == 200 && request.responseText == "success") {
                window.location.reload();
            } else {
                showAdminMessage("addProductMessage", request.responseText || "Something went wrong.", true);
            }
        }
    };
    request.open("POST", "addProductProcess.php", true);
    request.send(form);
}

function bulkAddProducts() {
    var sellerId = document.getElementById("bulkProductSeller").value;
    var products = document.getElementById("bulkProductList").value;
    var btn = document.getElementById("bulkAddProductsBtn");

    btn.disabled = true;

    var form = new FormData();
    form.append("seller_id", sellerId);
    form.append("products", products);

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            btn.disabled = false;
            var response = request.responseText || "";
            if (request.status == 200 && response.indexOf("success") === 0) {
                showAdminMessage("bulkAddProductsMessage", response, false);
                setTimeout(function () { window.location.reload(); }, 1200);
            } else {
                showAdminMessage("bulkAddProductsMessage", response || "Something went wrong.", true);
            }
        }
    };
    request.open("POST", "bulkAddProductsProcess.php", true);
    request.send(form);
}

function editProduct() {
    var form = new FormData();
    form.append("listing_id", document.getElementById("editProductId").value);
    form.append("title", document.getElementById("editProductTitle").value.trim());
    form.append("category_id", document.getElementById("editProductCategory").value);
    form.append("price", document.getElementById("editProductPrice").value);
    form.append("quantity", document.getElementById("editProductQuantity").value);
    form.append("status", document.getElementById("editProductStatus").value);
    form.append("description", document.getElementById("editProductDescription").value.trim());

    var imageInput = document.getElementById("editProductImage");
    if (imageInput && imageInput.files && imageInput.files[0]) {
        form.append("image", imageInput.files[0]);
    }

    var btn = document.getElementById("saveProductBtn");
    btn.disabled = true;

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            btn.disabled = false;
            if (request.status == 200 && request.responseText == "success") {
                window.location = "products.php";
            } else {
                showAdminMessage("editProductMessage", request.responseText || "Something went wrong.", true);
            }
        }
    };
    request.open("POST", "editProductProcess.php", true);
    request.send(form);
}

// ---------- Users ----------

function toggleUserStatus(userId, newStatusId) {
    var form = new FormData();
    form.append("user_id", userId);
    form.append("status_id", newStatusId);

    var btn = document.getElementById("toggle-btn-" + userId);
    if (btn) btn.disabled = true;

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            if (request.status == 200 && request.responseText == "success") {
                var statusCell = document.getElementById("user-status-" + userId);
                var isNowBlocked = newStatusId == 2;
                statusCell.innerHTML = '<span class="admin-badge ' + (isNowBlocked ? "deactive" : "active") + '">'
                    + (isNowBlocked ? "Blocked" : "Active") + "</span>";

                if (btn) {
                    btn.textContent = isNowBlocked ? "Activate" : "Block";
                    btn.className = "admin-btn " + (isNowBlocked ? "" : "admin-btn-danger");
                    btn.setAttribute("onclick", "toggleUserStatus(" + userId + ", " + (isNowBlocked ? 1 : 2) + ")");
                    btn.disabled = false;
                }
            } else {
                alert(request.responseText || "Something went wrong.");
                if (btn) btn.disabled = false;
            }
        }
    };
    request.open("POST", "toggleUserStatusProcess.php", true);
    request.send(form);
}

function updateUser() {
    var form = new FormData();
    form.append("user_id", document.getElementById("editUserId").value);
    form.append("name", document.getElementById("editUserName").value.trim());
    form.append("phone", document.getElementById("editUserPhone").value.trim());
    form.append("role", document.getElementById("editUserRole").value);
    form.append("status_id", document.getElementById("editUserStatus").value);

    var btn = document.getElementById("saveUserBtn");
    btn.disabled = true;

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            btn.disabled = false;
            if (request.status == 200 && request.responseText == "success") {
                window.location = "users.php";
            } else {
                showAdminMessage("editUserMessage", request.responseText || "Something went wrong.", true);
            }
        }
    };
    request.open("POST", "updateUserProcess.php", true);
    request.send(form);
}

// ---------- Categories ----------

function addCategory() {
    var form = new FormData();
    form.append("name", document.getElementById("newCategoryName").value.trim());
    form.append("description", document.getElementById("newCategoryDescription").value.trim());

    var imageInput = document.getElementById("newCategoryImage");
    if (imageInput && imageInput.files && imageInput.files[0]) {
        form.append("image", imageInput.files[0]);
    }

    var btn = document.getElementById("addCategoryBtn");
    btn.disabled = true;

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            btn.disabled = false;
            if (request.status == 200 && request.responseText == "success") {
                window.location.reload();
            } else {
                showAdminMessage("addCategoryMessage", request.responseText || "Something went wrong.", true);
            }
        }
    };
    request.open("POST", "addCategoryProcess.php", true);
    request.send(form);
}

// Deleting is blocked server-side while the category still has products in
// it (see deleteCategoryProcess.php), so this can't silently orphan them.
function deleteCategory(categoryId) {
    if (!confirm("Delete this category? This can't be undone.")) {
        return;
    }

    var form = new FormData();
    form.append("category_id", categoryId);

    var btn = document.getElementById("delete-category-btn-" + categoryId);
    if (btn) btn.disabled = true;

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            if (request.status == 200 && request.responseText == "success") {
                var row = document.getElementById("category-row-" + categoryId);
                if (row) row.remove();
            } else {
                alert(request.responseText || "Something went wrong.");
                if (btn) btn.disabled = false;
            }
        }
    };
    request.open("POST", "deleteCategoryProcess.php", true);
    request.send(form);
}

// ---------- Advertisements ----------

function addAdvertisement() {
    var form = new FormData();
    form.append("title", document.getElementById("newAdTitle").value.trim());
    form.append("description", document.getElementById("newAdDescription").value.trim());
    form.append("image_url", document.getElementById("newAdImageUrl").value.trim());
    form.append("link_url", document.getElementById("newAdLinkUrl").value.trim());
    form.append("start_date", document.getElementById("newAdStartDate").value);
    form.append("end_date", document.getElementById("newAdEndDate").value);

    var btn = document.getElementById("addAdBtn");
    btn.disabled = true;

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            btn.disabled = false;
            if (request.status == 200 && request.responseText == "success") {
                window.location.reload();
            } else {
                showAdminMessage("addAdMessage", request.responseText || "Something went wrong.", true);
            }
        }
    };
    request.open("POST", "addAdvertisementProcess.php", true);
    request.send(form);
}

function editAdvertisement() {
    var form = new FormData();
    form.append("advertisement_id", document.getElementById("editAdId").value);
    form.append("title", document.getElementById("editAdTitle").value.trim());
    form.append("description", document.getElementById("editAdDescription").value.trim());
    form.append("image_url", document.getElementById("editAdImageUrl").value.trim());
    form.append("link_url", document.getElementById("editAdLinkUrl").value.trim());
    form.append("start_date", document.getElementById("editAdStartDate").value);
    form.append("end_date", document.getElementById("editAdEndDate").value);
    form.append("is_active", document.getElementById("editAdStatus").value);

    var btn = document.getElementById("saveAdBtn");
    btn.disabled = true;

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            btn.disabled = false;
            if (request.status == 200 && request.responseText == "success") {
                window.location = "advertisements.php";
            } else {
                showAdminMessage("editAdMessage", request.responseText || "Something went wrong.", true);
            }
        }
    };
    request.open("POST", "editAdvertisementProcess.php", true);
    request.send(form);
}

function toggleAdvertisementStatus(adId, newActiveValue) {
    var form = new FormData();
    form.append("advertisement_id", adId);
    form.append("is_active", newActiveValue);

    var btn = document.getElementById("toggle-ad-btn-" + adId);
    if (btn) btn.disabled = true;

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            if (request.status == 200 && request.responseText == "success") {
                var isNowActive = newActiveValue == 1;
                var statusCell = document.getElementById("ad-status-" + adId);
                statusCell.innerHTML = '<span class="admin-badge ' + (isNowActive ? "active" : "deactive") + '">'
                    + (isNowActive ? "Active" : "Inactive") + "</span>";

                if (btn) {
                    btn.textContent = isNowActive ? "Deactivate" : "Activate";
                    btn.className = "admin-btn " + (isNowActive ? "admin-btn-danger" : "");
                    btn.setAttribute("onclick", "toggleAdvertisementStatus(" + adId + ", " + (isNowActive ? 0 : 1) + ")");
                    btn.disabled = false;
                }
            } else {
                alert(request.responseText || "Something went wrong.");
                if (btn) btn.disabled = false;
            }
        }
    };
    request.open("POST", "toggleAdvertisementStatusProcess.php", true);
    request.send(form);
}

// ---------- Messages ----------

function markMessageRead(messageId) {
    var form = new FormData();
    form.append("message_id", messageId);

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            if (request.status == 200 && request.responseText == "success") {
                var statusCell = document.getElementById("message-status-" + messageId);
                statusCell.innerHTML = '<span class="admin-badge read">Read</span>';
                var row = document.getElementById("message-row-" + messageId);
                var actionBtn = row.querySelector(".admin-table-actions button");
                if (actionBtn) actionBtn.remove();
            } else {
                alert(request.responseText || "Something went wrong.");
            }
        }
    };
    request.open("POST", "markMessageReadProcess.php", true);
    request.send(form);
}

// ---------- News ----------

function addNews() {
    var form = new FormData();
    form.append("title", document.getElementById("newNewsTitle").value.trim());
    form.append("content", document.getElementById("newNewsContent").value.trim());
    form.append("image_url", document.getElementById("newNewsImageUrl").value.trim());

    var btn = document.getElementById("addNewsBtn");
    btn.disabled = true;

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            btn.disabled = false;
            if (request.status == 200 && request.responseText == "success") {
                window.location.reload();
            } else {
                showAdminMessage("addNewsMessage", request.responseText || "Something went wrong.", true);
            }
        }
    };
    request.open("POST", "addNewsProcess.php", true);
    request.send(form);
}

function editNews() {
    var form = new FormData();
    form.append("news_id", document.getElementById("editNewsId").value);
    form.append("title", document.getElementById("editNewsTitle").value.trim());
    form.append("content", document.getElementById("editNewsContent").value.trim());
    form.append("image_url", document.getElementById("editNewsImageUrl").value.trim());
    form.append("is_active", document.getElementById("editNewsStatus").value);

    var btn = document.getElementById("saveNewsBtn");
    btn.disabled = true;

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            btn.disabled = false;
            if (request.status == 200 && request.responseText == "success") {
                window.location = "news.php";
            } else {
                showAdminMessage("editNewsMessage", request.responseText || "Something went wrong.", true);
            }
        }
    };
    request.open("POST", "editNewsProcess.php", true);
    request.send(form);
}

function toggleNewsStatus(newsId, newActiveValue) {
    var form = new FormData();
    form.append("news_id", newsId);
    form.append("is_active", newActiveValue);

    var btn = document.getElementById("toggle-news-btn-" + newsId);
    if (btn) btn.disabled = true;

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            if (request.status == 200 && request.responseText == "success") {
                var isNowActive = newActiveValue == 1;
                var statusCell = document.getElementById("news-status-" + newsId);
                statusCell.innerHTML = '<span class="admin-badge ' + (isNowActive ? "active" : "deactive") + '">'
                    + (isNowActive ? "Active" : "Inactive") + "</span>";

                if (btn) {
                    btn.textContent = isNowActive ? "Deactivate" : "Activate";
                    btn.className = "admin-btn " + (isNowActive ? "admin-btn-danger" : "");
                    btn.setAttribute("onclick", "toggleNewsStatus(" + newsId + ", " + (isNowActive ? 0 : 1) + ")");
                    btn.disabled = false;
                }
            } else {
                alert(request.responseText || "Something went wrong.");
                if (btn) btn.disabled = false;
            }
        }
    };
    request.open("POST", "toggleNewsStatusProcess.php", true);
    request.send(form);
}

// ---------- Orders ----------

function toggleOrderStatus(orderId, newStatus) {
    var form = new FormData();
    form.append("order_id", orderId);
    form.append("status", newStatus);

    var btn = document.getElementById("toggle-order-btn-" + orderId);
    if (btn) btn.disabled = true;

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            if (request.status == 200 && request.responseText == "success") {
                var isNowCompleted = newStatus === "completed";
                var statusCell = document.getElementById("order-status-" + orderId);
                statusCell.innerHTML = '<span class="admin-badge ' + (isNowCompleted ? "active" : "unread") + '">'
                    + (isNowCompleted ? "completed" : "pending") + "</span>";

                if (btn) {
                    btn.textContent = isNowCompleted ? "Mark Pending" : "Mark Completed";
                    btn.setAttribute("onclick", "toggleOrderStatus(" + orderId + ", '" + (isNowCompleted ? "pending" : "completed") + "')");
                    btn.disabled = false;
                }
            } else {
                alert(request.responseText || "Something went wrong.");
                if (btn) btn.disabled = false;
            }
        }
    };
    request.open("POST", "toggleOrderStatusProcess.php", true);
    request.send(form);
}

// Deleting is destructive (removes the order's items and payment record
// too), so this is the one admin action that confirms first.
function deleteOrder(orderId, redirectAfter) {
    if (!confirm("Remove order #" + orderId + "? This can't be undone.")) {
        return;
    }

    var form = new FormData();
    form.append("order_id", orderId);

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            if (request.status == 200 && request.responseText == "success") {
                if (redirectAfter) {
                    window.location = "orders.php";
                } else {
                    var row = document.getElementById("order-row-" + orderId);
                    if (row) row.remove();
                }
            } else {
                alert(request.responseText || "Something went wrong.");
            }
        }
    };
    request.open("POST", "deleteOrderProcess.php", true);
    request.send(form);
}
