<?php
require_once(__DIR__ . '/../config.php');

class TodoModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = pg_connect('host=' . DB_HOST . ' port=' . DB_PORT . ' dbname=' . DB_NAME . ' user=' . DB_USER . ' password=' . DB_PASSWORD);
        if (!$this->conn) {
            die('❌ Gagal konek ke database PostgreSQL. Cek config.php!');
        }
    }

    // Ambil semua todo dengan filter + pencarian
    public function getAllTodos($filter, $search)
    {
        $query = "SELECT * FROM todo";
        $conditions = [];
        $params = [];
        $index = 1;

        // 🔹 Tambahkan logika filter selesai / belum selesai
        if ($filter === 'finished') {
            $conditions[] = "status = 1";
        } elseif ($filter === 'unfinished') {
            $conditions[] = "status = 0";
        }

        // 🔹 Tambahkan logika pencarian tanpa mengganggu filter
        if (!empty($search)) {
            $conditions[] = "LOWER(activity) LIKE LOWER($" . $index++ . ")";
            $params[] = "%$search%";
        }

        // 🔹 Gabungkan semua kondisi
        if (count($conditions) > 0) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY position ASC";

        // 🔹 Eksekusi query berdasarkan jumlah parameter
        if (count($params) > 0) {
            $result = pg_query_params($this->conn, $query, $params);
        } else {
            $result = pg_query($this->conn, $query);
        }

        return pg_fetch_all($result) ?: [];
    }

    public function createTodo($activity)
{
    $query = "INSERT INTO todo (activity, status, created_at, updated_at)
              VALUES ($1, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
    return pg_query_params($this->conn, $query, [$activity, $description]);
}


    public function updateTodo($id, $activity)
    {
        $query = "UPDATE todo SET activity=$1, updated_at=CURRENT_TIMESTAMP WHERE id=$2";
        return pg_query_params($this->conn, $query, [$activity, $id]);
    }
    public function updateTodoDetail($id, $activity, $description, $status)
{
    $query = "UPDATE todo 
              SET activity = $1, 
                  description = $2,
                  status = $3,
                  updated_at = CURRENT_TIMESTAMP 
              WHERE id = $4";
    return pg_query_params($this->conn, $query, [$activity, $description, $status, $id]);
}


    public function deleteTodo($id)
    {
        $query = "DELETE FROM todo WHERE id=$1";
        return pg_query_params($this->conn, $query, [$id]);
    }

    public function toggleTodo($id)
    {
        $res = pg_query_params($this->conn, "SELECT status FROM todo WHERE id = $1", [$id]);
        if ($res && pg_num_rows($res) > 0) {
            $row = pg_fetch_assoc($res);
            $newStatus = ($row['status'] == 1) ? 0 : 1;
            pg_query_params($this->conn, "UPDATE todo SET status = $1, updated_at = CURRENT_TIMESTAMP WHERE id = $2", [$newStatus, $id]);
        }
    }
    public function getTodoById($id)
{
    $query = "SELECT * FROM todo WHERE id = $1";
    $result = pg_query_params($this->conn, $query, [$id]);
    return pg_fetch_assoc($result);
}


    public function isActivityExists($activity)
{
    $query = "SELECT COUNT(*) FROM todo WHERE LOWER(activity) = LOWER($1)";
    $result = pg_query_params($this->conn, $query, [$activity]);
    return pg_fetch_result($result, 0, 0) > 0;
}

public function updateOrder($orderedIds)
{
    $position = 1;
    foreach ($orderedIds as $id) {
        pg_query_params($this->conn, "UPDATE todo SET position = $1 WHERE id = $2", [$position, $id]);
        $position++;
    }
}

    
}
