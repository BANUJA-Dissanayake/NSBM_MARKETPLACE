<?php
session_start();
include "connection.php";

$news_id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
$article = null;

if ($news_id > 0) {
    $rs = Database::search("SELECT news.*, `user`.name AS author_name
        FROM news
        INNER JOIN `user` ON news.author_id = `user`.user_id
        WHERE news.news_id = $news_id AND news.is_active = 1");
    if ($rs && $rs->num_rows > 0) {
        $article = $rs->fetch_assoc();
    }
}

$other_news = [];
if ($article) {
    $otherRs = Database::search("SELECT news_id, title, image_url, created_at FROM news
        WHERE news_id != $news_id AND is_active = 1
        ORDER BY created_at DESC LIMIT 4");
    if ($otherRs) {
        while ($row = $otherRs->fetch_assoc()) {
            $other_news[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $article ? htmlspecialchars($article["title"]) . " | NSBM Marketplace" : "Article not found | NSBM Marketplace"; ?></title>
    <link rel="stylesheet" href="css/news.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="news-page">
        <p class="news-breadcrumb"><a href="newspage.php">News</a></p>

        <?php if (!$article): ?>
            <p class="news-empty">Sorry, we couldn't find that article. <a href="newspage.php">Back to news</a></p>
        <?php else: ?>
            <div class="news-detail-layout">
                <?php if (!empty($other_news)): ?>
                    <aside class="news-sidebar" aria-label="More articles">
                        <h2>More from the marketplace</h2>
                        <div class="news-sidebar-list">
                            <?php foreach ($other_news as $item): ?>
                                <a class="news-sidebar-item" href="news-details.php?id=<?php echo (int) $item['news_id']; ?>">
                                    <div class="news-sidebar-thumb">
                                        <?php if (!empty($item['image_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                        <?php else: ?>
                                            <span><?php echo htmlspecialchars($item['title']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="news-sidebar-info">
                                        <span class="news-sidebar-date"><?php echo htmlspecialchars($item['created_at']); ?></span>
                                        <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </aside>
                <?php endif; ?>

                <article class="news-article">
                    <?php if (!empty($article["image_url"])): ?>
                        <div class="news-article-image">
                            <img src="<?php echo htmlspecialchars($article["image_url"]); ?>" alt="<?php echo htmlspecialchars($article["title"]); ?>">
                        </div>
                    <?php endif; ?>
                    <div class="news-article-meta">
                        <span><?php echo htmlspecialchars($article["created_at"]); ?></span>
                        <span aria-hidden="true">&middot;</span>
                        <span>By <?php echo htmlspecialchars($article["author_name"]); ?></span>
                    </div>
                    <h1><?php echo htmlspecialchars($article["title"]); ?></h1>
                    <p class="news-article-content"><?php echo nl2br(htmlspecialchars($article["content"])); ?></p>
                </article>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
