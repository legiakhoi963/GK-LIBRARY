<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Library Management System</title>
        <link rel="icon" href="http://localhost/library/assets/img/book-solid.png" />
        <link href="http://localhost/library/assets/css/styles.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>

    <?php 
    if(is_admin_login())
    {
    ?>
    
    <body class="sb-nav-fixed">

        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="index.php">
                <i class="fa-solid fa-book me-2"></i>
                Library System
            </a>

            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                <!-- <div class="input-group">
                    <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                    <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
                </div> -->
            </form>

            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>

        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <a class="nav-link" href="index.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-house"></i></div>
                                Dashboard
                            </a>
                            <a class="nav-link" href="publisher.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-users-rectangle"></i></div>
                                Publisher
                            </a>
                            <a class="nav-link" href="category.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-tags"></i></div>
                                Category
                            </a>
                            <a class="nav-link" href="author.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-address-card"></i></div>
                                Author
                            </a>
                            <a class="nav-link" href="book.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-book-open"></i></div>
                                Book
                            </a>
                            <a class="nav-link" href="issue_book.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-table"></i></div>
                                Issue Book
                            </a>
                            <a class="nav-link" href="user.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-user-group"></i></div>
                                Manage User
                            </a>
                        </div>
                    </div>

                    <?php
                    $admin_name_query = "
	                SELECT admin_name FROM admin 
                    WHERE admin_id = ".$_SESSION['admin_id']."
                    ";
                    $admin_name_result = $connect->prepare($admin_name_query);
                    $admin_name_result->execute();
                    foreach ($admin_name_result as $row){
                    ?>

                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as Admin</div>
                        <?php echo $row['admin_name'];} ?>
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>


    <?php
    }
    else
    {
    ?>

    <body>
    	<main>
    		<div class="container py-2">
    			<header class="pb-2 mb-3 border-bottom">
                    <div class="row">
        			    <div class="col-md-4">
                            <a href="index.php" class="d-flex align-items-center text-dark text-decoration-none">
                                <span class="navbar-brand fs-4">
                                    <i class="fa-solid fa-book me-2"></i>
                                    Library Management System
                                </span>
                            </a>
                        </div>

                        <div class="col-md-8">
                            <?php 
                            
                            if(is_user_login())
                            {
                                $user_name_query = "
                                SELECT DISPLAY_USER_NAME(".$_SESSION['user_id'].") AS user_name; 
                                ";
                                $user_name_result = $connect->prepare($user_name_query);
                                $user_name_result->closeCursor();
                                $user_name_result->execute();
                                foreach ($user_name_result as $row){
                            ?>
                            <ul class="list-inline mt-1 mb-0 float-end">
                                <li class="list-inline-item">
                                    <?php
                                        echo '
                                            <div class="btn btn-dark text-light disabled">
                                            '.$row['user_name'].'
                                            </div>
                                        ';}
                                    ?>
                                </li>
                                <li class="list-inline-item"><a class="btn btn-primary" href="issue_book_details.php">Issue Book</a></li>
                                <li class="list-inline-item"><a class="btn btn-success" href="search_book.php">Search Book</a></li>
                                <li class="list-inline-item"><a class="btn btn-warning" href="profile.php">Profile</a></li>
                                <li class="list-inline-item"><a class="btn btn-danger" href="logout.php">Logout</a></li>
                            </ul>
                            <?php 
                            }
                            ?>
                        </div>
                    </div>
                </header>
    <?php 
    }
    ?>