<?php
$route = $_GET['route'] ?? 'index';
?>

<script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
<script type="module" src="assets/<?= $route === 'chat' ? 'chat' : 'index'?>.js"></script>

</body>
</html>