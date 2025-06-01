<?php
/**
 * MyWorkHub - Tasks API
 * Handles CRUD operations for major tasks
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
            // Get all major tasks
            $query = "
                SELECT 
                    mt.*,
                    p.name as period_name,
                    u1.username as assigned_to_name,
                    u2.username as created_by_name,
                    (SELECT COUNT(*) FROM SubTasks WHERE major_task_id = mt.id) as subtask_count
                FROM MajorTasks mt
                LEFT JOIN Periods p ON mt.period_id = p.id
                LEFT JOIN Users u1 ON mt.assigned_to = u1.id
                LEFT JOIN Users u2 ON mt.created_by = u2.id
                ORDER BY 
                    CASE 
                        WHEN mt.status = 'To Do' THEN 1
                        WHEN mt.status = 'In Progress' THEN 2
                        WHEN mt.status = 'On Hold' THEN 3
                        WHEN mt.status = 'Completed' THEN 4
                        WHEN mt.status = 'Cancelled' THEN 5
                        ELSE 6
                    END,
                    mt.priority DESC,
                    CASE WHEN mt.deadline IS NULL THEN 1 ELSE 0 END,
                    mt.deadline ASC
            ";
            
            $stmt = $db->query($query);
            $tasks = $stmt->fetchAll();
            
            echo json_encode(['status' => 'success', 'data' => $tasks]);
            break;
            
        case 'get':
            if (!isset($_GET['id'])) {
                throw new Exception("ID is required");
            }
            
            $id = (int)$_GET['id'];
            $stmt = $db->prepare("
                SELECT 
                    mt.*,
                    p.name as period_name,
                    u1.username as assigned_to_name,
                    u2.username as created_by_name
                FROM MajorTasks mt
                LEFT JOIN Periods p ON mt.period_id = p.id
                LEFT JOIN Users u1 ON mt.assigned_to = u1.id
                LEFT JOIN Users u2 ON mt.created_by = u2.id
                WHERE mt.id = ?
            ");
            $stmt->execute([$id]);
            $task = $stmt->fetch();
            
            if (!$task) {
                echo json_encode(['status' => 'error', 'message' => 'Task not found']);
            } else {
                // Get subtasks if any
                $stmt = $db->prepare("
                    SELECT * FROM SubTasks 
                    WHERE major_task_id = ? 
                    ORDER BY order_index ASC, created_at ASC
                ");
                $stmt->execute([$id]);
                $subtasks = $stmt->fetchAll();
                
                $task['subtasks'] = $subtasks;
                
                echo json_encode(['status' => 'success', 'data' => $task]);
            }
            break;
            
        case 'create':
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) {
                $data = $_POST;
            }
            
            if (!isset($data['task_name']) || empty($data['task_name'])) {
                throw new Exception("Task name is required");
            }
            
            $stmt = $db->prepare("
                INSERT INTO MajorTasks (
                    period_id, task_name, description, priority, urgency, importance,
                    deadline, status, percent_complete, working_with, notes,
                    estimated_hours, actual_hours, assigned_to, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['period_id'] ?? null,
                $data['task_name'],
                $data['description'] ?? null,
                $data['priority'] ?? 'Medium',
                $data['urgency'] ?? 'Soon',
                $data['importance'] ?? 'Important',
                $data['deadline'] ?? null,
                $data['status'] ?? 'To Do',
                $data['percent_complete'] ?? 0,
                $data['working_with'] ?? null,
                $data['notes'] ?? null,
                $data['estimated_hours'] ?? null,
                $data['actual_hours'] ?? null,
                $data['assigned_to'] ?? null,
                $data['created_by'] ?? null
            ]);
            
            $newId = $db->lastInsertId();
            
            // Get the newly created task
            $stmt = $db->prepare("SELECT * FROM MajorTasks WHERE id = ?");
            $stmt->execute([$newId]);
            $task = $stmt->fetch();
            
            echo json_encode([
                'status' => 'success', 
                'message' => 'Task created successfully',
                'data' => $task
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
                UPDATE MajorTasks 
                SET period_id = ?, task_name = ?, description = ?, priority = ?, 
                    urgency = ?, importance = ?, deadline = ?, status = ?, 
                    percent_complete = ?, working_with = ?, notes = ?,
                    estimated_hours = ?, actual_hours = ?, assigned_to = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['period_id'] ?? null,
                $data['task_name'],
                $data['description'] ?? null,
                $data['priority'] ?? 'Medium',
                $data['urgency'] ?? 'Soon',
                $data['importance'] ?? 'Important',
                $data['deadline'] ?? null,
                $data['status'] ?? 'To Do',
                $data['percent_complete'] ?? 0,
                $data['working_with'] ?? null,
                $data['notes'] ?? null,
                $data['estimated_hours'] ?? null,
                $data['actual_hours'] ?? null,
                $data['assigned_to'] ?? null,
                $data['id']
            ]);
            
            echo json_encode([
                'status' => 'success', 
                'message' => 'Task updated successfully'
            ]);
            break;
            
        case 'delete':
            if (!isset($_GET['id'])) {
                throw new Exception("ID is required");
            }
            
            $id = (int)$_GET['id'];
            
            // Begin transaction to handle subtasks deletion
            $db->beginTransaction();
            
            try {
                // Delete subtasks first
                $stmt = $db->prepare("DELETE FROM SubTasks WHERE major_task_id = ?");
                $stmt->execute([$id]);
                
                // Delete the task
                $stmt = $db->prepare("DELETE FROM MajorTasks WHERE id = ?");
                $stmt->execute([$id]);
                
                $db->commit();
                
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Task and all associated subtasks deleted successfully'
                ]);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            break;
            
        default:
            throw new Exception("Invalid action");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>