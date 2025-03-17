<?php
$insert = false;
$server = "localhost";
$username = "root";
$password = "";
$database = "notes";

$conn = mysqli_connect($server, $username, $password, $database);
if (!$conn) {
    die("Sorry, connection is unsuccessful with error: " . mysqli_connect_error());
}

// Handle Insert
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['title']) && isset($_POST['description'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];

    $sql = "INSERT INTO notes(title, description) VALUES ('$title', '$description')";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $insert = true;
    } else {
        echo "Not inserted";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $sno = $_GET['delete'];
    $sql = "DELETE FROM notes WHERE sno = $sno";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        header("Location: index.php"); // Reload page to update the list
    }
}

// Handle Update for Editing
if (isset($_POST['editSno'])) {
    $sno = $_POST['editSno'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    $sql = "UPDATE notes SET title='$title', description='$description' WHERE sno=$sno";
    if (mysqli_query($conn, $sql)) {
        echo "Note updated successfully!";
    } else {
        echo "Error updating note: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Notesly-Last Minute Notes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script>
        $(document).ready(function () {
            $('#myTable').DataTable();

            // Show modal with data when edit button is clicked
            $(".edit-btn").click(function () {
                const sno = $(this).data('sno');
                const title = $(this).data('title');
                const description = $(this).data('description');

                $("#editSno").val(sno);
                $("#editTitle").val(title);
                $("#editDescription").val(description);
                $("#editModal").modal("show");
            });
        });
    </script>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Notesly-Last Minute Notes</a>
        </div>
    </nav>

    <!-- Alert for successful insertion -->
    <?php
    if ($insert) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong>Success!</strong> Your Note has been inserted successfully.
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
    }
    ?>

    <!-- Form to Add Note -->
    <div class="container my-4">
        <h2>Add Your Note</h2>
        <form action="index.php" method="post">
            <div class="mb-3">
                <label for="title" class="form-label">Note Title</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="Enter the title" />
            </div>
            <div class="mb-3">
                <label for="desc" class="form-label">Note Description</label>
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Note</button>
        </form>
    </div>

    <!-- Notes Table -->
    <div class="container my-4">
        <table class="table" id="myTable">
            <thead>
                <tr>
                    <th scope="col">S.no</th>
                    <th scope="col">Title</th>
                    <th scope="col">Description</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $sql = "SELECT * FROM notes ORDER BY sno"; // Ensure notes are ordered by sno
                $result = mysqli_query($conn, $sql);
                $counter = 1; // Start counter for row number
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <th scope='row'>" . $counter++ . "</th> <!-- Dynamic serial number -->
                            <td>" . $row['title'] . "</td>
                            <td>" . $row['description'] . "</td>
                            <td>
                                <button class='btn btn-warning edit-btn' data-sno='" . $row['sno'] . "' data-title='" . $row['title'] . "' data-description='" . $row['description'] . "'>Edit</button> 
                                <a href='?delete=" . $row['sno'] . "' class='btn btn-danger'>Delete</a>
                            </td>
                        </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal for Editing Note -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form to edit note -->
                    <form action="index.php" method="POST">
                        <input type="hidden" id="editSno" name="editSno">

                        <div class="mb-3">
                            <label for="editTitle" class="form-label">Note Title</label>
                            <input type="text" €class="form-control" id="editTitle" name="title" />
                        </div>
                        <div class="mb-3">
                            <label for="editDescription" class="form-label">Note Description</label>
                            <textarea class="form-control" id="editDescription" name="description"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
