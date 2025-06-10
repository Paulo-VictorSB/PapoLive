<script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
<?php if (isset($_SESSION['chat.js'])) : ?>
    <script type="module" src="assets/chat.js"></script>
<?php else: ?>
    <script type="module" src="assets/index.js"></script>
<?php endif; ?>

</body>
</html>