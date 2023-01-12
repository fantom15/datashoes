<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?= base_url() ?>">
    <meta charset="utf-8">
    <title>Data shoes</title>

    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">

    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
</head>

<body class="main-bg pb-5">
    <div class="container-fluid" style="padding-top: 70px;">
        <div class="row">
            <?= $content ?>
        </div>
    </div>

</body>

</html>