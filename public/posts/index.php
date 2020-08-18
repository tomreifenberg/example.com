<?php
include '../../core/db_connect.php';

$stmt = $pdo->query("SELECT * FROM posts");

while ($row = $stmt->fetch())
{

    $content .= "<a href=\"view.php?slug={$row['slug']}\">{$row['title']}</a>";}

include '../../core/layout.php';