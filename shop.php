<?php
session_start();
include "connection.php";
include "includes/productHelper.php";
include "pagination.php";

// Set when the header search bar sends someone here as shop.php?search=...
$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";

// Set when categories.php links here as shop.php?category=... so a category
// card lands you on an already-filtered shop page instead of the full list.
$category = isset($_GET["category"]) ? trim($_GET["category"]) : "all";
if ($category !== "all") {
    $category = (int) $category;
}

$categories_rs = Database::search("SELECT * FROM categories ORDER BY category_name ASC");
$categories = [];
if ($categories_rs) {
    while ($row = $categories_rs->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Initial page load shows page 1 of every active product, most recent
// first, filtered by $search/$category when they were set.
$initialFilters = ["search" => $search, "category" => $category, "page" => 1, "per_page" => PRODUCTS_PER_PAGE];
$totalProducts = countProducts($initialFilters);
$totalPages = totalPagesFor($totalProducts);
$initialProducts = fetchProducts($initialFilters);
$productCount = count($initialProducts);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shop | NSBM Marketplace</title>
    <link rel="stylesheet" href="css/shop.css" />
</head>

<body>
    <?php include 'header.php'; ?>

    <main class="shop-page">
        <div class="shop-heading">
            <div>
                <p class="shop-eyebrow">NSBM Marketplace</p>
                <h1>Shop products</h1>
                <p class="shop-intro">
                    Find practical, sustainable products for campus life.
                </p>
            </div>
            <p class="results-count" id="resultsCount"><?php echo $totalProducts; ?> results</p>
        </div>

        <div class="shop-layout">
            <aside class="filter-panel" aria-label="Product filters">
                <div class="filter-header">
                    <h2>Filter products</h2>
                    <button class="clear-filters" id="clearFilters" type="button">
                        Clear
                    </button>
                </div>

                <!-- No visible search box here - the header search bar (header.php)
                     is the only search entry point. This hidden field just carries
                     the active search term through category/price/sort/page AJAX
                     requests so it isn't lost when those change. -->
                <input type="hidden" id="productSearch" value="<?php echo htmlspecialchars($search); ?>" />

                <fieldset class="filter-group">
                    <legend>Category</legend>
                    <label><input type="radio" name="category" value="all" <?php echo $category === "all" ? "checked" : ""; ?> /> All products</label>
                    <?php foreach ($categories as $categoryOption): ?>
                        <label>
                            <input type="radio" name="category" value="<?php echo (int) $categoryOption['category_id']; ?>" <?php echo ((int) $categoryOption['category_id'] === $category) ? "checked" : ""; ?> />
                            <?php echo htmlspecialchars($categoryOption['category_name']); ?>
                        </label>
                    <?php endforeach; ?>
                </fieldset>

                <label class="filter-label" for="priceFilter">Price range</label>
                <select class="filter-input" id="priceFilter">
                    <option value="all">All prices</option>
                    <option value="under-2000">Under Rs. 2,000</option>
                    <option value="2000-3000">Rs. 2,000 - Rs. 3,000</option>
                    <option value="over-3000">Over Rs. 3,000</option>
                </select>
            </aside>

            <section class="shop-results" aria-label="Products">
                <div class="sort-row">
                    <label for="sortProducts">Sort by</label>
                    <select id="sortProducts">
                        <option value="featured">Featured</option>
                        <option value="price-low">Price: low to high</option>
                        <option value="price-high">Price: high to low</option>
                        <option value="name">Name: A to Z</option>
                    </select>
                </div>

                <div class="shop-product-grid" id="productGrid" data-page="1">
                    <?php echo renderProductCards($initialProducts); ?>
                </div>

                <div id="shopPagination"><?php echo renderPagination(1, $totalPages, $totalProducts); ?></div>
            </section>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    <script src="js/main.js"></script>
</body>

</html>
