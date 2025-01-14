<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gestion sportive | <?= $title ?></title>
  <link rel="stylesheet" href="/globals.css" />
  <?php
  if (isset($head)) {
    echo $head;
  }
  ?>
</head>

<body>
  <?= $content ?>
</body>

</html>