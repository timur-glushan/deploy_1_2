<?php

require_once("Db.php");

$posts = Db::sql("SELECT * FROM posts ORDER BY id ASC;")->fetchAll();

header("Content-Type: application/json");
echo json_encode($posts);
