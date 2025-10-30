<?php
require_once(__DIR__ . '/../models/TodoModel.php');

class TodoController
{
    private $model;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->model = new TodoModel();
    }

    public function index()
    {
        $filter = $_GET['filter'] ?? 'all';
        $search = $_GET['search'] ?? '';
        $todos = $this->model->getAllTodos($filter, $search);
        include(__DIR__ . '/../views/TodoView.php');
    }

    public function create()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $activity = trim($_POST['activity']);
        $description = trim($_POST['description'] ?? '');

        if ($activity === '') {
            $_SESSION['flash'] = "Nama aktivitas tidak boleh kosong!";
        } elseif ($this->model->isActivityExists($activity)) {
            $_SESSION['flash'] = "Aktivitas dengan nama ini sudah ada!";
        } else {
            $this->model->createTodo($activity, $description);
            $_SESSION['flash'] = "Aktivitas berhasil ditambahkan!";
        }
    }

    header("Location: index.php");
    exit;
}





    public function delete()
    {
        if (isset($_GET['id'])) {
            $this->model->deleteTodo($_GET['id']);
            $_SESSION['flash'] = "Aktivitas dihapus!";
        }
        header("Location: index.php");
        exit;
    }

    public function toggle()
    {
        if (isset($_GET['id'])) {
            $this->model->toggleTodo($_GET['id']);
            $_SESSION['flash'] = "Status aktivitas diperbarui!";
        }
        header("Location: index.php");
        exit;
    }

    public function update()
{
     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];
        $activity = trim($_POST['activity']);
        $description = trim($_POST['description'] ?? '');
        $status = isset($_POST['status']) ? 1 : 0;

         if ($activity === '') {
            $_SESSION['flash'] = "Nama aktivitas tidak boleh kosong!";
        } else {
            $this->model->updateTodoDetail($id, $activity, $description, $status);
            $_SESSION['flash'] = "Aktivitas berhasil diperbarui!";
        }
    }

    header("Location: index.php");
    exit;
}
public function detail()
{
    if (isset($_GET['id'])) {
        $todo = $this->model->getTodoById($_GET['id']);
        include(__DIR__ . '/../views/TodoDetailView.php');
    } else {
        $_SESSION['flash'] = "Todo tidak ditemukan!";
        header("Location: index.php");
        exit;
    }
}
public function reorder()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order'])) {
        $order = $_POST['order']; // array id
        $this->model->updateOrder($order);
        echo json_encode(['success' => true]);
        exit;
    }
}


}
