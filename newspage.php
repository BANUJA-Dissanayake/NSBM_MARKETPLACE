<?php
session_start();
include "connection.php";

$news_items = [];
$rs = Database::search("SELECT news.*, `user`.name AS author_name
    FROM news
    INNER JOIN `user` ON news.author_id = `user`.user_id
    WHERE news.is_active = 1
    ORDER BY news.created_at DESC");
if ($rs) {
    while ($row = $rs->fetch_assoc()) {
        $news_items[] = $row;
    }
}

function newsExcerpt($text, $length = 140) {
    $text = trim($text);
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . "...";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News | NSBM Marketplace</title>
    <link rel="stylesheet" href="css/news.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="news-page">
        <section class="news-intro">
            <div>
                <p class="news-eyebrow">From the marketplace</p>
                <h1>Latest news &amp; tips.</h1>
            </div>
            <p class="news-description">Guides and updates for buying, selling, and settling into campus life.</p>
        </section>

        <div class="news-submit-cta">
            <a class="news-submit-link" href="submit-news.php">Share your own story <span aria-hidden="true">&rarr;</span></a>
        </div>

        <section class="news-grid" aria-label="News articles">
            <?php if (empty($news_items)): ?>
                <p class="news-empty">No articles yet. Check back soon.</p>
            <?php else: ?>
                <?php foreach ($news_items as $item): ?>
                    <a class="news-card" href="news-details.php?id=<?php echo (int) $item['news_id']; ?>">
                        <div class="news-card-image">
                            <?php if (!empty($item['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                            <?php else: ?>
                                <span><?php echo htmlspecialchars($item['title']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="news-card-body">
                            <span class="news-card-date"><?php echo htmlspecialchars($item['created_at']); ?></span>
                            <h2><?php echo htmlspecialchars($item['title']); ?></h2>
                            <p><?php echo htmlspecialchars(newsExcerpt($item['content'])); ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
