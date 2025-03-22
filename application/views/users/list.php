<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .welcome-message {
            font-size: 1.5rem;
            font-weight: bold;
            color: #007bff;
            text-align: center;
            margin-top: 20px;
        }
        .logout-btn {
            display: block;
            width: 150px;
            margin: 20px auto;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <!-- Welcome Message -->
        <div class="welcome-message">
            Hey <?php echo $username; ?>, Welcome to CodeIgniter 3 CRUD Application! You have logged in successfully.
        </div>

        <!-- Logout Button -->
        <a href="<?php echo base_url('logout'); ?>" class="btn btn-danger logout-btn">Logout</a>

        <div class="card shadow-lg p-4">
            <h2 class="mb-4 text-center">Users List</h2>
            <div class="text-end mb-3">
                <a href="<?php echo base_url('users/add'); ?>" class="btn btn-primary">+ Add New User</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Age</th>
                            <th>Skills</th>
                            <th>Address</th>
                            <th>Designation</th>
                            <th>Profile Picture</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user->id; ?></td>
                            <td><?php echo $user->name; ?></td>
                            <td><?php echo $user->email; ?></td>
                            <td><?php echo $user->age; ?></td>
                            <td><?php echo $user->skills; ?></td>
                            <td><?php echo $user->address; ?></td>
                            <td><?php echo $user->designation; ?></td>
                            <td>
                                <?php if ($user->profile_picture): ?>
                                    <img src="<?php echo base_url($user->profile_picture); ?>" alt="Profile Picture" class="rounded-circle" width="50">
                                <?php else: ?>
                                    <span class="text-muted">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo base_url('users/edit/'.$user->id); ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="<?php echo base_url('users/delete/'.$user->id); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>