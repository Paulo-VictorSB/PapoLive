<?php

if (!isset($_SESSION['username'])) {
    header("Location: ?route=index");
    exit;
}