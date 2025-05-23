<?php
require_once '../config/database.php';
$db = new Database();

// Handle add judge
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_judge'])) {
    $username = trim($_POST['judge_username']);
    $display_name = trim($_POST['judge_display_name']);
    if ($username && $display_name) {
        try {
            $db->addJudge($username, $display_name);
            $success = "Judge added successfully.";
        } catch (Exception $e) {
            $error = "Error adding judge: " . $e->getMessage();
        }
    }
}

// Handle add user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['user_username']);
    $display_name = trim($_POST['user_display_name']);
    if ($username && $display_name) {
        try {
            $db->addUser($username, $display_name);
            $success = "User added successfully.";
        } catch (Exception $e) {
            $error = "Error adding user: " . $e->getMessage();
        }
    }
}

// Handle fetch judge scores
$selected_judge_id = null;
$judge_scores = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['get_scores'])) {
    $selected_judge_id = intval($_POST['judge_id']);
    $judge_scores = $db->getJudgeScores($selected_judge_id);
}

$judges = $db->getJudges();
$users = $db->getUsers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        form { margin-bottom: 30px; }
        label { display: block; margin-top: 10px; }
        input[type="text"] { width: 300px; padding: 5px; }
        input[type="submit"] { margin-top: 10px; padding: 5px 15px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>

<h1>👨‍💼 Admin Panel</h1>

<?php if (!empty($success)): ?><p class="success"><?= $success ?></p><?php endif; ?>
<?php if (!empty($error)): ?><p class="error"><?= $error ?></p><?php endif; ?>

<h2>Add Judge</h2>
<form method="post">
    <input type="hidden" name="add_judge" value="1">
    <label>Username: <input type="text" name="judge_username" required></label>
    <label>Display Name: <input type="text" name="judge_display_name" required></label>
    <input type="submit" value="Add Judge">
</form>

<h2>Add User</h2>
<form method="post">
    <input type="hidden" name="add_user" value="1">
    <label>Username: <input type="text" name="user_username" required></label>
    <label>Display Name: <input type="text" name="user_display_name" required></label>
    <input type="submit" value="Add User">
</form>

<h2>View Judge Scores</h2>
<form method="post">
    <input type="hidden" name="get_scores" value="1">
    <label>Select Judge:
        <select name="judge_id" required>
            <option value="">-- Select Judge --</option>
            <?php foreach ($judges as $judge): ?>
                <option value="<?= $judge['id'] ?>" <?= $selected_judge_id == $judge['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($judge['display_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <input type="submit" value="Get Scores">
</form>

<?php if (!empty($judge_scores)): ?>
    <h3>Scores for <?= htmlspecialchars($judges[array_search($selected_judge_id, array_column($judges, 'id'))]['display_name']) ?>:</h3>
    <ul>
        <?php foreach ($judge_scores as $score): ?>
            <li><?= htmlspecialchars($score['user_name']) ?> — <?= $score['points'] ?> points</li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<h2>All Judges</h2>
<ul>
    <?php foreach ($judges as $judge): ?>
        <li><?= htmlspecialchars($judge['display_name']) ?> (<?= htmlspecialchars($judge['username']) ?>)</li>
    <?php endforeach; ?>
</ul>

<h2>All Participants</h2>
<ul>
    <?php foreach ($users as $user): ?>
        <li><?= htmlspecialchars($user['display_name']) ?> (<?= htmlspecialchars($user['username']) ?>)</li>
    <?php endforeach; ?>
</ul>

</body>
</html>
