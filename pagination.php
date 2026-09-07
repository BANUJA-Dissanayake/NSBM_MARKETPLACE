<?php
const PRODUCTS_PER_PAGE = 9;

function totalPagesFor($totalItems, $perPage = PRODUCTS_PER_PAGE) {
    return max(1, (int) ceil($totalItems / $perPage));
}

function renderPagination($currentPage, $totalPages, $totalItems = 0) {
    $currentPage = max(1, min($currentPage, $totalPages));
    $dataAttrs = ' data-current-page="' . $currentPage . '" data-total-pages="' . $totalPages . '" data-total-items="' . (int) $totalItems . '"';

    if ($totalPages <= 1) {
        return '<nav class="shop-pagination"' . $dataAttrs . ' hidden></nav>';
    }

    ob_start();
    ?>
    <nav class="shop-pagination"<?php echo $dataAttrs; ?> aria-label="Product pages">
        <button type="button" class="page-btn page-arrow" onclick="goToProductPage(<?php echo $currentPage - 1; ?>)" <?php echo $currentPage <= 1 ? "disabled" : ""; ?>>
            <span aria-hidden="true">&lt;</span> Prev
        </button>

        <div class="page-numbers">
            <?php foreach (paginationPageList($currentPage, $totalPages) as $item): ?>
                <?php if ($item === "..."): ?>
                    <span class="page-ellipsis">&hellip;</span>
                <?php else: ?>
                    <button type="button" class="page-btn page-number <?php echo $item === $currentPage ? "active" : ""; ?>"
                            onclick="goToProductPage(<?php echo (int) $item; ?>)"
                            <?php echo $item === $currentPage ? 'aria-current="page"' : ""; ?>>
                        <?php echo (int) $item; ?>
                    </button>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <button type="button" class="page-btn page-arrow" onclick="goToProductPage(<?php echo $currentPage + 1; ?>)" <?php echo $currentPage >= $totalPages ? "disabled" : ""; ?>>
            Next <span aria-hidden="true">&gt;</span>
        </button>
    </nav>
    <?php
    return ob_get_clean();
}

function paginationPageList($currentPage, $totalPages) {
    $pages = [];
    $window = 1;

    for ($p = 1; $p <= $totalPages; $p++) {
        $isEdge = $p === 1 || $p === $totalPages;
        $isNearCurrent = abs($p - $currentPage) <= $window;

        if ($isEdge || $isNearCurrent) {
            $pages[] = $p;
        } else if (end($pages) !== "...") {
            $pages[] = "...";
        }
    }

    return $pages;
}
