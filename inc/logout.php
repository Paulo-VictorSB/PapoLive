<?php

session_destroy();

header("Location: ?route=index");

?>

<script>
    const sessionStorage = window.sessionStorage;
    sessionStorage.setItem('user', '');
</script>