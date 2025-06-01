<?php
/**
 * MyWorkHub - Periods API
 * Handles CRUD operations for periods
 */

// Set headers
header('Content-Type: application/json');

// Include configuration
$config_path = __DIR__ . '/../config.php';

if (!file_exists($config_path)) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Configuration file (config.php) not found.',
        'path_checked' => $config_path
    ]);
    exit;
}

require_once $config_path;

try {
    $db = get_db_connection();
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    
    switch ($action) {
        case 'list':
            // Get all periods with task counts
            $query = "
                SELECT 
                    p.*,
                    (SELECT COUNT(*) FROM MajorTasks WHERE period_id = p.id) as task_count
                FROM Periods p
                ORDER BY 
                    CASE WHEN p.start_date IS NULL THEN 1 ELSE 0 END,
                    p.start_date DESC,
                    p.created_at DESC
            ";
            
            $stmt = $db->query($query);
            $periods = $stmt->fetchAll();
            
            echo json_encode(['status' => 'success', 'data' => $periods]);
            break;
            
        case 'get':
            if (!isset($_GET['id'])) {
                throw new Exception("ID is required");
            }
            
            $id = (int)$_GET['id'];
            $stmt = $db->prepare("SELECT * FROM Periods WHERE id = ?");
            $stmt->execute([$id]);
            $period = $stmt->fetch();
            
            if (!$period) {
                echo json_encode(['status' => 'error', 'message' => 'Period not found']);
            } else {
                echo json_encode(['status' => 'success', 'data' => $period]);
            }
            break;
            
        case 'create':
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) {
                $data = $_POST;
            }
            
            if (!isset($data['name']) || empty($data['name'])) {
                throw new Exception("Name is required");
            }
            
            $stmt = $db->prepare("
                INSERT INTO Periods (name, description, start_date, end_date, status, color_code, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['name'],
                $data['description'] ?? null,
                $data['start_date'] ?? null,
                $data['end_date'] ?? null,
                $data['status'] ?? 'active',
                $data['color_code'] ?? null,
                $data['created_by'] ?? null
            ]);
            
            $newId = $db->lastInsertId();
            
            // Get the newly created period
            $stmt = $db->prepare("SELECT * FROM Periods WHERE id = ?");
            $stmt->execute([$newId]);
            $period = $stmt->fetch();
            
            echo json_encode([
                'status' => 'success', 
                'message' => 'Period created successfully',
                'data' => $period
            ]);
            break;
            
        case 'update':
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) {
                $data = $_POST;
            }
            
            if (!isset($data['id']) || empty($data['id'])) {
                throw new Exception("ID is required");
            }
            
            $stmt = $db->prepare("
                UPDATE Periods 
                SET name = ?, description = ?, start_date = ?, end_date = ?, 
                    status = ?, color_code = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['name'],
                $data['description'] ?? null,
                $data['start_date'] ?? null,
                $data['end_date'] ?? null,
                $data['status'] ?? 'active',
                $data['color_code'] ?? null,
                $data['id']
            ]);
            
            // Get the updated period
            $stmt = $db->prepare("SELECT * FROM Periods WHERE id = ?");
            $stmt->execute([$data['id']]);
            $period = $stmt->fetch();
            
            echo json_encode([
                'status' => 'success', 
                'message' => 'Period updated successfully',
                'data' => $period
            ]);
            break;
            
        case 'delete':
            if (!isset($_GET['id'])) {
                throw new Exception("ID is required");
            }
            
            $id = (int)$_GET['id'];
            
            // Check for associated tasks
            $stmt = $db->prepare("SELECT COUNT(*) FROM MajorTasks WHERE period_id = ?");
            $stmt->execute([$id]);
            $taskCount = $stmt->fetchColumn();
            
            if ($taskCount > 0) {
                throw new Exception("Cannot delete period with associated tasks. Please delete or reassign the tasks first.");
            }
            
            $stmt = $db->prepare("DELETE FROM Periods WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode([
                'status' => 'success', 
                'message' => 'Period deleted successfully'
            ]);
            break;
            
        default:
            throw new Exception("Invalid action");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>