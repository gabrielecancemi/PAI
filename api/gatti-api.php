<?php

declare(strict_types=1);

require_once "../includes/db.php";

header("Content-Type: application/json");

$conn = getLectureConnection();

$sql = "
    SELECT *
    FROM gatti
    ORDER BY data_arrivo DESC
";

$result = $conn->query($sql);

$cats = [];

while ($row = $result->fetch_assoc()) {

    $cats[] = $row;
}

echo json_encode($cats);