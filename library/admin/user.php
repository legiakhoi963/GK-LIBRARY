<?php
include '../database_connection.php';
include '../function.php';

if(!is_admin_login())
{
	header('location:../admin_login.php');
}

if(isset($_GET["action"], $_GET['status'], $_GET['id']) && $_GET["action"] == 'change')
{
	$user_id = $_GET["id"];
	$status = $_GET["status"];

	$data = array(
		':user_status'		=>	$status,
		':user_date_updated'	=>	get_date_time($connect),
		':user_id'			=>	$user_id
	);

	$query = "
	UPDATE user 
    SET user_status = :user_status, 
    user_date_updated = :user_date_updated
    WHERE user_id = :user_id
	";

	$statement = $connect->prepare($query);
	$statement->execute($data);

	header('location:user.php?msg='.strtolower($status).'');
}

$query = "
	SELECT * FROM user 
    ORDER BY user_id DESC
";

$statement = $connect->prepare($query);
$statement->execute();
include '../header.php';

?>

<div class="container-fluid px-4">
	<div class="row mt-3">
		<div class="col-md-12">
			<h1>Manage User</h1>
		</div>
	</div>

	<div class="col-md-12 mb-3">
	<?php 
		if(isset($_GET["msg"]))
		{
			if($_GET["msg"] == 'disable')
			{
				echo 
				'<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">User Status Change to Disable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
				';
			}

			if($_GET["msg"] == 'enable')
			{
				echo '
				<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">User Status Change to Enable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
				';
			}
		}
	?>
	</div>

	<!-- Table -->
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card mb-3">
				<div class="card-header">
					<div class="row">
						<div class="col col-md-6 mt-1">
							<i class="fa-solid fa-user-group me-1"></i> Manage User
						</div>
					</div>
				</div>
				<div class="card-body">
					<table id="datatablesSimple">
						<thead>
							<tr>
								<th>User Name</th>
								<th>Email Address</th>
								<th>Password</th>
								<th>Contact</th>
								<th>Address</th>
								<th>Created On</th>
								<th>Updated On</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>User Name</th>
								<th>Email Address</th>
								<th>Password</th>
								<th>Contact</th>
								<th>Address</th>
								<th>Created On</th>
								<th>Updated On</th>
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
								$user_status = '';
								if($row['user_status'] == 'Enable')
								{
									$user_status = '<div class="badge bg-success">Enable</div>';
								}
								else
								{
									$user_status = '<div class="badge bg-danger">Disable</div>';
								}
								echo '
								<tr>
									<td>'.$row["user_name"].'</td>
									<td>'.$row["user_email"].'</td>
									<td>'.$row["user_password"].'</td>
									<td>'.$row["user_contact"].'</td>
									<td>'.$row["user_address"].'</td>
									<td>'.$row["user_date_created"].'</td>
									<td>'.$row["user_date_updated"].'</td>
									<td>'.$user_status.'</td>
									<td>
										<div class="col">
											<div class="row mb-1">
												<button class="btn btn-warning btn-sm" 
													onclick="change_data(`'.$row["user_id"].'`, `'.$row["user_status"].'`)">
													Change
												</button>
											</div>
										</div>
									</td>
								</tr>
								';
							}
						}
						?>
						</tbody>
					</table>
					<script>
						function change_data(id, status)
						{
							var new_status = 'Enable';

							if(status == 'Enable')
							{
								new_status = 'Disable';
							}

							if(confirm("Are you sure you want to "+new_status+" this User?"))
							{
								window.location.href = "user.php?action=change&id="+id+"&status="+new_status+"";
							}
						}
					</script>
				</div>
			</div>
		</div>
	</div>
	<!-- Table -->
</div>

<?php 
include '../footer.php';
?>