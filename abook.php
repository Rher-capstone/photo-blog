<?php
session_start();
include_once("includes/db-conn.php");

$CURRENT_PAGE = "Alphabet Book";
if (!isset($_SESSION['current_user_email'])) {
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include("includes/head-tag-contents.php");?>
    <link rel="stylesheet" href="css/alpha.css">
    <link rel="stylesheet" href="css/print-page.css">
</head>

<body>
    <?php include("includes/top-bar.php");?>
    <?php include("includes/abook.modal.php");?>
    <?php include("includes/abook.newbook.modal.php");?>
    <?php include("includes/abook.bookedit.modal.php");?>
    
    <h2 style="text-align: center">Alphabet Book</h2>
    <div class='container' id="main-body">
        <div class="container" id='book-bar'>
            <div class="row" id="book-bar-row"></div>
        </div>

        <div class="container" id='book-content'>
            <div id="util-bar">
                <div class="col-8" id="progress-container">
                    <div class="row">
                        <div class="col">
                            <h2 style="text-align: center;" id="prog-header">Book</h2>
                        </div>

                    </div>
                    <div class="progress" role="progressbar" aria-label="Basic example" aria-valuenow="0"
                        aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar" id='progress-bar'></div>
                        <div class="progress-bar bg-info" id="progress-bar-pending"></div>
                        <div class="progress-bar bg-danger" id="progress-bar-regress"></div>
                    </div>

                </div>

                <div id="button-container">
                    <button type="button" class="btn btn-primary disabled" id='update-button'>Save Book</button>
                    <button type="button" class="btn btn-danger" id='delete-button'>Delete Book</button>
                    <button type="button" class="btn btn-primary" id='edit-button' data-bs-target="#book-edit-modal" data-bs-toggle="modal">Edit Book</button>
                    <button type="button" class="btn btn-primary" id='print-button' onclick="">Print Book</button>
                </div>


            </div>

            <!-- Styling to grid for height -->
            <div class='container' id='alpha-grid'>
                <div id="alpha-grid-row"></div>
            </div>

        </div>
    </div>
    <?php include("includes/footer.php");?>
    <script src="js/print-blogs.js"></script>
    <script src="js/abook-grid.js" defer></script>
</body>

</html>