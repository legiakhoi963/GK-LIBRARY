<?php
include 'database_connection.php';
include 'function.php';

if(!is_user_login())
{
	header('location:user_login.php');
}

$query = "
	SELECT * FROM issue_book 
	INNER JOIN book 
	ON book.book_id = issue_book.book_id 
	WHERE issue_book.user_id = '".$_SESSION['user_id']."' 
	ORDER BY issue_book.issue_book_id DESC
";

$statement = $connect->prepare($query);

$statement->execute();

include 'header.php';

?>
<div class="container-fluid px-4 bg-dark pt-2 pb-2 rounded-3">
	<div class="row mt-3 mb-3">
		<div class="col-md-12 text-white">
			<h1>Issue Book Details</h1>
		</div>
	</div>

	<div class="card mb-3">
		<div class="card-header">
			<div class="col col-md-6 mt-1">
				<i class="fas fa-table me-1"></i> Issue Book Detail
			</div>
		</div>

		<div class="card-body">
			<table id="datatablesSimple">
				<thead>
					<tr>
						<th>Book Name</th>
						<th>Issue Date</th>
						<th>Return Date</th>
						<th>Real Return Date</th>
						<th>Fines</th>
						<th>Status</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>Book Name</th>
						<th>Issue Date</th>
						<th>Return Date</th>
						<th>Real Return Date</th>
						<th>Fines</th>
						<th>Status</th>
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

						$status = $row["issue_book_status"];
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
							<td>'.$row["book_name"].'</td>
							<td>'.$row["issue_book_issue_date"].'</td>
							<td>'.$row["issue_book_return_date"].'</td>
							<td>'.$row["issue_book_real_return_date"].'</td>
							<td>'
								.$row["issue_book_fines"].
								"<br>"
								.'VND'.
							'</td>
							<td>'.$status.'</td>
						</tr>
						';
					}
				}
				?>
				</tbody>
			</table>
		</div>
	</div>

</div>

<?php 

include 'footer.php';

?>