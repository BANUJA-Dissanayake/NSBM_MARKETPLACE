<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";
include "../pagination.php"; // reuses paginationPageList() for the ellipsis-compacted page list

requireAdminOrRedirect();

$categories = [];
$catRs = Database::search("SELECT * FROM categories ORDER BY category_name ASC");
if ($catRs) {
    while ($row = $catRs->fetch_assoc()) {
        $categories[] = $row;
    }
}

$sellers = [];
$sellerRs = Database::search("SELECT user_id, name FROM `user` ORDER BY name ASC");
if ($sellerRs) {
    while ($row = $sellerRs->fetch_assoc()) {
        $sellers[] = $row;
    }
}

const ADMIN_PRODUCTS_PER_PAGE = 20;

// Starting point for the "Bulk add products" textarea below - 5 sample
// products per real category (Academic Services, Electronic, Event Tickets
// & Merchandise, Fashion, Food & Snacks, Gift Items, Hostel & Room
// Essentials, Sports & Fitness Gear, Stationery, Textbooks & Study
// Materials), reusing existing images/listings/*.jpg stock photos so every
// row has a picture without needing new uploads. Admin can edit/delete
// lines before submitting.
const BULK_PRODUCT_SEED = <<<'SEED'
Wireless Bluetooth Earbuds | Electronic | 4500 | 8 | Compact true wireless earbuds with 20-hour battery life, great for lectures and gym sessions. | photo-headphones.jpg
Mechanical Gaming Keyboard | Electronic | 6800 | 4 | RGB backlit mechanical keyboard with tactile switches, ideal for gaming and typing marathons. | photo-keyboard.jpg
Portable Bluetooth Speaker | Electronic | 3200 | 6 | Compact speaker with punchy bass, perfect for dorm room hangouts. | photo-speaker.jpg
Wireless Mouse | Electronic | 1500 | 12 | Ergonomic wireless mouse with silent clicks, works on any desk surface. | photo-mouse.jpg
Aluminum Laptop Stand | Electronic | 2200 | 10 | Adjustable aluminum laptop stand that improves posture during long study sessions. | photo-laptop.jpg
NSBM Hoodie - Unisex | Fashion | 3500 | 15 | Soft fleece hoodie with campus print, available in multiple colours. | photo-hoodie.jpg
Graphic Print T-Shirt | Fashion | 1800 | 20 | 100% cotton tee with a trendy graphic print, true to size. | photo-tshirt.jpg
Formal Polo Shirt | Fashion | 2400 | 10 | Smart-casual polo shirt, ideal for presentations and interviews. | photo-shirt.jpg
Campus Sneakers | Fashion | 5200 | 8 | Comfortable everyday sneakers built for long walks between lecture halls. | photo-shoes.jpg
Adjustable Snapback Cap | Fashion | 1200 | 18 | One-size-fits-all cap with embroidered logo, good sun protection outdoors. | photo-cap.jpg
Premium Ruled Notebook Set (3-Pack) | Stationery | 950 | 25 | Set of three 200-page ruled notebooks, perfect for lecture notes. | photo-notes.jpg
Scientific Calculator | Stationery | 2100 | 10 | Multi-function scientific calculator approved for most engineering modules. | photo-calculator.jpg
Sticky Notes & Highlighter Bundle | Stationery | 650 | 30 | Colour-coded sticky notes with 4 highlighters for organized studying. | photo-notes.jpg
Drafting Set with Geometry Tools | Stationery | 1300 | 12 | Complete drafting kit with compass, protractor, and rulers. | photo-notes.jpg
Desk Organizer Tray | Stationery | 1100 | 14 | Multi-compartment tray to keep pens, clips, and cards tidy. | photo-notes.jpg
Software Engineering Principles Textbook | Textbooks & Study Materials | 2800 | 5 | Comprehensive coursebook covering core software engineering concepts, lightly used. | photo-book.jpg
Data Structures & Algorithms Reference | Textbooks & Study Materials | 2600 | 6 | Well-annotated reference text, great for revision before exams. | photo-book.jpg
Financial Accounting Study Guide | Textbooks & Study Materials | 1900 | 7 | Concise study guide with worked examples for accounting modules. | photo-book.jpg
Past Paper Compilation (3 Years) | Textbooks & Study Materials | 850 | 15 | Compiled past exam papers with model answers across three academic years. | photo-book.jpg
Business Statistics Textbook | Textbooks & Study Materials | 2400 | 5 | Statistics textbook with practice problems, minimal highlighting. | photo-book.jpg
Assignment Proofreading Service | Academic Services | 800 | 20 | Get your assignments proofread for grammar, structure, and clarity within 24 hours. | photo-notes.jpg
One-on-One Maths Tutoring (Per Session) | Academic Services | 1500 | 30 | Personalized tutoring session covering calculus, statistics, or algebra. | photo-calculator.jpg
Document Printing & Binding | Academic Services | 300 | 50 | Same-day printing and spiral binding for reports and theses. | photo-notes.jpg
Presentation Slide Design Help | Academic Services | 1200 | 15 | Professional slide design service for group projects and vivas. | photo-notes.jpg
Group Project Coordination Templates | Academic Services | 500 | 25 | Ready-made Gantt chart and task tracker templates for group assignments. | photo-notes.jpg
NSBM Cultural Night Ticket | Event Tickets & Merchandise | 1000 | 40 | Entry ticket to this semester's cultural night, includes dinner. | photo-accessory.jpg
Sports Fest Spectator Pass | Event Tickets & Merchandise | 500 | 60 | All-day access pass to the inter-faculty sports fest. | photo-accessory.jpg
Limited Edition Event Wristband | Event Tickets & Merchandise | 350 | 45 | Collectible wristband from this year's freshers' welcome event. | photo-cap.jpg
Farewell Dinner Ticket | Event Tickets & Merchandise | 3500 | 20 | Ticket for the graduating batch's farewell dinner, includes buffet. | photo-accessory.jpg
Society Membership Badge & Lanyard | Event Tickets & Merchandise | 450 | 35 | Official membership badge and lanyard for campus societies. | photo-cap.jpg
Homemade Chocolate Brownies (Box of 6) | Food & Snacks | 900 | 20 | Freshly baked fudgy brownies, made to order for exam-week cravings. | photo-kitchen.jpg
Instant Noodles Variety Pack | Food & Snacks | 750 | 30 | Assorted instant noodle pack, perfect for late-night study sessions. | photo-kitchen.jpg
Reusable Snack Box Set | Food & Snacks | 1100 | 15 | Set of 3 stackable snack boxes, microwave and dishwasher safe. | photo-kitchen.jpg
Energy Bar Bundle (10 Pack) | Food & Snacks | 1400 | 18 | High-protein energy bars for between classes or gym sessions. | photo-kitchen.jpg
Fresh Fruit Juice Subscription (Weekly) | Food & Snacks | 1600 | 10 | Weekly delivery of freshly squeezed juice to your hostel block. | photo-kitchen.jpg
Personalized Photo Mug | Gift Items | 1200 | 20 | Custom-printed mug with your photo or message, great for gifting. | photo-accessory.jpg
Handmade Friendship Bracelet Set | Gift Items | 600 | 25 | Set of 5 handmade bracelets, perfect for batch mates. | photo-accessory.jpg
Scented Candle Gift Pack | Gift Items | 1500 | 12 | Set of 3 scented candles in a gift box, ideal for birthdays. | photo-bag.jpg
Custom Name Keychain | Gift Items | 450 | 40 | Laser-engraved keychain with a name or short message of your choice. | photo-accessory.jpg
Handmade Greeting Card Bundle | Gift Items | 500 | 30 | Pack of 5 handmade greeting cards for any occasion. | photo-bag.jpg
Foldable Study Table | Hostel & Room Essentials | 4200 | 6 | Compact foldable table, ideal for small hostel rooms. | photo-furniture.jpg
Electric Kettle (1.5L) | Hostel & Room Essentials | 2800 | 10 | Fast-boil electric kettle, safe for hostel room use. | photo-kitchen.jpg
Storage Organizer Rack | Hostel & Room Essentials | 3100 | 8 | 5-tier storage rack for clothes and books in shared rooms. | photo-furniture.jpg
LED Desk Lamp with USB Port | Hostel & Room Essentials | 1900 | 15 | Adjustable LED lamp with a built-in USB charging port. | photo-furniture.jpg
Bedside Caddy Organizer | Hostel & Room Essentials | 950 | 20 | Hanging bedside pocket organizer for phone, glasses, and other essentials. | photo-furniture.jpg
Non-Slip Yoga Mat | Sports & Fitness Gear | 2200 | 12 | Extra-thick non-slip yoga mat with a carry strap. | photo-shoes.jpg
Adjustable Dumbbell Set | Sports & Fitness Gear | 5500 | 6 | Pair of adjustable dumbbells, ideal for hostel room workouts. | photo-shoes.jpg
Running Shoes (Men/Women) | Sports & Fitness Gear | 6200 | 8 | Lightweight running shoes with a breathable mesh upper. | photo-shoes.jpg
Resistance Bands Set (5 Levels) | Sports & Fitness Gear | 1400 | 20 | Set of 5 resistance bands for strength and mobility training. | photo-shoes.jpg
Sports Water Bottle (1L) | Sports & Fitness Gear | 850 | 25 | Leak-proof 1L sports bottle with time markers. | photo-shoes.jpg
SEED;

$totalProducts = 0;
$countRs = Database::search("SELECT COUNT(*) AS total FROM listings");
if ($countRs && $row = $countRs->fetch_assoc()) {
    $totalProducts = (int) $row["total"];
}
$totalPages = max(1, (int) ceil($totalProducts / ADMIN_PRODUCTS_PER_PAGE));

$page = isset($_GET["page"]) ? (int) $_GET["page"] : 1;
$page = max(1, min($page, $totalPages));
$offset = ($page - 1) * ADMIN_PRODUCTS_PER_PAGE;

$products = [];
$productRs = Database::search("SELECT listings.*, categories.category_name, `user`.name AS seller_name
    FROM listings
    INNER JOIN categories ON listings.category_id = categories.category_id
    INNER JOIN `user` ON listings.seller_id = `user`.user_id
    ORDER BY listings.listing_id DESC
    LIMIT " . ADMIN_PRODUCTS_PER_PAGE . " OFFSET $offset");
if ($productRs) {
    while ($row = $productRs->fetch_assoc()) {
        $products[] = $row;
    }
}

$pageTitle = "Products";
$activeAdminPage = "products";
include "includes/admin-layout-top.php";
?>
<div class="admin-panel">
    <h2>Add a product</h2>
    <div id="addProductMessage" class="admin-message" style="display:none;"></div>
    <div class="admin-form-row">
        <div class="admin-field">
            <label for="newProductTitle">Title</label>
            <input id="newProductTitle" type="text" placeholder="e.g. Used Laptop - HP Pavilion">
        </div>
        <div class="admin-field">
            <label for="newProductCategory">Category</label>
            <select id="newProductCategory">
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo (int) $category["category_id"]; ?>"><?php echo htmlspecialchars($category["category_name"]); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="admin-field">
            <label for="newProductSeller">Seller</label>
            <select id="newProductSeller">
                <?php foreach ($sellers as $seller): ?>
                    <option value="<?php echo (int) $seller["user_id"]; ?>"><?php echo htmlspecialchars($seller["name"]); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="admin-field">
            <label for="newProductPrice">Price (Rs.)</label>
            <input id="newProductPrice" type="number" min="0" step="0.01" placeholder="0.00">
        </div>
        <div class="admin-field">
            <label for="newProductQuantity">Quantity</label>
            <input id="newProductQuantity" type="number" min="1" value="1">
        </div>
    </div>
    <div class="admin-field">
        <label for="newProductDescription">Description</label>
        <textarea id="newProductDescription" rows="3" placeholder="Short description shown on the product page"></textarea>
    </div>
    <div class="admin-field">
        <label for="newProductImage">Product image (optional)</label>
        <input id="newProductImage" type="file" accept="image/png,image/jpeg,image/gif,image/webp">
    </div>
    <button type="button" class="admin-btn" id="addProductBtn" onclick="addProduct()"><i class="fas fa-plus"></i> Add product</button>
</div>

<div class="admin-panel">
    <h2>Bulk add products</h2>
    <div id="bulkAddProductsMessage" class="admin-message" style="display:none;"></div>
    <div class="admin-form-row">
        <div class="admin-field">
            <label for="bulkProductSeller">Seller (used for every product below)</label>
            <select id="bulkProductSeller">
                <?php foreach ($sellers as $seller): ?>
                    <option value="<?php echo (int) $seller["user_id"]; ?>"><?php echo htmlspecialchars($seller["name"]); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="admin-field">
        <label for="bulkProductList">Products - one per line</label>
        <p class="admin-field-hint">Format: Title | Category | Price | Quantity | Description | Image filename (optional, must already exist in images/listings/)</p>
        <textarea id="bulkProductList" rows="16"><?php echo htmlspecialchars(BULK_PRODUCT_SEED); ?></textarea>
    </div>
    <button type="button" class="admin-btn" id="bulkAddProductsBtn" onclick="bulkAddProducts()"><i class="fas fa-layer-group"></i> Add all products</button>
</div>

<div class="admin-panel">
    <h2>All products (<?php echo $totalProducts; ?>)</h2>
    <div class="admin-table-wrap">
        <?php if (empty($products)): ?>
            <p class="admin-empty">No products yet.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Seller</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr id="product-row-<?php echo (int) $product["listing_id"]; ?>">
                            <td><?php echo htmlspecialchars($product["title"]); ?></td>
                            <td><?php echo htmlspecialchars($product["category_name"]); ?></td>
                            <td><?php echo htmlspecialchars($product["seller_name"]); ?></td>
                            <td>Rs. <?php echo number_format((float) $product["price"], 2); ?></td>
                            <td><?php echo (int) $product["quantity"]; ?></td>
                            <td><span class="admin-badge <?php echo $product["status"] === "active" ? "active" : "deactive"; ?>"><?php echo htmlspecialchars($product["status"]); ?></span></td>
                            <td class="admin-table-actions">
                                <a class="admin-btn admin-btn-outline" href="edit-product.php?id=<?php echo (int) $product["listing_id"]; ?>"><i class="fas fa-pen"></i> Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($totalPages > 1): ?>
                <nav class="admin-pagination" aria-label="Product pages">
                    <a class="admin-page-btn <?php echo $page <= 1 ? "disabled" : ""; ?>"
                       href="?page=<?php echo max(1, $page - 1); ?>">&lt; Prev</a>

                    <?php foreach (paginationPageList($page, $totalPages) as $item): ?>
                        <?php if ($item === "..."): ?>
                            <span class="admin-page-ellipsis">&hellip;</span>
                        <?php else: ?>
                            <a class="admin-page-btn <?php echo $item === $page ? "active" : ""; ?>" href="?page=<?php echo (int) $item; ?>">
                                <?php echo (int) $item; ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <a class="admin-page-btn <?php echo $page >= $totalPages ? "disabled" : ""; ?>"
                       href="?page=<?php echo min($totalPages, $page + 1); ?>">Next &gt;</a>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?php include "includes/admin-layout-bottom.php"; ?>
