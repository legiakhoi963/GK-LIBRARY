<?php
include '../database_connection.php';
include '../function.php';

if(!is_admin_login())
{
	header('location:../admin_login.php');
}

$error = '';

if(isset($_POST["issue_book_button"]))
{
    $formdata = array();

    if(empty($_POST["book_name"]))
    {
        $error .= '<li>Book Name is required</li>';
    }
    else
    {
        $formdata['book_name'] = trim($_POST['book_name']);
    }

    if(empty($_POST["user_id"]))
    {
        $error .= '<li>User Name is required</li>';
    }
    else
    {
        $formdata['user_id'] = trim($_POST['user_id']);
    }

    if($error == '')
    {
        //Check Book Available or Not

        $query = "
        SELECT * FROM book 
        WHERE book_name = '".$formdata['book_name']."'
        ";

        $statement = $connect->prepare($query);
        $statement->execute();

        if($statement->rowCount() > 0)
        {
            foreach($statement->fetchAll() as $book_row)
            {
                //check book is available or not
                if($book_row['book_status'] == 'Enable' && $book_row['book_copies'] > 0)
                {
                    //Check User is exist

                    $query = "
                    SELECT * FROM user 
                    WHERE user_id = '".$formdata['user_id']."'
                    ";

                    $statement = $connect->prepare($query);
                    $statement->execute();

                    if($statement->rowCount() > 0)
                    {
                        foreach($statement->fetchAll() as $user_row)
                        {
                            if($user_row['user_status'] == 'Enable')
                            {
                                //Check User Total issue of Book

                                $book_issue_limit = get_book_issue_limit_per_user($connect);

                                $total_book_issue = get_total_book_issue_per_user($connect, $formdata['user_id']);

                                if($total_book_issue < $book_issue_limit)
                                {
                                    $total_book_issue_day = get_total_book_issue_day($connect);

                                    $today_date = get_date_time($connect);

                                    $expected_return_date = date('d-m-Y H:i:s', strtotime($today_date. ' + '.$total_book_issue_day.' days'));

                                    $admin_id = $_SESSION['admin_id'];

                                    $data = array(
                                        ':book_id'                          =>  $book_row['book_id'],
                                        ':user_id'                          =>  $formdata['user_id'],
                                        ':admin_id'			                =>	$admin_id,
                                        ':issue_book_issue_date'            =>  $today_date,
                                        ':issue_book_return_date'           =>  $expected_return_date,
                                        ':issue_book_real_return_date'      =>  '',
                                        ':issue_book_fines'                 =>  0,
                                        ':issue_book_status'                =>  'Issue'
                                    );

                                    $query = "
                                    INSERT INTO issue_book 
                                    (book_id, user_id, admin_id, issue_book_issue_date, issue_book_return_date, issue_book_real_return_date, issue_book_fines, issue_book_status) 
                                    VALUES (:book_id, :user_id, :admin_id, :issue_book_issue_date, :issue_book_return_date, :issue_book_real_return_date, :issue_book_fines, :issue_book_status)
                                    ";

                                    $statement = $connect->prepare($query);
                                    $statement->execute($data);

                                    $query = "
                                    UPDATE book 
                                    SET book_copies = book_copies - 1
                                    WHERE book_name = '".$formdata['book_name']."' 
                                    ";

                                    $connect->query($query);

                                    header('location:issue_book.php?msg=add');
                                }
                                else
                                {
                                    $error .= '<li>User has reached Book Issue Limit</li>';
                                }
                            }
                            else
                            {
                                $error .= '<li>User Account is Disable, Contact Admin</li>';
                            }
                        }
                    }
                    else
                    {
                        $error .= '<li>User not Found</li>';
                    }
                }
                else
                {
                    $error .= '<li>Book not Available</li>';
                }
            }
        }
        else
        {
            $error .= '<li>Book not Found</li>';
        }
    }
}

if(isset($_GET["action"], $_GET["id"], $_GET["book"], $_GET["status"]) && $_GET["action"] == 'return')
{
    $data = array(
        ':issue_book_real_return_date'     =>  get_date_time($connect),
        ':issue_book_status'               =>  $_GET["status"],
        ':issue_book_id'                    =>  $_GET['id']
    );  

    $query = "
    UPDATE issue_book 
    SET issue_book_real_return_date = :issue_book_real_return_date, 
    issue_book_status = :issue_book_status 
    WHERE issue_book_id = :issue_book_id
    ";

    $statement = $connect->prepare($query);
    $statement->execute($data);

    $query = "
    UPDATE book 
    SET book_copies = book_copies + 1 
    WHERE book_id = '".$_GET["book"]."'
    ";

    $connect->query($query);

    header("location:issue_book.php?msg=return");
}

if(isset($_GET["action"], $_GET["id"], $_GET["book"], $_GET["user"]) && $_GET["action"] == 'accept')
{
    //Check Book Available or Not
    $issue_book_id = $_GET["id"];
    $book_id = $_GET["book"];
    $user_id = $_GET["user"];

    $query = "
    SELECT * FROM book 
    WHERE book_id = '".$book_id."'
    ";

    $statement = $connect->prepare($query);
    $statement->execute();

    if($statement->rowCount() > 0)
    {
        foreach($statement->fetchAll() as $book_row)
        {
            //check book is available or not
            if($book_row['book_status'] == 'Enable' && $book_row['book_copies'] > 0)
            {
                //Check User is exist

                $query = "
                SELECT * FROM user 
                WHERE user_id = '".$user_id."'
                ";

                $statement = $connect->prepare($query);
                $statement->execute();

                if($statement->rowCount() > 0)
                {
                    foreach($statement->fetchAll() as $user_row)
                    {
                        if($user_row['user_status'] == 'Enable')
                        {
                            //Check User Total issue of Book

                            $book_issue_limit = get_book_issue_limit_per_user($connect);

                            $total_book_issue = get_total_book_issue_per_user($connect, $user_id);

                            if($total_book_issue < $book_issue_limit)
                            {
                                $total_book_issue_day = get_total_book_issue_day($connect);

                                $today_date = get_date_time($connect);

                                $expected_return_date = date('d-m-Y H:i:s', strtotime($today_date. ' + '.$total_book_issue_day.' days'));

                                $admin_id = $_SESSION['admin_id'];

                                $data = array(
                                    ':issue_book_id'                    =>  $issue_book_id,
                                    ':admin_id'			                =>	$admin_id,
                                    ':issue_book_issue_date'            =>  $today_date,
                                    ':issue_book_return_date'           =>  $expected_return_date,
                                    ':issue_book_real_return_date'      =>  '',
                                    ':issue_book_fines'                 =>  0,
                                    ':issue_book_status'                =>  'Issue'
                                );

                                $query = "
                                UPDATE issue_book
                                SET admin_id                         = :admin_id,
                                    issue_book_issue_date            = :issue_book_issue_date,
                                    issue_book_return_date           = :issue_book_return_date,
                                    issue_book_real_return_date      = :issue_book_real_return_date,
                                    issue_book_fines                 = :issue_book_fines,
                                    issue_book_status                = :issue_book_status
                                WHERE issue_book_id = :issue_book_id
                                ";

                                $statement = $connect->prepare($query);
                                $statement->execute($data);

                                $query = "
                                UPDATE book 
                                SET book_copies = book_copies - 1
                                WHERE book_id = '".$book_id."' 
                                ";

                                $connect->query($query);

                                header('location:issue_book.php?msg=add');
                            }
                            else
                            {
                                header('location:issue_book.php?msg=userreachlimit');
                            }
                        }
                        else
                        {
                            header('location:issue_book.php?msg=userdisabled');
                        }
                    }
                }
                // else
                // {
                //     header('location:issue_book.php?msg=usernotfound');
                // }
            }
            else
            {
                header('location:issue_book.php?msg=booknotavailable');
            }
        }
    }
    // else
    // {
    //     header('location:issue_book.php?msg=booknotfound');
    // }

}

if(isset($_GET["action"], $_GET["id"]) && $_GET["action"] == 'decline')
{
    $data = array(
		':issue_book_status'			=>	'Decline',
		':issue_book_id'				=>	$_GET["id"]
	);

    $query = "
	UPDATE issue_book
	SET issue_book_status = :issue_book_status
	WHERE issue_book_id = :issue_book_id 
	";

    $statement = $connect->prepare($query);
	$statement->execute($data);

    header('location:issue_book.php?msg=decline');
}

$query = "
	SELECT * FROM issue_book 
    ORDER BY issue_book_id DESC
";

$statement = $connect->prepare($query);
$statement->execute();

include '../header.php';
?>

<div class="container-fluid px-4">
	<div class="row mt-3">
		<div class="col-md-12">
			<h1>Issue Book</h1>
		</div>
    </div>

    <div class="col-md-12 mb-3">
		<?php 
		if($error != '')
        {
            echo '<div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                    <ul class="mb-0">'.$error.'</ul> 
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
        }

        if(isset($_GET['msg']))
        {
            if($_GET['msg'] == 'add')
            {
                echo '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">New Book Issue Successfully<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }

            if($_GET["msg"] == 'return')
            {
                echo '
                <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">Issued Book Successfully Return To Library <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
                ';
            }

            if($_GET["msg"] == 'decline')
            {
                echo '
                <div class="alert alert-warning alert-dismissible fade show mb-0" role="alert">Request Declined<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
                ';
            }

            // if($_GET["msg"] == 'booknotfound')
            // {
            //     echo '
            //     <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">Book Not Found<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            //     ';
            // }

            if($_GET["msg"] == 'booknotavailable')
            {
                echo '
                <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">Book Not Available<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
                ';
            }

            // if($_GET["msg"] == 'usernotfound')
            // {
            //     echo '
            //     <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">User Not Found<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            //     ';
            // }

            if($_GET["msg"] == 'userdisabled')
            {
                echo '
                <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">User Is Disabled<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
                ';
            }

            if($_GET["msg"] == 'userreachlimit')
            {
                echo '
                <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">User Has Reached Book Limit<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
                ';
            }
        }
		?>
	</div>

    <!-- Add -->
    <?php 
    if(isset($_GET["action"]))
    {
        if($_GET["action"] == 'add')
        {
    ?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">
                    <div class="col col-md-6 mt-1">
                        <i class="fas fa-user-plus me-1"></i> Issue New Book
                    </div>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">User</label>
                            <select name="user_id" id="user_id" class="form-control">
                                <?php echo fill_user($connect); ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Book</label>
                            <select name="book_name" id="book_name" class="form-control">
                                <?php echo fill_book($connect); ?>
                            </select>

                        </div>
                        <div class="mt-3 mb-0">
                            <input type="submit" name="issue_book_button" value="Book Issue" class="btn btn-primary" />
                        </div>  
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Add -->

    <?php 
        }
    }
    else 
    {
    ?>

    <!-- Table -->
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header">
                    <div class="row">
                        <div class="col col-md-6 mt-1">
                            <i class="fas fa-table me-1"></i> Issue Book Management
                        </div>
                        <div class="col col-md-6" align="right">
                            <a href="issue_book.php?action=add" class="btn btn-success btn-sm">Add</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="datatablesSimple">
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>Book Name</th>
                                <th>Admin Name</th>
                                <th>Issue Date</th>
                                <th>Return Date</th>
                                <th>Real Return Date</th>
                                <th>Fines</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>User Name</th>
                                <th>Book Name</th>
                                <th>Admin Name</th>
                                <th>Issue Date</th>
                                <th>Return Date</th>
                                <th>Real Return Date</th>
                                <th>Fines</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                        <tbody>
                        <?php
                        if($statement->rowCount() > 0)
                        {
                            $one_day_fine = get_one_day_fines($connect);

                            foreach($statement->fetchAll() as $row)
                            {
                                $query = "
                                    SELECT * FROM book
                                    WHERE book_id = '".$row["book_id"]."'
                                ";
                                $book_data = $connect->prepare($query);
                                $book_data->execute();

                                $query = "
                                SELECT * FROM user 
                                WHERE user_id = '".$row["user_id"]."'
                                ";
                                $user_data = $connect->prepare($query);
                                $user_data->execute();

                                $query = "
                                SELECT * FROM admin 
                                WHERE admin_id = '".$row["admin_id"]."'
                                ";
                                $admin_data = $connect->prepare($query);
                                $admin_data->execute();

                                $status = $row["issue_book_status"];
                                $book_fines = $row["issue_book_fines"];

                                if($row["issue_book_status"] == "Issue")
                                {
                                    $current_date_time = new DateTime(get_date_time($connect));
                                    $expected_return_date = new DateTime($row["issue_book_return_date"]);

                                    if($current_date_time > $expected_return_date)
                                    {
                                        $interval = $current_date_time->diff($expected_return_date);

                                        $total_day = $interval->d;

                                        $book_fines = $total_day * $one_day_fine;

                                        $status = 'Not Return';

                                        $query = "
                                        UPDATE issue_book 
                                                            SET issue_book_fines = '".$book_fines."', 
                                                            issue_book_status = '".$status."' 
                                                            WHERE issue_book_id = '".$row["issue_book_id"]."'
                                        ";

                                        $connect->query($query);
                                    }
                                }

                                if($row["issue_book_status"] == "Not Return")
                                {
                                    $current_date_time = new DateTime(get_date_time($connect));
                                    $expected_return_date = new DateTime($row["issue_book_return_date"]);

                                    if($current_date_time > $expected_return_date)
                                    {
                                        $interval = $current_date_time->diff($expected_return_date);

                                        $total_day = $interval->d;

                                        $book_fines = $total_day * $one_day_fine;

                                        $status = 'Not Return';

                                        $query = "
                                        UPDATE issue_book 
                                        SET issue_book_fines = '".$book_fines."', 
                                        issue_book_status = '".$status."' 
                                        WHERE issue_book_id = '".$row["issue_book_id"]."'
                                        ";

                                        $connect->query($query);
                                    }
                                }

                                if($status == 'Issue')
                                {
                                    $status = '<span class="badge bg-warning">Issue</span>';
                                }

                                if($status == 'Not Return')
                                {
                                    $status = '<span class="badge bg-danger">Not Return</span>';
                                }

                                if($status == 'Return')
                                {
                                    $status = '<span class="badge bg-success">Return</span>';
                                }

                                if($status == 'Pending')
                                {
                                    $status = '<span class="badge bg-primary">Pending</span>';
                                }

                                if($status == 'Decline')
                                {
                                    $status = '<span class="badge bg-secondary">Decline</span>';
                                }

                                echo '
                                <tr>
                                ';

                                foreach($user_data as $user_data)
                                {
                                echo '
                                    <td>'
                                        .$user_data ["user_name"].
                                        "<br>"
                                        .$user_data ["user_email"].
                                    '</td>
                                ';
                                }

                                foreach($book_data as $book_data)
                                {
                                echo '
                                    <td>'
                                        .$book_data ["book_name"].
                                        "<br>"
                                        .$book_data ["book_code"].
                                    '</td>
                                ';
                                }

                                foreach($admin_data as $admin_data)
                                {
                                echo '
                                    <td>'
                                        .$admin_data ["admin_name"].
                                        "<br>"
                                        .$admin_data ["admin_email"].
                                    '</td>
                                ';
                                }

                                echo '
                                    <td>'.$row["issue_book_issue_date"].'</td>
                                    <td>'.$row["issue_book_return_date"].'</td>
                                    <td>'.$row["issue_book_real_return_date"].'</td>
                                    <td>'
                                        .$book_fines.
                                        "<br>"
                                        .'VND'.
                                    '</td>
                                    <td>'.$status.'</td>
                                ';

                                if($row["issue_book_status"] == "Return" || $row["issue_book_status"] == "Decline") 
                                {
                                echo '
                                    <td></td>
                                </tr>
                                ';
                                }

                                if($row["issue_book_status"] == "Pending")
                                {
                                echo '
                                    <td>
                                        <div class="col">
                                            <div class="row mb-1">
                                                <button
                                                    class="btn btn-success btn-sm" 
                                                    onclick="accept(`'.$row["issue_book_id"].'`, `'.$row["user_id"].'`, `'.$row["book_id"].'`)">
                                                    Accept
                                                </button>
                                            </div>

                                            <div class="row mb-1">
                                                <button
                                                    class="btn btn-danger btn-sm" 
                                                    onclick="decline(`'.$row["issue_book_id"].'`)">
                                                    Decline
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                ';
                                }

                                if($row["issue_book_status"] == "Not Return" || $row["issue_book_status"] == "Issue")
                                {
                                echo '
                                    <td>
                                        <div class="row">
                                            <button
                                                class="btn btn-warning btn-sm" 
                                                onclick="change_status(`'.$row["issue_book_id"].'`, `'.$row["book_id"].'`, `'.$row["issue_book_status"].'`)">
                                                Return
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                ';
                                }
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                    <script>
                        function accept(id, user_id, book_id) {
                            if(confirm("Are you sure you want to Accept this Request?"))
                            {
                                window.location.href="issue_book.php?action=accept&id="+id+"&user="+user_id+"&book="+book_id+"";
                            }
                        }

                        function decline(id) {
                            if(confirm("Are you sure you want to Decline this Request?"))
                            {
                                window.location.href="issue_book.php?action=decline&id="+id+"";
                            }
                        }

                        function change_status(id, book_id, status)
                        {
                            var new_status = 'Return';

                            if(status == 'Issue')
                            {
                                new_status = 'Return';
                            }

                            if(status == 'Not Return')
                            {
                                new_status = 'Return';
                            }

                            if(confirm("Are you sure you want to "+new_status+" this Book?"))
                            {
                                window.location.href="issue_book.php?action=return&id="+id+"&book="+book_id+"&status="+new_status+"";
                            }
                        }
                    </script>
                </div>
            </div>
        </div> 
    </div>
    <!-- Table -->
    <?php
    }
    ?>
</div>

<?php 
include '../footer.php';
?>