<?php
session_start();
include "connection.php";

// Pull real categories (and how many active listings sit in each one) so
// this page always matches what admins have set up in admin/categories.php,
// instead of the 4 hardcoded cards this page used to show.
$categories = [];
$rs = Database::search("SELECT categories.*,
        (SELECT COUNT(*) FROM listings
         WHERE listings.category_id = categories.category_id AND listings.status = 'active') AS product_count
    FROM categories ORDER BY category_name ASC");
if ($rs) {
    while ($row = $rs->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Cards without an admin-set image fall back to a colour + icon from this
// palette, cycling by position so it still looks intentional at any count.
$swatches = [
    ["bg" => "#cfe8d5", "icon" => "&#9682;"],
    ["bg" => "#f1dcae", "icon" => "&#9633;"],
    ["bg" => "#d7d5ee", "icon" => "&#10022;"],
    ["bg" => "#f0c7b8", "icon" => "&#43;"],
    ["bg" => "#c8ddce", "icon" => "&#10070;"],
    ["bg" => "#f4d9df", "icon" => "&#9670;"],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories | NSBM Marketplace</title>
    <link rel="stylesheet" href="css/categories.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="categories-page">
        <section class="categories-intro">
            <div>
                <p class="categories-eyebrow">Browse by collection</p>
                <h1>Find your next campus essential.</h1>
            </div>
            <p class="categories-description">Explore useful, reusable, and study-friendly products selected for the NSBM community.</p>
        </section>

        <?php if (!empty($categories)): ?>
            <div class="categories-toolbar">
                <p class="categories-count" id="categoriesCount"><?php echo count($categories); ?> collection<?php echo count($categories) === 1 ? "" : "s"; ?> available</p>
                <div class="categories-search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm10 2-4.35-4.35" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <input type="search" id="categorySearch" placeholder="Search collections..." aria-label="Search collections">
                </div>
            </div>
        <?php endif; ?>

        <section class="category-grid" id="categoryGrid" aria-label="Product categories">
            <?php if (empty($categories)): ?>
                <p class="categories-empty">No collections have been added yet. Check back soon.</p>
            <?php else: ?>
                <?php foreach ($categories as $index => $category):
                    $swatch = $swatches[$index % count($swatches)];
                    $hasImage = !empty($category["image_url"]);
                    $productCount = (int) $category["product_count"];
                    $name = htmlspecialchars($category["category_name"]);
                    $description = htmlspecialchars($category["description"] ?? "");
                ?>
                    <a class="category-card <?php echo $hasImage ? "has-image" : ""; ?>"
                       style="<?php echo $hasImage ? "" : "background:" . htmlspecialchars($swatch["bg"]); ?>"
                       href="shop.php?category=<?php echo (int) $category["category_id"]; ?>"
                       data-name="<?php echo strtolower($name); ?>">
                        <?php if ($hasImage): ?>
                            <img class="category-card-image" src="<?php echo htmlspecialchars($category["image_url"]); ?>" alt="" loading="lazy">
                            <span class="category-card-overlay" aria-hidden="true"></span>
                        <?php else: ?>
                            <span class="category-icon" aria-hidden="true"><?php echo $swatch["icon"]; ?></span>
                        <?php endif; ?>

                        <span class="category-index"><?php echo str_pad((string) ($index + 1), 2, "0", STR_PAD_LEFT); ?></span>
                        <span class="category-count"><?php echo $productCount; ?> product<?php echo $productCount === 1 ? "" : "s"; ?></span>

                        <h2><?php echo $name; ?></h2>
                        <?php if ($description !== ""): ?>
                            <p><?php echo $description; ?></p>
                        <?php endif; ?>
                        <span class="category-link">Shop collection <span aria-hidden="true">&rarr;</span></span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <p class="categories-empty categories-no-match" id="categoriesNoMatch" hidden>No collections match your search.</p>

        <section class="categories-callout">
            <div>
                <p class="categories-eyebrow">Ready to discover?</p>
                <h2>Good finds are closer than you think.</h2>
            </div>
            <a class="callout-button" href="shop.php">View all products</a>
        </section>
    </main>

    <?php include 'footer.php'; ?>
    <script>
        // Filters the category cards by name as you type - purely client
        // side since the full list is already on the page.
        (function () {
            var input = document.getElementById("categorySearch");
            if (!input) return;

            var cards = Array.prototype.slice.call(document.querySelectorAll("#categoryGrid .category-card"));
            var noMatch = document.getElementById("categoriesNoMatch");
            var countLabel = document.getElementById("categoriesCount");

            input.addEventListener("input", function () {
                var query = input.value.trim().toLowerCase();
                var visible = 0;

                cards.forEach(function (card) {
                    var matches = card.getAttribute("data-name").indexOf(query) !== -1;
                    card.hidden = !matches;
                    if (matches) visible++;
                });

                if (noMatch) noMatch.hidden = visible !== 0;
                if (countLabel) {
                    countLabel.textContent = query
                        ? visible + " of " + cards.length + " collections"
                        : cards.length + " collection" + (cards.length === 1 ? "" : "s") + " available";
                }
            });
        })();
    </script>
</body>
</html>
