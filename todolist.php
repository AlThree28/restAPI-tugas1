<?php
session_start();

// Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

include 'db.php'; // Sertakan koneksi database

// Proses penambahan tugas
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $task = $_POST['task'];
    if (!empty($task)) {
        $stmt = $conn->prepare("INSERT INTO todos (task, status) VALUES (?, 'pending')");
        $stmt->bind_param("s", $task);
        $stmt->execute();
        $stmt->close();
    }
}

// Proses menyelesaikan tugas
if (isset($_GET['complete'])) {
    $id = $_GET['complete'];
    $stmt = $conn->prepare("UPDATE todos SET status = 'complete' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Proses menghapus tugas
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM todos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Ambil semua tugas dari database
$result = $conn->query("SELECT * FROM todos");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>To Do List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .todo-container {
            width: 400px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .todo-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .task-form {
            display: flex;
            margin-bottom: 20px;
        }

        .task-form input[type="text"] {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-right: 10px;
            font-size: 14px;
        }

        .task-form input[type="submit"] {
            padding: 10px;
            border: none;
            border-radius: 4px;
            background-color: #5a67d8;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .task-form input[type="submit"]:hover {
            background-color: #434190;
        }

        .task-list {
            list-style: none;
            padding: 0;
        }

        .task-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .task-item.complete {
            text-decoration: line-through;
            color: #888;
        }

        .task-actions {
            display: flex;
            gap: 5px;
        }

        .task-actions a {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            background-color: #48bb78;
            color: #fff;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .task-actions a.complete {
            background-color: #38a169;
        }

        .task-actions a.delete {
            background-color: #e53e3e;
        }

        .task-actions a:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="todo-container">
        <h2>To Do List</h2>
        <form class="task-form" method="POST" action="">
            <input type="text" name="task" placeholder="Teks to do" required>
            <input type="submit" name="add" value="Tambah">
        </form>

        <ul class="task-list">
            <?php while ($row = $result->fetch_assoc()): ?>
                <li class="task-item <?php echo $row['status'] == 'complete' ? 'complete' : ''; ?>">
                    <?php echo htmlspecialchars($row['task']); ?>
                    <div class="task-actions">
                        <?php if ($row['status'] == 'pending'): ?>
                            <a href="?complete=<?php echo $row['id']; ?>" class="complete">Selesai</a>
                        <?php endif; ?>
                        <a href="?delete=<?php echo $row['id']; ?>" class="delete">Hapus</a>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</body>
</html>
