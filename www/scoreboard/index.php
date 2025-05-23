<?php
require_once '../config/database.php';
$db = new Database();
$scores = $db->getScoreboard();
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Scoreboard</title></head>
<body>
<h1>Live Scoreboard</h1>
<table border="1">
<tr><th>Participant</th><th>Total Points</th><th>Total Scores</th></tr>
<?php foreach ($scores as $score): ?>
<tr>
<td><?= htmlspecialchars($score['display_name']) ?></td>
<td><?= $score['total_points'] ?></td>
<td><?= $score['total_scores'] ?></td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>