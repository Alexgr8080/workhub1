<?php
// Start session for user authentication
session_start();

// Check if user is logged in - redirect to login page if not
// if (!isset($_SESSION['user_id'])) {
//     header('Location: login.php');
//     exit;
// }

// Include configuration
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyWorkHub Dashboard</title>
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-bg: #f8f9fa;
            --dark-bg: #343a40;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --info-color: #3498db;
            --danger-color: #e74c3c;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            color: #333;
            padding-top: 56px; /* For fixed navbar */
        }
        
        .navbar {
            background-color: var(--secondary-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            color: white;
        }
        
        .sidebar {
            position: fixed;
            top: 56px;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 20px 0;
            overflow-x: hidden;
            overflow-y: auto;
            background-color: var(--dark-bg);
            width: 250px;
            transition: all 0.3s;
        }
        
        .sidebar.collapsed {
            width: 60px;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.75);
            padding: 10px 20px;
            margin: 5px 15px;
            border-radius: 5px;
            display: flex;
            align-items: center;
        }
        
        .sidebar .nav-link:hover {
            color: rgba(255,255,255,0.95);
            background-color: rgba(255,255,255,0.1);
        }
        
        .sidebar .nav-link.active {
            color: white;
            background-color: var(--primary-color);
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .sidebar.collapsed .nav-link span {
            display: none;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: margin 0.3s;
        }
        
        .main-content.expanded {
            margin-left: 60px;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            font-weight: 600;
            border-radius: 10px 10px 0 0 !important;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        /* Status colors for periods */
        .status-active {
            background-color: #2ecc71;
            color: white;
        }
        
        .status-completed {
            background-color: #34495e;
            color: white;
        }
        
        .status-planned {
            background-color: #3498db;
            color: white;
        }
        
        .status-archived {
            background-color: #95a5a6;
            color: white;
        }
        
        /* Status colors for tasks */
        .status-to-do {
            background-color: #3498db;
            color: white;
        }
        
        .status-in-progress {
            background-color: #f39c12;
            color: white;
        }
        
        .status-completed-task {
            background-color: #2ecc71;
            color: white;
        }
        
        .status-on-hold {
            background-color: #95a5a6;
            color: white;
        }
        
        .status-cancelled {
            background-color: #e74c3c;
            color: white;
        }
        
        /* Priority colors */
        .priority-low {
            border-left: 4px solid #3498db;
        }
        
        .priority-medium {
            border-left: 4px solid #f39c12;
        }
        
        .priority-high {
            border-left: 4px solid #e74c3c;
        }
        
        .priority-critical {
            border-left: 4px solid #c0392b;
        }
        
        /* Progress bar styling */
        .progress {
            height: 10px;
            border-radius: 5px;
            margin-top: 5px;
            background-color: rgba(0,0,0,0.05);
        }
        
        .progress-bar {
            background-color: var(--primary-color);
        }
        
        /* Custom styling for period cards */
        .period-card {
            border-radius: 10px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }
        
        .period-header {
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .period-header h3 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .period-details {
            padding: 0 15px 15px;
        }
        
        .period-dates {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            margin-top: 10px;
            color: #666;
        }
        
        .period-status {
            margin-top: 10px;
            font-weight: 500;
        }
        
        .period-footer {
            padding: 10px 15px;
            background-color: rgba(0,0,0,0.03);
            text-align: right;
        }
        
        .period-actions {
            display: flex;
            gap: 10px;
        }
        
        /* Custom styling for task cards */
        .task-card {
            padding: 15px;
            border-left-width: 4px;
            margin-bottom: 10px;
            position: relative;
        }
        
        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        
        .task-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin: 0;
            padding-right: 50px;
        }
        
        .task-metadata {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 8px;
            font-size: 0.85rem;
            color: #666;
        }
        
        .task-actions {
            position: absolute;
            top: 15px;
            right: 15px;
            display: flex;
            gap: 5px;
        }
        
        .task-description {
            margin-top: 10px;
            font-size: 0.9rem;
        }
        
        .subtask-list {
            margin-top: 15px;
        }
        
        .subtask-item {
            padding: 10px 0;
            border-top: 1px solid rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
        }
        
        .subtask-checkbox {
            margin-right: 10px;
        }
        
        .subtask-title {
            font-size: 0.9rem;
        }
        
        /* Loading indicators */
        .loading-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 50px 0;
            text-align: center;
        }
        
        .loading-spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-left-color: var(--primary-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
        }
        
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
        
        .error-container {
            padding: 30px;
            text-align: center;
            background-color: rgba(231, 76, 60, 0.1);
            border-radius: 10px;
            margin: 20px 0;
        }
        
        /* Modal customizations */
        .modal-header {
            background-color: var(--secondary-color);
            color: white;
            border-radius: 0.3rem 0.3rem 0 0;
        }
        
        .modal-content {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        /* Form customizations */
        .form-label {
            font-weight: 500;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }
        
        /* Color picker */
        .color-picker {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 5px;
        }
        
        .color-option {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid transparent;
        }
        
        .color-option.selected {
            border-color: #333;
        }
        
        /* Dashboard welcome state */
        .welcome-container {
            text-align: center;
            padding: 50px 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        /* Delete confirmation overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            color: white;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: static;
                height: auto;
                padding-bottom: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar.collapsed {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <button class="btn btn-link text-light me-3" id="sidebar-toggle">
                <i class="fas fa-bars"></i>
            </button>
            <a class="navbar-brand" href="#">MyWorkHub</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            <span id="current-username">User</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="#dashboard-section" data-section="dashboard-section">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#periods-section" data-section="periods-section">
                    <i class="far fa-calendar-alt"></i>
                    <span>Periods</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#tasks-section" data-section="tasks-section">
                    <i class="fas fa-tasks"></i>
                    <span>Tasks</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#reports-section" data-section="reports-section">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#settings-section" data-section="settings-section">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Dashboard Section -->
        <div id="dashboard-section" class="content-section active">
            <h1 class="mb-4">Dashboard</h1>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Active Periods</h5>
                            <h2 id="active-periods-count">-</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Open Tasks</h5>
                            <h2 id="open-tasks-count">-</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Completed Tasks</h5>
                            <h2 id="completed-tasks-count">-</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Upcoming Deadlines</h5>
                            <h2 id="upcoming-deadlines-count">-</h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Recent Periods</span>
                            <a href="#periods-section" class="btn btn-sm btn-outline-primary view-all-link" data-section="periods-section">View All</a>
                        </div>
                        <div class="card-body">
                            <div id="recent-periods-container">
                                <div class="loading-container">
                                    <div class="loading-spinner"></div>
                                    <p>Loading periods...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Task Progress</span>
                            <a href="#tasks-section" class="btn btn-sm btn-outline-primary view-all-link" data-section="tasks-section">View All</a>
                        </div>
                        <div class="card-body">
                            <div id="tasks-progress-container">
                                <div class="loading-container">
                                    <div class="loading-spinner"></div>
                                    <p>Loading task progress...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Upcoming Deadlines</span>
                            <button class="btn btn-sm btn-outline-primary" id="refresh-deadlines-btn">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="upcoming-deadlines-container">
                                <div class="loading-container">
                                    <div class="loading-spinner"></div>
                                    <p>Loading deadlines...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Periods Section -->
        <div id="periods-section" class="content-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Periods</h1>
                <button class="btn btn-primary" id="add-period-btn">
                    <i class="fas fa-plus me-2"></i>Add Period
                </button>
            </div>
            
            <div class="row" id="periods-container">
                <div class="col-12">
                    <div class="loading-container">
                        <div class="loading-spinner"></div>
                        <p>Loading periods...</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tasks Section -->
        <div id="tasks-section" class="content-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Tasks</h1>
                <button class="btn btn-primary" id="add-task-btn">
                    <i class="fas fa-plus me-2"></i>Add Task
                </button>
            </div>
            
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="filter-period" class="form-label">Period</label>
                            <select class="form-select" id="filter-period">
                                <option value="">All Periods</option>
                                <!-- Periods will be loaded here -->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter-status" class="form-label">Status</label>
                            <select class="form-select" id="filter-status">
                                <option value="">All Statuses</option>
                                <option value="To Do">To Do</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                                <option value="On Hold">On Hold</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter-priority" class="form-label">Priority</label>
                            <select class="form-select" id="filter-priority">
                                <option value="">All Priorities</option>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                                <option value="Critical">Critical</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-primary w-100" id="apply-filters-btn">Apply Filters</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="tasks-list-container">
                <div class="loading-container">
                    <div class="loading-spinner"></div>
                    <p>Loading tasks...</p>
                </div>
            </div>
        </div>
        
        <!-- Reports Section -->
        <div id="reports-section" class="content-section">
            <h1 class="mb-4">Reports</h1>
            <div class="card">
                <div class="card-body">
                    <p class="card-text">Reports functionality will be added in a future update.</p>
                </div>
            </div>
        </div>
        
        <!-- Settings Section -->
        <div id="settings-section" class="content-section">
            <h1 class="mb-4">Settings</h1>
            <div class="card">
                <div class="card-body">
                    <p class="card-text">Settings functionality will be added in a future update.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Period Modal -->
    <div class="modal fade" id="periodModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="periodModalTitle">Add Period</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="period-form">
                        <input type="hidden" id="period-id">
                        <div class="mb-3">
                            <label for="period-name" class="form-label">Period Name</label>
                            <input type="text" class="form-control" id="period-name" required>
                        </div>
                        <div class="mb-3">
                            <label for="period-description" class="form-label">Description</label>
                            <textarea class="form-control" id="period-description" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="period-start-date" class="form-label">Start Date</label>
                                                               <input type="date" class="form-control" id="period-start-date">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="period-end-date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="period-end-date">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="period-status" class="form-label">Status</label>
                            <select class="form-select" id="period-status">
                                <option value="active">Active</option>
                                <option value="completed">Completed</option>
                                <option value="planned">Planned</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="period-color" class="form-label">Color</label>
                            <input type="color" class="form-control form-control-color" id="period-color" value="#3498db">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="save-period-btn">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Modal -->
    <div class="modal fade" id="taskModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskModalTitle">Add Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="task-form">
                        <input type="hidden" id="task-id">
                        <div class="mb-3">
                            <label for="task-name" class="form-label">Task Name</label>
                            <input type="text" class="form-control" id="task-name" required>
                        </div>
                        <div class="mb-3">
                            <label for="task-period" class="form-label">Period</label>
                            <select class="form-select" id="task-period">
                                <option value="">No Period</option>
                                <!-- Periods will be loaded here -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="task-description" class="form-label">Description</label>
                            <textarea class="form-control" id="task-description" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="task-priority" class="form-label">Priority</label>
                                <select class="form-select" id="task-priority">
                                    <option value="Low">Low</option>
                                    <option value="Medium" selected>Medium</option>
                                    <option value="High">High</option>
                                    <option value="Critical">Critical</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="task-status" class="form-label">Status</label>
                                <select class="form-select" id="task-status">
                                    <option value="To Do" selected>To Do</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Completed">Completed</option>
                                    <option value="On Hold">On Hold</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="task-urgency" class="form-label">Urgency</label>
                                <select class="form-select" id="task-urgency">
                                    <option value="Flexible">Flexible</option>
                                    <option value="Soon" selected>Soon</option>
                                    <option value="Immediate">Immediate</option>
                                    <option value="Daily">Daily</option>
                                    <option value="Weekly">Weekly</option>
                                    <option value="Periodic">Periodic</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="task-importance" class="form-label">Importance</label>
                                <select class="form-select" id="task-importance">
                                    <option value="Routine">Routine</option>
                                    <option value="Important" selected>Important</option>
                                    <option value="Critical">Critical</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="task-deadline" class="form-label">Deadline</label>
                                <input type="date" class="form-control" id="task-deadline">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="task-assigned-to" class="form-label">Assigned To</label>
                                <select class="form-select" id="task-assigned-to">
                                    <option value="">Unassigned</option>
                                    <!-- Users will be loaded here -->
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="task-progress" class="form-label">Progress</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="task-progress" min="0" max="100" value="0">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="task-working-with" class="form-label">Working With</label>
                                <input type="text" class="form-control" id="task-working-with" placeholder="Other people involved">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="task-estimated-hours" class="form-label">Estimated Hours</label>
                                <input type="number" class="form-control" id="task-estimated-hours" min="0" step="0.5">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="task-actual-hours" class="form-label">Actual Hours</label>
                                <input type="number" class="form-control" id="task-actual-hours" min="0" step="0.5">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="task-notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="task-notes" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="save-task-btn">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Subtask Modal -->
    <div class="modal fade" id="subtaskModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="subtaskModalTitle">Add Subtask</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="subtask-form">
                        <input type="hidden" id="subtask-id">
                        <input type="hidden" id="subtask-major-task-id">
                        <div class="mb-3">
                            <label for="subtask-name" class="form-label">Subtask Name</label>
                            <input type="text" class="form-control" id="subtask-name" required>
                        </div>
                        <div class="mb-3">
                            <label for="subtask-description" class="form-label">Description</label>
                            <textarea class="form-control" id="subtask-description" rows="2"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="subtask-status" class="form-label">Status</label>
                                <select class="form-select" id="subtask-status">
                                    <option value="To Do" selected>To Do</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Completed">Completed</option>
                                    <option value="On Hold">On Hold</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="subtask-deadline" class="form-label">Deadline</label>
                                <input type="date" class="form-control" id="subtask-deadline">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="subtask-progress" class="form-label">Progress</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="subtask-progress" min="0" max="100" value="0">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="save-subtask-btn">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        // Global variables
        let periodsData = [];
        let tasksData = [];
        let usersData = [];
        let currentUser = null;

        // Utility functions
        function formatDate(dateString) {
            if (!dateString) return 'Not set';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
        }

        function getDaysRemaining(dateString) {
            if (!dateString) return null;
            
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            const deadline = new Date(dateString);
            deadline.setHours(0, 0, 0, 0);
            
            const diffTime = deadline - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            return diffDays;
        }

        function getStatusClass(status, type = 'task') {
            if (type === 'period') {
                switch (status?.toLowerCase()) {
                    case 'active': return 'status-active';
                    case 'completed': return 'status-completed';
                    case 'planned': return 'status-planned';
                    case 'archived': return 'status-archived';
                    default: return 'status-active';
                }
            } else {
                switch (status) {
                    case 'To Do': return 'status-to-do';
                    case 'In Progress': return 'status-in-progress';
                    case 'Completed': return 'status-completed-task';
                    case 'On Hold': return 'status-on-hold';
                    case 'Cancelled': return 'status-cancelled';
                    default: return 'status-to-do';
                }
            }
        }

        function getPriorityClass(priority) {
            switch (priority) {
                case 'Low': return 'priority-low';
                case 'Medium': return 'priority-medium';
                case 'High': return 'priority-high';
                case 'Critical': return 'priority-critical';
                default: return 'priority-medium';
            }
        }

        // API Functions
        /**
         * Fetches data from API with improved error handling
         * @param {string} url - The API endpoint URL
         * @returns {Promise<object>} - The parsed JSON response
         */
        async function fetchDataV2(url) {
            console.log("Fetching from:", url);
            try {
                const response = await fetch(url);
                
                // First get the text response
                const textResponse = await response.text();
                
                // Try to parse as JSON
                let jsonData;
                try {
                    jsonData = JSON.parse(textResponse);
                } catch (jsonError) {
                    console.error("JSON parse error:", jsonError);
                    console.error("Raw response:", textResponse);
                    
                    // Throw a more descriptive error
                    throw new Error(`Invalid JSON response from ${url}. Error: ${jsonError.message}`);
                }
                
                // Check if response contains an error status
                if (!response.ok || (jsonData && jsonData.status === 'error')) {
                    throw new Error(jsonData?.message || `HTTP error! Status: ${response.status}`);
                }
                
                return jsonData;
            } catch (error) {
                console.error(`Error fetching ${url}:`, error);
                throw error;
            }
        }

        /**
         * Handles the delete operation for an item
         * @param {string} type - The type of item to delete (period, majortask, subtask)
         * @param {number} id - The ID of the item to delete
         */
        async function handleDelete(type, id) {
            if (!confirm("Are you sure you want to delete this item? This action cannot be undone.")) {
                return;
            }
            
            try {
                // Show loading indicator
                const loadingEl = document.createElement('div');
                loadingEl.className = 'loading-overlay';
                loadingEl.innerHTML = '<div class="loading-spinner"></div><p>Deleting...</p>';
                document.body.appendChild(loadingEl);
                
                // Use the delete API endpoint
                const response = await fetchDataV2(`api/delete.php?type=${type}&id=${id}`);
                
                // Remove loading indicator
                document.body.removeChild(loadingEl);
                
                // Show success message
                alert(response.message || 'Item deleted successfully.');
                
                // Reload dashboard data
                loadAllData();
            } catch (error) {
                // Remove loading indicator if exists
                const loadingEl = document.querySelector('.loading-overlay');
                if (loadingEl) {
                    document.body.removeChild(loadingEl);
                }
                
                // Show error message
                alert(`Error deleting item: ${error.message}`);
            }
        }

        // Data loading functions
        async function loadUsers() {
            try {
                const response = await fetchDataV2('api/users.php?action=list');
                usersData = response.data || [];
                
                // Update user dropdowns
                const userSelect = document.getElementById('task-assigned-to');
                userSelect.innerHTML = '<option value="">Unassigned</option>';
                
                usersData.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = user.username;
                    userSelect.appendChild(option);
                });
                
                // Load current user
                await loadCurrentUser();
            } catch (error) {
                console.error('Error loading users:', error);
            }
        }

        async function loadCurrentUser() {
            try {
                const response = await fetchDataV2('api/users.php?action=current');
                
                if (response.status === 'success' && response.is_logged_in) {
                    currentUser = response.data;
                    document.getElementById('current-username').textContent = currentUser.username;
                }
            } catch (error) {
                console.error('Error loading current user:', error);
                // Not a critical error, so we don't need to show an alert
            }
        }

        async function loadPeriods() {
            try {
                const response = await fetchDataV2('api/periods.php?action=list');
                periodsData = response.data || [];
                
                // Update period dropdowns
                updatePeriodDropdowns();
                
                // Render periods on the periods page
                renderPeriods();
                
                // Update recent periods on dashboard
                renderRecentPeriods();
                
                // Update dashboard stats
                updateDashboardStats();
            } catch (error) {
                console.error('Error loading periods:', error);
                document.getElementById('periods-container').innerHTML = `
                    <div class="col-12">
                        <div class="error-container">
                            <h3>Error Loading Periods</h3>
                            <p>${error.message}</p>
                            <button onclick="loadPeriods()" class="btn btn-primary">Try Again</button>
                        </div>
                    </div>
                `;
            }
        }

        function updatePeriodDropdowns() {
            const periodSelects = [
                document.getElementById('task-period'),
                document.getElementById('filter-period')
            ];
            
            periodSelects.forEach(select => {
                if (!select) return;
                
                // Save current value
                const currentValue = select.value;
                
                // Clear options except first one (which is "No Period" or "All Periods")
                const firstOption = select.options[0];
                select.innerHTML = '';
                select.appendChild(firstOption);
                
                // Add period options
                periodsData.forEach(period => {
                    const option = document.createElement('option');
                    option.value = period.id;
                    option.textContent = period.name;
                    select.appendChild(option);
                });
                
                // Restore selected value if it still exists
                if (currentValue) {
                    select.value = currentValue;
                }
            });
        }

        async function loadTasks() {
            try {
                const response = await fetchDataV2('api/tasks.php?action=list');
                tasksData = response.data || [];
                
                // Render tasks list
                renderTasks();
                
                // Update task progress on dashboard
                renderTaskProgress();
                
                // Update upcoming deadlines
                renderUpcomingDeadlines();
                
                // Update dashboard stats
                updateDashboardStats();
            } catch (error) {
                console.error('Error loading tasks:', error);
                document.getElementById('tasks-list-container').innerHTML = `
                    <div class="error-container">
                        <h3>Error Loading Tasks</h3>
                        <p>${error.message}</p>
                        <button onclick="loadTasks()" class="btn btn-primary">Try Again</button>
                    </div>
                `;
            }
        }

        async function loadAllData() {
            try {
                // Show loading indicators
                document.getElementById('dashboard-content').innerHTML = `
                    <div class="loading-container">
                        <div class="loading-spinner"></div>
                        <p>Loading dashboard data...</p>
                    </div>
                `;
                
                // Load all data in parallel
                await Promise.all([
                    loadUsers(),
                    loadPeriods(),
                    loadTasks()
                ]);
                
                // Show dashboard content
                showSection('dashboard-section');
            } catch (error) {
                console.error('Failed to load initial dashboard data:', error);
                
                document.getElementById('dashboard-content').innerHTML = `
                    <div class="error-container">
                        <h3>Error Loading Dashboard</h3>
                        <p>${error.message}</p>
                        <button onclick="loadAllData()" class="btn btn-primary">Try Again</button>
                    </div>
                `;
            }
        }

        // Rendering functions
        function renderPeriods() {
            const container = document.getElementById('periods-container');
            
            if (!periodsData || periodsData.length === 0) {
                container.innerHTML = `
                    <div class="col-12">
                        <div class="welcome-container">
                            <h3>No Periods Found</h3>
                            <p>Get started by creating your first period to organize your tasks.</p>
                            <button onclick="showAddPeriodModal()" class="btn btn-primary">Create Period</button>
                        </div>
                    </div>
                `;
                return;
            }
            
            let html = '';
            
            periodsData.forEach(period => {
                const statusClass = getStatusClass(period.status, 'period');
                
                html += `
                    <div class="col-md-6 col-lg-4">
                        <div class="card period-card" style="border-left: 5px solid ${period.color_code || '#cccccc'}">
                            <div class="period-header">
                                <h3>${escapeHtml(period.name)}</h3>
                                <div class="period-actions">
                                    <button class="btn btn-sm btn-outline-primary" onclick="editPeriod(${period.id})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="handleDelete('period', ${period.id})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="period-details">
                                <p>${escapeHtml(period.description || 'No description')}</p>
                                <div class="period-dates">
                                    <span>Start: ${formatDate(period.start_date)}</span>
                                    <span>End: ${formatDate(period.end_date)}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="status-badge ${statusClass}">${period.status || 'Active'}</span>
                                    <span>Tasks: ${period.task_count || 0}</span>
                                </div>
                            </div>
                            <div class="period-footer">
                                <button class="btn btn-sm btn-primary" onclick="filterTasksByPeriod(${period.id})">
                                    <i class="fas fa-tasks me-1"></i> View Tasks
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = `<div class="row">${html}</div>`;
        }

        function renderRecentPeriods() {
            const container = document.getElementById('recent-periods-container');
            
            if (!periodsData || periodsData.length === 0) {
                container.innerHTML = '<p class="text-muted">No periods found.</p>';
                return;
            }
            
            // Get 3 most recent periods
            const recentPeriods = [...periodsData]
                .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
                .slice(0, 3);
            
            let html = '<ul class="list-group">';
            
            recentPeriods.forEach(period => {
                const statusClass = getStatusClass(period.status, 'period');
                
                html += `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="d-flex align-items-center">
                                <div style="width: 12px; height: 12px; background-color: ${period.color_code || '#cccccc'}; border-radius: 50%; margin-right: 10px;"></div>
                                <strong>${escapeHtml(period.name)}</strong>
                            </div>
                            <small class="text-muted">Tasks: ${period.task_count || 0}</small>
                        </div>
                        <span class="status-badge ${statusClass}">${period.status || 'Active'}</span>
                    </li>
                `;
            });
            
            html += '</ul>';
            
            if (recentPeriods.length < periodsData.length) {
                html += `
                    <div class="text-center mt-3">
                        <a href="#periods-section" class="btn btn-sm btn-outline-primary view-all-link" data-section="periods-section">
                            View All Periods
                        </a>
                    </div>
                `;
            }
            
            container.innerHTML = html;
        }

        function renderTasks(filters = {}) {
            const container = document.getElementById('tasks-list-container');
            
            // Apply filters
            let filteredTasks = [...tasksData];
            
            if (filters.periodId) {
                filteredTasks = filteredTasks.filter(task => 
                    task.period_id === filters.periodId || task.period_id === String(filters.periodId)
                );
            }
            
            if (filters.status) {
                filteredTasks = filteredTasks.filter(task => task.status === filters.status);
            }
            
            if (filters.priority) {
                filteredTasks = filteredTasks.filter(task => task.priority === filters.priority);
            }
            
            if (!filteredTasks || filteredTasks.length === 0) {
                container.innerHTML = `
                    <div class="card">
                        <div class="card-body text-center">
                            <p class="mb-3">No tasks found matching your criteria.</p>
                            <button class="btn btn-primary" id="create-task-btn" onclick="showAddTaskModal()">
                                <i class="fas fa-plus me-2"></i>Create Task
                            </button>
                        </div>
                    </div>
                `;
                return;
            }
            
            let html = '';
            
            filteredTasks.forEach(task => {
                const statusClass = getStatusClass(task.status);
                const priorityClass = getPriorityClass(task.priority);
                const daysRemaining = task.deadline ? getDaysRemaining(task.deadline) : null;
                
                let deadlineHtml = '';
                if (daysRemaining !== null) {
                    if (daysRemaining < 0) {
                        deadlineHtml = `<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>Overdue by ${Math.abs(daysRemaining)} day${Math.abs(daysRemaining) !== 1 ? 's' : ''}</span>`;
                    } else if (daysRemaining === 0) {
                        deadlineHtml = '<span class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Due today</span>';
                    } else if (daysRemaining <= 3) {
                        deadlineHtml = `<span class="text-warning"><i class="fas fa-clock me-1"></i>Due in ${daysRemaining} day${daysRemaining !== 1 ? 's' : ''}</span>`;
                    } else {
                        deadlineHtml = `<span><i class="far fa-calendar-alt me-1"></i>Due in ${daysRemaining} days</span>`;
                    }
                }
                
                html += `
                    <div class="card task-card ${priorityClass} mb-3">
                        <div class="task-header">
                            <div>
                                <h5 class="task-title">${escapeHtml(task.task_name)}</h5>
                                <div class="task-metadata">
                                    ${task.period_name ? `<span><i class="far fa-calendar-alt me-1"></i>${escapeHtml(task.period_name)}</span>` : ''}
                                    <span class="status-badge ${statusClass}">${task.status}</span>
                                    ${deadlineHtml ? `<span>${deadlineHtml}</span>` : ''}
                                    ${task.assigned_to_name ? `<span><i class="far fa-user me-1"></i>${escapeHtml(task.assigned_to_name)}</span>` : ''}
                                </div>
                            </div>
                            <div class="task-actions">
                                <button class="btn btn-sm btn-outline-primary" onclick="editTask(${task.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="handleDelete('majortask', ${task.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        ${task.description ? `<div class="task-description">${escapeHtml(task.description)}</div>` : ''}
                        
                        <div class="mt-2">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Progress: ${task.percent_complete}%</span>
                                <span><i class="fas fa-list me-1"></i>Subtasks: ${task.subtask_count || 0}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: ${task.percent_complete}%" 
                                     aria-valuenow="${task.percent_complete}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <button class="btn btn-sm btn-outline-primary" onclick="viewTaskDetails(${task.id})">
                                <i class="fas fa-eye me-1"></i>View Details
                            </button>
                            <button class="btn btn-sm btn-outline-success ms-2" onclick="addSubtask(${task.id})">
                                <i class="fas fa-plus me-1"></i>Add Subtask
                            </button>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }

          function renderTaskProgress() {
            const container = document.getElementById('tasks-progress-container');
            
            if (!tasksData || tasksData.length === 0) {
                container.innerHTML = '<p class="text-muted">No tasks found.</p>';
                return;
            }
            
            // Group tasks by status
            const tasksByStatus = {};
            const statusOrder = ['To Do', 'In Progress', 'On Hold', 'Completed', 'Cancelled'];
            
            statusOrder.forEach(status => {
                tasksByStatus[status] = tasksData.filter(task => task.status === status);
            });
            
            // Calculate status distribution
            const totalTasks = tasksData.length;
            const statusCounts = {};
            const statusPercentages = {};
            
            statusOrder.forEach(status => {
                statusCounts[status] = tasksByStatus[status].length;
                statusPercentages[status] = Math.round((statusCounts[status] / totalTasks) * 100) || 0;
            });
            
            // Create progress bar
            let progressBarHtml = '<div class="progress" style="height: 20px;">';
            const statusColors = {
                'To Do': '#3498db',
                'In Progress': '#f39c12',
                'On Hold': '#95a5a6',
                'Completed': '#2ecc71',
                'Cancelled': '#e74c3c'
            };
            
            statusOrder.forEach(status => {
                if (statusPercentages[status] > 0) {
                    progressBarHtml += `
                        <div class="progress-bar" role="progressbar" 
                            style="width: ${statusPercentages[status]}%; background-color: ${statusColors[status]}" 
                            aria-valuenow="${statusPercentages[status]}" aria-valuemin="0" aria-valuemax="100" 
                            title="${status}: ${statusCounts[status]} tasks">
                            ${statusPercentages[status] > 10 ? `${status} (${statusCounts[status]})` : ''}
                        </div>
                    `;
                }
            });
            progressBarHtml += '</div>';
            
            // Create legend
            let legendHtml = '<div class="d-flex flex-wrap mt-3 justify-content-between">';
            statusOrder.forEach(status => {
                legendHtml += `
                    <div class="me-3 mb-2">
                        <span class="badge" style="background-color: ${statusColors[status]};">&nbsp;</span>
                        ${status}: ${statusCounts[status]} 
                        (${statusPercentages[status]}%)
                    </div>
                `;
            });
            legendHtml += '</div>';
            
            // Create task completion rate
            const completedTasks = statusCounts['Completed'] || 0;
            const completionRate = Math.round((completedTasks / totalTasks) * 100) || 0;
            
            const completionHtml = `
                <div class="mt-4">
                    <h6>Overall Completion: ${completedTasks} of ${totalTasks} tasks (${completionRate}%)</h6>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: ${completionRate}%" 
                             aria-valuenow="${completionRate}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            `;
            
            container.innerHTML = progressBarHtml + legendHtml + completionHtml;
        }

        function renderUpcomingDeadlines() {
            const container = document.getElementById('upcoming-deadlines-container');
            
            if (!tasksData || tasksData.length === 0) {
                container.innerHTML = '<p class="text-muted">No tasks found.</p>';
                return;
            }
            
            // Get tasks with deadlines that are not completed or canceled
            const tasksWithDeadlines = tasksData.filter(task => 
                task.deadline && 
                task.status !== 'Completed' && 
                task.status !== 'Cancelled'
            );
            
            if (tasksWithDeadlines.length === 0) {
                container.innerHTML = '<p class="text-muted">No upcoming deadlines found.</p>';
                return;
            }
            
            // Sort by deadline
            tasksWithDeadlines.sort((a, b) => new Date(a.deadline) - new Date(b.deadline));
            
            // Get the next 5 deadlines
            const upcomingTasks = tasksWithDeadlines.slice(0, 5);
            
            let html = '<div class="table-responsive"><table class="table table-hover">';
            html += `
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Assignee</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
            `;
            
            upcomingTasks.forEach(task => {
                const statusClass = getStatusClass(task.status);
                const daysRemaining = getDaysRemaining(task.deadline);
                let deadlineClass = '';
                
                if (daysRemaining !== null) {
                    if (daysRemaining < 0) {
                        deadlineClass = 'text-danger fw-bold';
                    } else if (daysRemaining === 0) {
                        deadlineClass = 'text-warning fw-bold';
                    } else if (daysRemaining <= 3) {
                        deadlineClass = 'text-warning';
                    }
                }
                
                html += `
                    <tr>
                        <td>${escapeHtml(task.task_name)}</td>
                        <td class="${deadlineClass}">${formatDate(task.deadline)}</td>
                        <td><span class="status-badge ${statusClass}">${task.status}</span></td>
                        <td>${task.priority}</td>
                        <td>${task.assigned_to_name || 'Unassigned'}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="viewTaskDetails(${task.id})">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            html += '</tbody></table></div>';
            
            container.innerHTML = html;
        }

        function updateDashboardStats() {
            // Update period stats
            const activePeriods = periodsData.filter(period => period.status === 'active' || period.status === 'Active').length;
            document.getElementById('active-periods-count').textContent = activePeriods;
            
            // Update task stats
            const openTasks = tasksData.filter(task => task.status !== 'Completed' && task.status !== 'Cancelled').length;
            const completedTasks = tasksData.filter(task => task.status === 'Completed').length;
            
            document.getElementById('open-tasks-count').textContent = openTasks;
            document.getElementById('completed-tasks-count').textContent = completedTasks;
            
            // Update deadline stats
            const upcomingDeadlines = tasksData.filter(task => {
                if (task.deadline && task.status !== 'Completed' && task.status !== 'Cancelled') {
                    const daysRemaining = getDaysRemaining(task.deadline);
                    return daysRemaining !== null && daysRemaining >= 0 && daysRemaining <= 7;
                }
                return false;
            }).length;
            
            document.getElementById('upcoming-deadlines-count').textContent = upcomingDeadlines;
        }

        // Modal functions
        function showAddPeriodModal() {
            // Reset form
            document.getElementById('period-form').reset();
            document.getElementById('period-id').value = '';
            document.getElementById('period-status').value = 'active';
            document.getElementById('period-color').value = '#3498db';
            
            // Update modal title
            document.getElementById('periodModalTitle').textContent = 'Add Period';
            
            // Show modal
            const periodModal = new bootstrap.Modal(document.getElementById('periodModal'));
            periodModal.show();
        }

        function editPeriod(periodId) {
            const period = periodsData.find(p => p.id === periodId || p.id === String(periodId));
            
            if (!period) {
                alert('Period not found');
                return;
            }
            
            // Fill the form
            document.getElementById('period-id').value = period.id;
            document.getElementById('period-name').value = period.name;
            document.getElementById('period-description').value = period.description || '';
            document.getElementById('period-start-date').value = period.start_date || '';
            document.getElementById('period-end-date').value = period.end_date || '';
            document.getElementById('period-status').value = period.status || 'active';
            document.getElementById('period-color').value = period.color_code || '#3498db';
            
            // Update modal title
            document.getElementById('periodModalTitle').textContent = 'Edit Period';
            
            // Show modal
            const periodModal = new bootstrap.Modal(document.getElementById('periodModal'));
            periodModal.show();
        }

        function showAddTaskModal() {
            // Reset form
            document.getElementById('task-form').reset();
            document.getElementById('task-id').value = '';
            document.getElementById('task-status').value = 'To Do';
            document.getElementById('task-priority').value = 'Medium';
            document.getElementById('task-urgency').value = 'Soon';
            document.getElementById('task-importance').value = 'Important';
            document.getElementById('task-progress').value = '0';
            
            // Update modal title
            document.getElementById('taskModalTitle').textContent = 'Add Task';
            
            // Show modal
            const taskModal = new bootstrap.Modal(document.getElementById('taskModal'));
            taskModal.show();
        }

        function editTask(taskId) {
            const task = tasksData.find(t => t.id === taskId || t.id === String(taskId));
            
            if (!task) {
                alert('Task not found');
                return;
            }
            
            // Fill the form
            document.getElementById('task-id').value = task.id;
            document.getElementById('task-name').value = task.task_name;
            document.getElementById('task-period').value = task.period_id || '';
            document.getElementById('task-description').value = task.description || '';
            document.getElementById('task-priority').value = task.priority || 'Medium';
            document.getElementById('task-status').value = task.status || 'To Do';
            document.getElementById('task-urgency').value = task.urgency || 'Soon';
            document.getElementById('task-importance').value = task.importance || 'Important';
            document.getElementById('task-deadline').value = task.deadline || '';
            document.getElementById('task-assigned-to').value = task.assigned_to || '';
            document.getElementById('task-progress').value = task.percent_complete || '0';
            document.getElementById('task-working-with').value = task.working_with || '';
            document.getElementById('task-estimated-hours').value = task.estimated_hours || '';
            document.getElementById('task-actual-hours').value = task.actual_hours || '';
            document.getElementById('task-notes').value = task.notes || '';
            
            // Update modal title
            document.getElementById('taskModalTitle').textContent = 'Edit Task';
            
            // Show modal
            const taskModal = new bootstrap.Modal(document.getElementById('taskModal'));
            taskModal.show();
        }

        function addSubtask(taskId) {
            // Reset form
            document.getElementById('subtask-form').reset();
            document.getElementById('subtask-id').value = '';
            document.getElementById('subtask-major-task-id').value = taskId;
            document.getElementById('subtask-status').value = 'To Do';
            document.getElementById('subtask-progress').value = '0';
            
            // Update modal title
            document.getElementById('subtaskModalTitle').textContent = 'Add Subtask';
            
            // Show modal
            const subtaskModal = new bootstrap.Modal(document.getElementById('subtaskModal'));
            subtaskModal.show();
        }

        function viewTaskDetails(taskId) {
            // TODO: Implement task details view
            alert('Task details view will be implemented in a future update.');
        }

        // Form submission handlers
        document.getElementById('save-period-btn').addEventListener('click', async function() {
            const form = document.getElementById('period-form');
            
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            const periodData = {
                name: document.getElementById('period-name').value,
                description: document.getElementById('period-description').value,
                start_date: document.getElementById('period-start-date').value || null,
                end_date: document.getElementById('period-end-date').value || null,
                status: document.getElementById('period-status').value,
                color_code: document.getElementById('period-color').value
            };
            
            const periodId = document.getElementById('period-id').value;
            let url = 'api/periods.php?action=';
            let method = '';
            
            if (periodId) {
                // Update existing period
                url += 'update';
                method = 'PUT';
                periodData.id = periodId;
            } else {
                // Create new period
                url += 'create';
                method = 'POST';
                
                // Add created_by if current user is available
                if (currentUser) {
                    periodData.created_by = currentUser.id;
                }
            }
            
            try {
                const response = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(periodData)
                });
                
                const data = await response.json();
                
                if (data.status === 'error') {
                    throw new Error(data.message || 'Error saving period');
                }
                
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('periodModal')).hide();
                
                // Reload periods
                loadPeriods();
                
                // Show success message
                alert(periodId ? 'Period updated successfully' : 'Period created successfully');
            } catch (error) {
                alert('Error: ' + error.message);
            }
        });

        document.getElementById('save-task-btn').addEventListener('click', async function() {
            const form = document.getElementById('task-form');
            
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            const taskData = {
                task_name: document.getElementById('task-name').value,
                period_id: document.getElementById('task-period').value || null,
                description: document.getElementById('task-description').value || null,
                priority: document.getElementById('task-priority').value,
                status: document.getElementById('task-status').value,
                urgency: document.getElementById('task-urgency').value,
                importance: document.getElementById('task-importance').value,
                deadline: document.getElementById('task-deadline').value || null,
                assigned_to: document.getElementById('task-assigned-to').value || null,
                percent_complete: document.getElementById('task-progress').value,
                working_with: document.getElementById('task-working-with').value || null,
                estimated_hours: document.getElementById('task-estimated-hours').value || null,
                actual_hours: document.getElementById('task-actual-hours').value || null,
                notes: document.getElementById('task-notes').value || null
            };
            
            const taskId = document.getElementById('task-id').value;
            let url = 'api/tasks.php?action=';
            let method = '';
            
            if (taskId) {
                // Update existing task
                url += 'update';
                method = 'PUT';
                taskData.id = taskId;
            } else {
                // Create new task
                url += 'create';
                method = 'POST';
                
                // Add created_by if current user is available
                if (currentUser) {
                    taskData.created_by = currentUser.id;
                }
            }
            
            try {
                const response = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(taskData)
                });
                
                const data = await response.json();
                
                if (data.status === 'error') {
                    throw new Error(data.message || 'Error saving task');
                }
                
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('taskModal')).hide();
                
                // Reload tasks
                loadTasks();
                
                // Show success message
                alert(taskId ? 'Task updated successfully' : 'Task created successfully');
            } catch (error) {
                alert('Error: ' + error.message);
            }
        });

        document.getElementById('save-subtask-btn').addEventListener('click', async function() {
            const form = document.getElementById('subtask-form');
            
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            const subtaskData = {
                major_task_id: document.getElementById('subtask-major-task-id').value,
                task_name: document.getElementById('subtask-name').value,
                description: document.getElementById('subtask-description').value || null,
                status: document.getElementById('subtask-status').value,
                deadline: document.getElementById('subtask-deadline').value || null,
                percent_complete: document.getElementById('subtask-progress').value
            };
            
            const subtaskId = document.getElementById('subtask-id').value;
            let url = 'api/subtasks.php?action=';
            let method = '';
            
            if (subtaskId) {
                // Update existing subtask
                url += 'update';
                method = 'PUT';
                subtaskData.id = subtaskId;
            } else {
                // Create new subtask
                url += 'create';
                method = 'POST';
                
                // Add created_by if current user is available
                if (currentUser) {
                    subtaskData.created_by = currentUser.id;
                }
            }
            
            try {
                const response = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(subtaskData)
                });
                
                const data = await response.json();
                
                if (data.status === 'error') {
                    throw new Error(data.message || 'Error saving subtask');
                }
                
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('subtaskModal')).hide();
                
                // Reload tasks
                loadTasks();
                
                // Show success message
                alert(subtaskId ? 'Subtask updated successfully' : 'Subtask created successfully');
            } catch (error) {
                alert('Error: ' + error.message);
            }
        });

        // Filter handling
        document.getElementById('apply-filters-btn').addEventListener('click', function() {
            const filters = {
                periodId: document.getElementById('filter-period').value,
                status: document.getElementById('filter-status').value,
                priority: document.getElementById('filter-priority').value
            };
            
            renderTasks(filters);
        });

        function filterTasksByPeriod(periodId) {
            // Set the period filter value
            document.getElementById('filter-period').value = periodId;
            
            // Clear other filters
            document.getElementById('filter-status').value = '';
            document.getElementById('filter-priority').value = '';
            
            // Switch to tasks section
            showSection('tasks-section');
            
            // Apply the filter
            renderTasks({ periodId });
        }

        // Navigation handling
        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.content-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Show selected section
            document.getElementById(sectionId).classList.add('active');
            
            // Update active nav item
            document.querySelectorAll('.sidebar .nav-link').forEach(navLink => {
                navLink.classList.remove('active');
                
                if (navLink.getAttribute('data-section') === sectionId) {
                    navLink.classList.add('active');
                }
            });
        }

        document.querySelectorAll('.sidebar .nav-link').forEach(navLink => {
            navLink.addEventListener('click', function(e) {
                e.preventDefault();
                const sectionId = this.getAttribute('data-section');
                showSection(sectionId);
            });
        });

        document.querySelectorAll('.view-all-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const sectionId = this.getAttribute('data-section');
                showSection(sectionId);
            });
        });

        // Sidebar toggle
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });

        // Utility functions
        function escapeHtml(unsafe) {
            if (!unsafe) return '';
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Initial load
        document.addEventListener('DOMContentLoaded', function() {
            // Load all data
            loadAllData();
            
            // Set up refresh button for deadlines
            document.getElementById('refresh-deadlines-btn').addEventListener('click', function() {
                loadTasks();
            });
            
            // Set up add buttons
            document.getElementById('add-period-btn').addEventListener('click', showAddPeriodModal);
            document.getElementById('add-task-btn').addEventListener('click', showAddTaskModal);
        });
    </script>
</body>
</html>