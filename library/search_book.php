<?php
include 'database_connection.php';
include 'function.php';

if(!is_user_login())
{
	header('location:user_login.php');
}

$error = '';

if(isset($_GET["action"], $_GET["book"], $_GET["user"]) && $_GET["action"] == 'borrow')
{

	$book_request_limit = get_book_request_limit_per_user($connect);

	$total_book_request = get_total_book_request_per_user($connect, $_GET["user"]);

	$today_date = get_date_time($connect);

	$user_status = get_user_status($connect, $_GET["user"]);

	if($total_book_request < $book_request_limit && $user_status == 'Enable')
    {
		$data = array(
			':book_id'                          =>  $_GET["book"],
			':user_id'                          =>  $_GET["user"],
			':admin_id'                         =>  1,
			':issue_book_issue_date'            =>  $today_date,
			':issue_book_fines'                 =>  0,
			':issue_book_status'                =>  'Pending'
		);

		$query = "
		INSERT INTO issue_book 
		(book_id, user_id, admin_id, issue_book_issue_date, issue_book_fines, issue_book_status) 
		VALUES (:book_id, :user_id, :admin_id, :issue_book_issue_date, :issue_book_fines, :issue_book_status)
		";

		$statement = $connect->prepare($query);
        $statement->execute($data);

		header('location:search_book.php?msg=add');
	}
	else
	{
		if($user_status == 'Disable') 
		{
			$error .= '<li>User is Disabled</li>';
		}
		else 
		{
			$error .= '<li>User has reached Book Request Limit</li>';
		}
	}

}

$query = "
	CALL DISPLAY_BOOKS()
";

$statement = $connect->prepare($query);
$statement->execute();
include 'header.php';
?>

<div class="container-fluid px-4 bg-dark pt-2 pb-2 rounded-3">
	<div class="row mt-3 mb-3">
		<div class="col-md-2 text-white">
			<h1>Book</h1>
		</div>

		<div class="col-md-10">
		<?php 
		if($error != '')
        {
            echo '<div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                    <ul class="mb-0 list-unstyled">'.$error.'</ul> 
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
        }

        if(isset($_GET['msg']))
        {
            if($_GET['msg'] == 'add')
            {
                echo '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">Book Added To Pending List<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
        }
		?>
		</div>
	</div>

	<div class="card mb-3">
		<div class="card-header">
			<div class="col col-md-6 mt-1">
				<i class="fa-solid fa-book-open me-1"></i> Book List
			</div>
		</div>
		
		<div class="card-body">
			<table id=datatablesSimple>
				<thead>
					<tr>
						<th>Name</th>
						<th>Author</th>
						<th>Category</th>
						<th>Publisher</th>
						<th>Location</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>Name</th>
						<th>Author</th>
						<th>Category</th>
						<th>Publisher</th>
						<th>Location</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</tfoot>
				<tbody>
					<?php
					if($statement->rowCount() > 0)
					{
						foreach($statement->fetchAll() as $row)
						{
							$book_status = '';
							if($row['book_copies'] > 0 && $row['book_status'] == 'Enable')
							{
								$book_status = '<div class="badge bg-success">Available</div>';
							}
							else
							{
								$book_status = '<div class="badge bg-danger">Unavailable</div>';
							}

							$query = "
							SELECT * FROM publisher 
							WHERE publisher_id = '".$row["book_publisher"]."'
							";
							$publisher_data = $connect->prepare($query);
							$publisher_data->execute();

							$query = "
							SELECT * FROM book_author 
							WHERE book_id = '".$row["book_id"]."'
							";
							$book_author_data = $connect->prepare($query);
							$book_author_data->execute();

							$query = "
							SELECT * FROM book_category 
							WHERE book_id = '".$row["book_id"]."'
							";
							$book_category_data = $connect->prepare($query);
							$book_category_data->execute();

							echo '
							<tr>
								<td>'.$row["book_name"].'</td>
							';

							echo '<td>';
								foreach($book_author_data->fetchAll() as $book_author_data)
								{
									$query = "
									SELECT * FROM author 
									WHERE author_id = '".$book_author_data["author_id"]."'
									";
									$author_data = $connect->prepare($query);
									$author_data->execute();

									foreach($author_data->fetchAll() as $author_data)
									{
									echo '
										<div>'.$author_data ["author_name"].'</div>
									';
									}
								}
							echo '</td>';

							echo '<td>';
								foreach($book_category_data->fetchAll() as $book_category_data)
								{
									$query = "
									SELECT * FROM category 
									WHERE category_id = '".$book_category_data["category_id"]."'
									";
									$category_data = $connect->prepare($query);
									$category_data->execute();

									foreach($category_data->fetchAll() as $category_data)
									{
									echo '
										<div>'.$category_data ["category_name"].'</div>
									';
									}
								}
							echo '</td>';

							foreach($publisher_data as $publisher_data)
							{
							echo '
								<td>'.$publisher_data ["publisher_name"].'</td>
							';
							}

							echo '
								<td>'.$row["book_location"].'</td>
								<td>'.$book_status.'</td>
							';
							
							if($row['book_copies'] > 0 && $row['book_status'] == 'Enable')
							{
							echo '
								<td>
									<div class="row">
										<button
											class="btn btn-primary btn-sm" 
											onclick="borrow_book(`'.$row["book_id"].'`, `'.$_SESSION['user_id'].'`)">
											Borrow
										</button>
									</div>
								</td>
							</tr>
							';
							}	
							else
							{
							echo '
								<td></td>
							</tr>
							';
							}
						}
					}
					?>
				</tbody>
			</table>
			<script>
				function borrow_book(book_id, user_id) {
					if(confirm("Are you sure you want to Borrow this Book?"))
						{
							window.location.href="search_book.php?action=borrow&book="+book_id+"&user="+user_id+"";
						}
				}
			</script>
		</div>
	</div>
</div>

<?php 
include 'footer.php';
?>