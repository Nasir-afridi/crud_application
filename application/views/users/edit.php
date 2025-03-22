<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow-lg p-4">
            <h2 class="text-center mb-4">Edit User</h2>

                <?php if ($this->session->flashdata('error')) { ?>
                    <div class="alert alert-danger">
                        <?php echo $this->session->flashdata('error'); ?>
                    </div>
                    
                <?php } ?>
            <form action="<?php echo base_url('users/update/'.$user->id); ?>" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Name:</label>
                    <input type="text" class="form-control" name="name" value="<?php echo $user->name; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" name="email" value="<?php echo $user->email; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Leave blank to keep current password">
                </div>
                <div class="mb-3">
                    <label for="age" class="form-label">Age:</label>
                    <input type="number" class="form-control" name="age" value="<?php echo $user->age; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="skills" class="form-label">Skills:</label>
                    <textarea class="form-control" name="skills" required><?php echo $user->skills; ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address:</label>
                    <input type="text" class="form-control" name="address" value="<?php echo $user->address; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="designation" class="form-label">Designation:</label>
                    <input type="text" class="form-control" name="designation" value="<?php echo $user->designation; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="profile_picture" class="form-label">Profile Picture:</label>
                    <input type="file" class="form-control" name="profile_picture">
                    <input type="hidden" name="existing_profile_picture" value="<?php echo $user->profile_picture; ?>">
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>