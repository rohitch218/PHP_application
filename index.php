<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }
        
        .table-container {
            padding: 30px;
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9em;
            letter-spacing: 0.5px;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        tbody tr {
            transition: background-color 0.3s ease;
        }
        
        tbody tr:hover {
            background-color: #f5f5f5;
        }
        
        tbody tr:nth-child(even) {
            background-color: #fafafa;
        }
        
        .status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }
        
        .status.active {
            background-color: #4caf50;
            color: white;
        }
        
        .status.inactive {
            background-color: #f44336;
            color: white;
        }
        
        .status.pending {
            background-color: #ff9800;
            color: white;
        }
        
        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.8em;
            }
            
            table {
                font-size: 0.9em;
            }
            
            th, td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>👥 User Management System</h1>
            <p>Dummy User Data Display</p>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Department</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Dummy user data
                    $users = [
                        ['id' => 1, 'name' => 'John Doe', 'email' => 'john.doe@example.com', 'phone' => '+1-555-0101', 'department' => 'Engineering', 'status' => 'active'],
                        ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane.smith@example.com', 'phone' => '+1-555-0102', 'department' => 'Marketing', 'status' => 'active'],
                        ['id' => 3, 'name' => 'Robert Johnson', 'email' => 'robert.j@example.com', 'phone' => '+1-555-0103', 'department' => 'Sales', 'status' => 'active'],
                        ['id' => 4, 'name' => 'Emily Davis', 'email' => 'emily.davis@example.com', 'phone' => '+1-555-0104', 'department' => 'HR', 'status' => 'pending'],
                        ['id' => 5, 'name' => 'Michael Brown', 'email' => 'michael.b@example.com', 'phone' => '+1-555-0105', 'department' => 'Engineering', 'status' => 'active'],
                        ['id' => 6, 'name' => 'Sarah Wilson', 'email' => 'sarah.w@example.com', 'phone' => '+1-555-0106', 'department' => 'Finance', 'status' => 'active'],
                        ['id' => 7, 'name' => 'David Lee', 'email' => 'david.lee@example.com', 'phone' => '+1-555-0107', 'department' => 'Operations', 'status' => 'inactive'],
                        ['id' => 8, 'name' => 'Lisa Anderson', 'email' => 'lisa.a@example.com', 'phone' => '+1-555-0108', 'department' => 'Marketing', 'status' => 'active'],
                        ['id' => 9, 'name' => 'James Taylor', 'email' => 'james.t@example.com', 'phone' => '+1-555-0109', 'department' => 'Sales', 'status' => 'pending'],
                        ['id' => 10, 'name' => 'Maria Garcia', 'email' => 'maria.g@example.com', 'phone' => '+1-555-0110', 'department' => 'Engineering', 'status' => 'active'],
                    ];
                    
                    foreach ($users as $user) {
                        $statusClass = $user['status'];
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['phone']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['department']) . "</td>";
                        echo "<td><span class='status {$statusClass}'>" . ucfirst(htmlspecialchars($user['status'])) . "</span></td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

