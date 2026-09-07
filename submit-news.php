<?php
session_start();
include "connection.php";
include "includes/session-check.php";

requireLoginOrRedirect();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit News | NSBM Marketplace</title>
    <link rel="stylesheet" href="css/news.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="news-page">
        <p class="news-breadcrumb"><a href="newspage.php">&larr; Back to news</a></p>

        <section class="news-intro">
            <div>
                <p class="news-eyebrow">Share something</p>
                <h1>Submit a news article.</h1>
            </div>
            <p class="news-description">Got a tip, guide, or campus update worth sharing? Submit it below - our team reviews every article before it goes live on the news page.</p>
        </section>

        <section class="news-form-layout">
            <form class="news-form" id="newsSubmitForm" onsubmit="return submitNewsArticle(event)">
                <div class="form-success" id="newsSubmitSuccess" role="status" hidden>Thanks! Your article has been submitted and will appear once it's reviewed.</div>
                <div class="form-error" id="newsSubmitError" role="alert" hidden></div>

                <div class="form-field">
                    <label for="newsTitle">Title</label>
                    <input id="newsTitle" type="text" placeholder="e.g. 5 Tips for Selling Your Textbooks Fast" required>
                </div>
                <div class="form-field">
                    <label for="newsContent">Content</label>
                    <textarea id="newsContent" rows="8" placeholder="Write your article here..." required></textarea>
                </div>
                <div class="form-field">
                    <label for="newsImage">Image (optional)</label>
                    <input id="newsImage" type="file" accept="image/png,image/jpeg,image/gif,image/webp">
                </div>
                <button class="news-submit-button" type="submit" id="newsSubmitBtn">Submit for review <span aria-hidden="true">&rarr;</span></button>
            </form>
        </section>
    </main>

    <?php include 'footer.php'; ?>
    <script src="js/main.js"></script>
</body>
</html>
