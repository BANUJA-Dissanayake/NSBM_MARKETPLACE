function login() {
    var email = document.getElementById("email");
    var password = document.getElementById("password");
    var rememberme = document.getElementById("rememberme");

    var form = new FormData();
    form.append("email", email.value);
    form.append("password", password.value);
    form.append("rememberme", rememberme.checked);

    var request = new XMLHttpRequest();

    request.onreadystatechange = function () {
        if (request.status == 200 && request.readyState == 4) {
            var response = request.responseText;

            if (response == "success") {
                window.location = "index.php";
            } else {
                alert(response);
            }
        }
    }

    request.open("POST", "loginProcess.php", true);
    request.send(form);
}

function register() {
    var name = document.getElementById("reg-name");
    var email = document.getElementById("reg-email");
    var password = document.getElementById("reg-password");
    var phone = document.getElementById("reg-phone");
    var cpassword = document.getElementById("reg-cpassword");

    var form = new FormData();
    form.append("name", name.value);
    form.append("email", email.value);
    form.append("password", password.value);
    form.append("phone", phone.value);
    form.append("cpassword", cpassword.value);

    var request = new XMLHttpRequest();

    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            if (request.status == 200) {
                var response = request.responseText;
                if (response == "success") {
                    window.location = "index.php";
                } else {
                    alert(response);
                }
            } else {
                alert("Something went wrong. Please try again.");
            }
        }
    }

    request.open("POST", "registerProcess.php", true);
    request.send(form);
}

// Used on product.php (and anywhere else with an "Add to cart" button).
// quantityInputId lets the same function work whether or not the page has
// a quantity <input> next to the button.
function addToCart(listingId, quantityInputId) {
    var quantity = 1;
    if (quantityInputId) {
        var qtyEl = document.getElementById(quantityInputId);
        if (qtyEl && qtyEl.value) {
            quantity = parseInt(qtyEl.value, 10) || 1;
        }
    }

    var form = new FormData();
    form.append("listing_id", listingId);
    form.append("quantity", quantity);

    var request = new XMLHttpRequest();

    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            if (request.status == 200) {
                var response = request.responseText;
                if (response == "success") {
                    alert("Added to cart.");
                } else if (response == "login_required") {
                    window.location = "register.php";
                } else {
                    alert(response);
                }
            } else {
                alert("Something went wrong. Please try again.");
            }
        }
    };

    request.open("POST", "addToCartProcess.php", true);
    request.send(form);
}

// Used on product.php's "Add to wishlist" button.
function addToWishlist(listingId) {
    var form = new FormData();
    form.append("listing_id", listingId);

    var request = new XMLHttpRequest();

    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            if (request.status == 200) {
                var response = request.responseText;
                if (response == "success") {
                    alert("Added to wishlist.");
                } else if (response == "login_required") {
                    window.location = "register.php";
                } else if (response == "already_in_wishlist") {
                    alert("This item is already in your wishlist.");
                } else {
                    alert(response);
                }
            } else {
                alert("Something went wrong. Please try again.");
            }
        }
    };

    request.open("POST", "addToWishlistProcess.php", true);
    request.send(form);
}
// Cart page behaviour: +/- quantity, remove item, checkout.
// Every action posts to a backend endpoint via XMLHttpRequest, the same
// pattern used by login()/register() in main.js.

function updateCartItemQuantity(cartItemId, action, cardEl) {
    var form = new FormData();
    form.append("cart_item_id", cartItemId);
    form.append("action", action);

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            var response = request.responseText;

            if (response == "removed") {
                cardEl.remove();
            } else if (response == "login_required") {
                window.location = "register.php";
                return;
            } else if (!isNaN(response) && response.trim() !== "") {
                cardEl.querySelector(".quantity").textContent = response;
                var price = parseFloat(cardEl.getAttribute("data-price"));
                var newQuantity = parseInt(response, 10);
                cardEl.querySelector(".item-total").textContent = "Rs. " + (price * newQuantity).toLocaleString();
            } else {
                alert(response);
                return;
            }
            recalcCartTotals();
        }
    };
    request.open("POST", "updateCartProcess.php", true);
    request.send(form);
}

function removeCartItem(cartItemId, cardEl) {
    var form = new FormData();
    form.append("cart_item_id", cartItemId);

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            var response = request.responseText;
            if (response == "success") {
                cardEl.remove();
                recalcCartTotals();
            } else if (response == "login_required") {
                window.location = "register.php";
            } else {
                alert(response);
            }
        }
    };
    request.open("POST", "removeCartProcess.php", true);
    request.send(form);
}

function recalcCartTotals() {
    var items = document.querySelectorAll(".cart-item");
    var subtotal = 0;

    items.forEach(function (item) {
        var price = parseFloat(item.getAttribute("data-price"));
        var quantityEl = item.querySelector(".quantity");
        var quantity = quantityEl ? parseInt(quantityEl.textContent, 10) : 0;
        subtotal += price * quantity;
    });

    var subtotalEl = document.getElementById("cartSubtotal");
    var totalEl = document.getElementById("cartTotal");
    if (subtotalEl) subtotalEl.textContent = "Rs. " + subtotal.toLocaleString();
    if (totalEl) totalEl.textContent = "Rs. " + subtotal.toLocaleString();

    var checkoutBtn = document.querySelector(".checkout-button");
    var emptyMsg = document.querySelector(".cart-empty");
    if (items.length === 0) {
        if (emptyMsg) emptyMsg.hidden = false;
        if (checkoutBtn) checkoutBtn.disabled = true;
    }
}

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".cart-item").forEach(function (item) {
        var cartItemId = item.getAttribute("data-cart-item-id");
        var minusBtn = item.querySelector(".quantity-minus");
        var plusBtn = item.querySelector(".quantity-plus");
        var removeBtn = item.querySelector(".remove-cart-item");

        if (minusBtn) {
            minusBtn.addEventListener("click", function () {
                updateCartItemQuantity(cartItemId, "decrease", item);
            });
        }
        if (plusBtn) {
            plusBtn.addEventListener("click", function () {
                updateCartItemQuantity(cartItemId, "increase", item);
            });
        }
        if (removeBtn) {
            removeBtn.addEventListener("click", function () {
                removeCartItem(cartItemId, item);
            });
        }
    });
});

// Reads the current filter controls, sends them to loadProduct.php over
// XMLHttpRequest (same pattern as login()/register() in main.js), and swaps
// in the returned product cards plus the pagination controls that come back
// with them (pagination.php's renderPagination(), rendered server-side).
// Changing a filter always jumps back to page 1; paging with
// goToProductPage() keeps the current filters and just changes the page.
function loadProducts(resetPage) {
    var grid = document.getElementById("productGrid");

    var searchEl = document.getElementById("productSearch");
    var priceFilterEl = document.getElementById("priceFilter");
    var sortEl = document.getElementById("sortProducts");
    var categoryEl = document.querySelector('input[name="category"]:checked');

    var search = searchEl ? searchEl.value : "";
    var category = categoryEl ? categoryEl.value : "all";
    var sort = sortEl ? sortEl.value : "featured";

    var minPrice = "";
    var maxPrice = "";
    var priceFilter = priceFilterEl ? priceFilterEl.value : "all";
    if (priceFilter === "under-2000") {
        maxPrice = 2000;
    } else if (priceFilter === "2000-3000") {
        minPrice = 2000;
        maxPrice = 3000;
    } else if (priceFilter === "over-3000") {
        minPrice = 3000;
    }

    if (resetPage !== false) {
        grid.setAttribute("data-page", "1");
    }
    var page = parseInt(grid.getAttribute("data-page"), 10) || 1;

    var form = new FormData();
    form.append("search", search);
    form.append("category", category);
    form.append("min_price", minPrice);
    form.append("max_price", maxPrice);
    form.append("sort", sort);
    form.append("page", page);

    var request = new XMLHttpRequest();

    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            if (request.status == 200) {
                var marker = "<!--PAGINATION-->";
                var splitAt = request.responseText.indexOf(marker);
                var cardsHtml = splitAt === -1 ? request.responseText : request.responseText.slice(0, splitAt);
                var paginationHtml = splitAt === -1 ? "" : request.responseText.slice(splitAt + marker.length);

                grid.innerHTML = cardsHtml;

                var paginationContainer = document.getElementById("shopPagination");
                if (paginationContainer) {
                    paginationContainer.innerHTML = paginationHtml;
                }

                var resultsCount = document.getElementById("resultsCount");
                if (resultsCount) {
                    var paginationNav = paginationContainer ? paginationContainer.querySelector(".shop-pagination") : null;
                    var total = paginationNav ? paginationNav.getAttribute("data-total-items") : null;
                    resultsCount.textContent = (total !== null ? total : grid.querySelectorAll(".shop-product-card").length) + " results";
                }
            } else {
                alert("Something went wrong loading products. Please try again.");
            }
        }
    };

    request.open("POST", "loadProduct.php", true);
    request.send(form);
}

// Called by the onclick handlers pagination.php renders onto each page
// button/arrow.
function goToProductPage(page) {
    var grid = document.getElementById("productGrid");
    if (!grid || page < 1) {
        return;
    }
    grid.setAttribute("data-page", page);
    loadProducts(false);
    grid.scrollIntoView({ behavior: "smooth", block: "start" });
}

document.addEventListener("DOMContentLoaded", function () {
    var searchEl = document.getElementById("productSearch");
    var priceFilterEl = document.getElementById("priceFilter");
    var sortEl = document.getElementById("sortProducts");
    var clearBtn = document.getElementById("clearFilters");

    if (priceFilterEl) {
        priceFilterEl.addEventListener("change", function () { loadProducts(); });
    }
    if (sortEl) {
        sortEl.addEventListener("change", function () { loadProducts(); });
    }

    document.querySelectorAll('input[name="category"]').forEach(function (radio) {
        radio.addEventListener("change", function () { loadProducts(); });
    });

    if (clearBtn) {
        clearBtn.addEventListener("click", function () {
            if (searchEl) searchEl.value = "";
            if (priceFilterEl) priceFilterEl.value = "all";
            if (sortEl) sortEl.value = "featured";
            var allRadio = document.querySelector('input[name="category"][value="all"]');
            if (allRadio) allRadio.checked = true;
            loadProducts();
        });
    }
});

// Wishlist page behaviour: remove item, move item to cart.

function removeWishlistItem(wishlistItemId, cardEl) {
    var form = new FormData();
    form.append("wishlist_item_id", wishlistItemId);

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            var response = request.responseText;
            if (response == "success") {
                cardEl.remove();
                var remaining = document.querySelectorAll(".wishlist-card").length;
                var emptyMsg = document.querySelector(".wishlist-empty");
                if (remaining === 0 && emptyMsg) {
                    emptyMsg.hidden = false;
                }
            } else if (response == "login_required") {
                window.location = "register.php";
            } else {
                alert(response);
            }
        }
    };
    request.open("POST", "removeWishlistProcess.php", true);
    request.send(form);
}

function addWishlistItemToCart(listingId) {
    var form = new FormData();
    form.append("listing_id", listingId);
    form.append("quantity", 1);

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            var response = request.responseText;
            if (response == "success") {
                alert("Added to cart.");
            } else if (response == "login_required") {
                window.location = "register.php";
            } else {
                alert(response);
            }
        }
    };
    request.open("POST", "addToCartProcess.php", true);
    request.send(form);
}

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".wishlist-card").forEach(function (card) {
        var wishlistItemId = card.getAttribute("data-wishlist-item-id");
        var listingId = card.getAttribute("data-listing-id");
        var removeBtn = card.querySelector(".remove-item");
        var addToCartBtn = card.querySelector(".add-to-cart-btn");

        if (removeBtn) {
            removeBtn.addEventListener("click", function () {
                removeWishlistItem(wishlistItemId, card);
            });
        }
        if (addToCartBtn) {
            addToCartBtn.addEventListener("click", function () {
                addWishlistItemToCart(listingId);
            });
        }
    });
});

function placeOrder() {
    var name = document.getElementById("shippingName");
    var phone = document.getElementById("shippingPhone");
    var address = document.getElementById("shippingAddress");
    var city = document.getElementById("shippingCity");
    var paymentMethod = document.querySelector('input[name="paymentMethod"]:checked');
    var errorEl = document.getElementById("checkoutError");
    var placeBtn = document.getElementById("placeOrderBtn");

    if (errorEl) {
        errorEl.hidden = true;
        errorEl.textContent = "";
    }

    if (!name.value.trim() || !phone.value.trim() || !address.value.trim() || !city.value.trim()) {
        if (errorEl) {
            errorEl.textContent = "Please fill in all shipping details.";
            errorEl.hidden = false;
        }
        return;
    }

    var form = new FormData();
    form.append("shipping_name", name.value.trim());
    form.append("shipping_phone", phone.value.trim());
    form.append("shipping_address", address.value.trim());
    form.append("shipping_city", city.value.trim());
    form.append("payment_method", paymentMethod ? paymentMethod.value : "Cash on Delivery");

    if (placeBtn) {
        placeBtn.disabled = true;
        placeBtn.textContent = "Placing order...";
    }

    function resetButton() {
        if (placeBtn) {
            placeBtn.disabled = false;
            placeBtn.textContent = "Place order";
        }
    }

    function showError(msg) {
        if (errorEl) {
            errorEl.textContent = msg;
            errorEl.hidden = false;
        } else {
            alert(msg);
        }
    }

    if (paymentMethod.value === "Online Payment") {
        var xhttp = new XMLHttpRequest();

        xhttp.onreadystatechange = function () {
            if (xhttp.readyState !== 4) return;
            resetButton();

            if (xhttp.status !== 200) {
                showError("Something went wrong. Please try again.");
                return;
            }

            var obj = JSON.parse(xhttp.responseText);

            // Payment completed. It can be a successful failure.
            payhere.onCompleted = function onCompleted(orderId) {
                console.log("Payment completed. OrderID:" + orderId);
                // Note: validate the payment and show success or failure page to the customer
            };

            // Payment window closed
            payhere.onDismissed = function onDismissed() {
                // Note: Prompt user to pay again or show an error page
                console.log("Payment dismissed");
            };

            // Error occurred
            payhere.onError = function onError(error) {
                // Note: show an error page
                console.log("Error:" + error);
            };

            // Put the payment variables here
            var payment = {
                "sandbox": true,
                "merchant_id": obj["merchant_id"],
                "return_url": "http://localhost/NSBMMarketplace/index.php",     // Important
                "cancel_url": "http://localhost/NSBMMarketplace/index.php",     // Important
                "notify_url": "http://sample.com/notify",
                "order_id": obj["order_id"],
                "items": obj["items"],
                "amount": obj["amount"],
                "currency": obj["currency"],
                "hash": obj["hash"],
                "first_name": obj["first_name"],
                "last_name": obj["last_name"],
                "email": obj["email"],
                "phone": obj["phone"],
                "address": obj["address"],
                "city": obj["city"],
                "country": obj["country"],
                "delivery_address": obj["delivery_address"],
                "delivery_city": obj["delivery_city"],
                "delivery_country": obj["country"],
                "custom_1": "",
                "custom_2": ""
            };

            payhere.startPayment(payment);
        };

        xhttp.open("POST", "payhereProcess.php", true);
        xhttp.send(form);

    } else {

        var request = new XMLHttpRequest();
        request.onreadystatechange = function () {
            if (request.readyState !== 4) return;
            resetButton();

            if (request.status === 200) {
                var response = request.responseText;
                if (response.indexOf("order_placed:") === 0) {
                    window.location = "order-details.php?order=" + response.split(":")[1];
                } else if (response === "login_required") {
                    window.location = "register.php";
                } else {
                    showError(response);
                }
            } else {
                showError("Something went wrong. Please try again.");
            }
        };

        request.open("POST", "checkoutProcess.php", true);
        request.send(form);
    }
}

// Edit profile page: updates name/phone and, optionally, password.
function updateProfile() {
    var name = document.getElementById("editName");
    var phone = document.getElementById("editPhone");
    var password = document.getElementById("editPassword");
    var cpassword = document.getElementById("editCPassword");
    var messageEl = document.getElementById("editProfileMessage");
    var saveBtn = document.getElementById("saveProfileBtn");

    if (messageEl) {
        messageEl.hidden = true;
        messageEl.classList.remove("form-error", "form-success");
        messageEl.textContent = "";
    }

    var form = new FormData();
    form.append("name", name.value.trim());
    form.append("phone", phone.value.trim());
    form.append("password", password.value);
    form.append("cpassword", cpassword.value);

    if (saveBtn) {
        saveBtn.disabled = true;
    }

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            if (request.status == 200) {
                var response = request.responseText;
                if (response == "success") {
                    if (messageEl) {
                        messageEl.textContent = "Profile updated.";
                        messageEl.classList.add("form-success");
                        messageEl.hidden = false;
                    }
                    password.value = "";
                    cpassword.value = "";
                    setTimeout(function () {
                        window.location = "profile.php";
                    }, 900);
                } else if (response == "login_required") {
                    window.location = "register.php";
                } else if (messageEl) {
                    messageEl.textContent = response;
                    messageEl.classList.add("form-error");
                    messageEl.hidden = false;
                } else {
                    alert(response);
                }
            } else if (messageEl) {
                messageEl.textContent = "Something went wrong. Please try again.";
                messageEl.classList.add("form-error");
                messageEl.hidden = false;
            }

            if (saveBtn) {
                saveBtn.disabled = false;
            }
        }
    };

    request.open("POST", "updateProfileProcess.php", true);
    request.send(form);
}

// Contact page: sends the form to submitMessageProcess.php instead of a
// normal POST, so the page never reloads and the message gets saved (it
// used to just show a static "received" banner without saving anything).
function submitContactMessage(event) {
    event.preventDefault();

    var nameEl = document.getElementById("name");
    var emailEl = document.getElementById("email");
    var topicEl = document.getElementById("topic");
    var messageEl = document.getElementById("message");
    var successEl = document.getElementById("contactSuccess");
    var errorEl = document.getElementById("contactError");
    var submitBtn = document.getElementById("contactSubmitBtn");

    successEl.hidden = true;
    errorEl.hidden = true;

    var form = new FormData();
    form.append("name", nameEl.value.trim());
    form.append("email", emailEl.value.trim());
    form.append("topic", topicEl.value);
    form.append("message", messageEl.value.trim());

    submitBtn.disabled = true;

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            submitBtn.disabled = false;
            if (request.status == 200 && request.responseText == "success") {
                successEl.hidden = false;
                document.querySelector(".contact-form").reset();
            } else {
                errorEl.textContent = request.responseText || "Something went wrong. Please try again.";
                errorEl.hidden = false;
            }
        }
    };

    request.open("POST", "submitMessageProcess.php", true);
    request.send(form);

    return false;
}

// submit-news.php: lets a logged-in user submit an article for review.
// Submitted articles land inactive (is_active = 0) - an admin has to
// activate them from admin/news.php before they show on newspage.php.
function submitNewsArticle(event) {
    event.preventDefault();

    var titleEl = document.getElementById("newsTitle");
    var contentEl = document.getElementById("newsContent");
    var imageInput = document.getElementById("newsImage");
    var successEl = document.getElementById("newsSubmitSuccess");
    var errorEl = document.getElementById("newsSubmitError");
    var submitBtn = document.getElementById("newsSubmitBtn");

    successEl.hidden = true;
    errorEl.hidden = true;

    var form = new FormData();
    form.append("title", titleEl.value.trim());
    form.append("content", contentEl.value.trim());
    if (imageInput && imageInput.files && imageInput.files[0]) {
        form.append("image", imageInput.files[0]);
    }

    submitBtn.disabled = true;

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            submitBtn.disabled = false;
            if (request.status == 200 && request.responseText == "success") {
                successEl.hidden = false;
                document.getElementById("newsSubmitForm").reset();
            } else if (request.responseText == "login_required") {
                window.location = "register.php";
            } else {
                errorEl.textContent = request.responseText || "Something went wrong. Please try again.";
                errorEl.hidden = false;
            }
        }
    };
    request.open("POST", "submitNewsProcess.php", true);
    request.send(form);

    return false;
}

// Product page: Prev/Next arrows next to "Related Products" scroll the card
// row by roughly one card's width instead of jumping to the end.
function scrollRelatedProducts(direction) {
    var scrollEl = document.getElementById("relatedProductsScroll");
    if (!scrollEl) return;
    scrollEl.scrollBy({ left: direction * 240, behavior: "smooth" });
}

// Homepage: same idea for the "Latest News" row of cards.
function scrollLatestNews(direction) {
    var scrollEl = document.getElementById("latestNewsScroll");
    if (!scrollEl) return;
    scrollEl.scrollBy({ left: direction * 320, behavior: "smooth" });
}

// Homepage: ad banner carousel (Daraz-style). One full-width slide visible
// at a time, dots + arrows to navigate, and it auto-advances every 5s,
// pausing while the pointer is over the banner.
function scrollPromo(direction) {
    var track = document.getElementById("promoTrack");
    if (!track) return;
    var slideCount = track.children.length;
    if (!slideCount) return;
    var index = Math.round(track.scrollLeft / track.clientWidth);
    var next = (index + direction + slideCount) % slideCount;
    track.scrollTo({ left: track.clientWidth * next, behavior: "smooth" });
}

(function initPromoCarousel() {
    var track = document.getElementById("promoTrack");
    var dotsEl = document.getElementById("promoDots");
    if (!track || !dotsEl) return;

    var slides = track.children;
    for (var i = 0; i < slides.length; i++) {
        (function (index) {
            var dot = document.createElement("button");
            dot.type = "button";
            dot.className = "promo-dot" + (index === 0 ? " active" : "");
            dot.setAttribute("aria-label", "Go to banner " + (index + 1));
            dot.addEventListener("click", function () {
                track.scrollTo({ left: track.clientWidth * index, behavior: "smooth" });
            });
            dotsEl.appendChild(dot);
        })(i);
    }

    var scrollTimer;
    track.addEventListener("scroll", function () {
        clearTimeout(scrollTimer);
        scrollTimer = setTimeout(function () {
            var active = Math.round(track.scrollLeft / track.clientWidth);
            var dots = dotsEl.children;
            for (var d = 0; d < dots.length; d++) {
                dots[d].classList.toggle("active", d === active);
            }
        }, 100);
    });

    var autoplay = setInterval(function () { scrollPromo(1); }, 5000);
    track.addEventListener("mouseenter", function () { clearInterval(autoplay); });
    track.addEventListener("mouseleave", function () {
        autoplay = setInterval(function () { scrollPromo(1); }, 5000);
    });
})();
