            </div>
        </main>
    </div>
    <script src="../js/bootstrap.bundle.min.js"></script>
    <?php $adminJsVersion = @filemtime(__DIR__ . "/../js/admin.js") ?: time(); ?>
    <script src="js/admin.js?v=<?php echo $adminJsVersion; ?>"></script>
</body>
</html>
