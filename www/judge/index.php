<?php
// judge/index.php
require_once '../config/database.php';
$db = new Database();
$users = $db->getUsers();
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Judge Portal</title></head>
<body>
<h1>Judge Portal</h1>
<form action="../api/add_score.php" method="POST">
<select name="user_id">
<?php foreach ($users as $user): ?>
<option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['display_name']) ?></option>
<?php endforeach; ?>
</select>
<input type="number" name="points" min="0" max="100" required>
<input type="hidden" name="judge_id" value="1"> <!-- Dummy ID -->
<button type="submit">Submit Score</button>
</form>
</body>
</html>