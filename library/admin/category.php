<?php
include '../database_connection.php';
include '../function.php';

if(!is_admin_login())
{
	header('location:../admin_login.php');
}

$message = '';
$error = '';

if(isset($_GET["action"], $_GET["id"], $_GET["status"]) && $_GET["action"] == 'change')
{
	$category_id = $_GET["id"];
	$status = $_GET["status"];
	$data = array(
		':category_status'			=>	$status,
		':category_id'				=>	$category_id
	);
	$query = "
	UPDATE category
	SET category_status = :category_status
	WHERE category_id = :category_id 
	";

	$statement = $connect->prepare($query);
	$statement->execute($data);

	header('location:category.php?msg='.strtolower($status).'');
}

if(isset($_GET["action"], $_GET["id"]) && $_GET["action"] == 'delete')
{
	$category_id = $_GET["id"];
	$query = "
	DELETE FROM category
	WHERE category_id = '".$category_id."'
	";

	$statement = $connect->prepare($query);
	$statement->execute($data);

	header('location:category.php?msg=delete');
}

if(isset($_POST['add_category']))
{
	$formdata = array();

	if(empty($_POST['category_name']))
	{
		$error .= '<li>Category Name is required</li>';
	}
	else
	{
		$formdata['category_name'] = trim($_POST['category_name']);
	}

	if($error == '')
	{
		$query = "
		SELECT * FROM category 
        WHERE category_name = '".$formdata['category_name']."'
		";

		$statement = $connect->prepare($query);
		$statement->execute();

		if($statement->rowCount() > 0)
		{
			$error = '<li>Category Name Already Exists</li>';
		}
		else
		{
			$data = array(
				':category_name'			=>	$formdata['category_name'],
				':category_status'			=>	'Enable'
			);

			$query = "
			INSERT INTO category 
            (category_name, category_status) 
            VALUES (:category_name, :category_status)
			";

			$statement = $connect->prepare($query);
			$statement->execute($data);

			header('location:category.php?msg=add');
		}
	}
}

if(isset($_POST["edit_category"]))
{
	$formdata = array();

	if(empty($_POST["category_name"]))
	{
		$error .= '<li>Category Name is required</li>';
	}
	else
	{
		$formdata['category_name'] = $_POST['category_name'];
	}

	if($error == '')
	{
		$category_id = $_POST['category_id'];

		$query = "
		SELECT * FROM category 
        WHERE category_name = '".$formdata['category_name']."' 
        AND category_id != '".$category_id."'
		";

		$statement = $connect->prepare($query);
		$statement->execute();

		if($statement->rowCount() > 0)
		{
			$error = '<li>Category Name Already Exists</li>';
		}
		else
		{
			$data = array(
				':category_name'		=>	$formdata['category_name'],
				':category_id'			=>	$category_id
			);

			$query = "
			UPDATE category 
            SET category_name = :category_name
            WHERE category_id = :category_id
			";

			$statement = $connect->prepare($query);
			$statement->execute($data);

			header('location:category.php?msg=edit');
		}
	}
}


$query = "
	SELECT * FROM category
	ORDER BY category_id ASC
";

$statement = $connect->prepare($query);
$statement->execute();
include '../header.php';
?>

<div class="container-fluid px-4">
	<div class="row mt-3">
		<div class="col-md-12">
			<h1>Category</h1>
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

		if($message != '')
		{
			echo '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
					<ul class="mb-0">'.$message.'</ul> 
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
		}

		if(isset($_GET['msg']))
		{
			if($_GET['msg'] == 'add')
			{
				echo '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">New Category Added<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
	
			if($_GET["msg"] == 'edit')
			{
				echo '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">Category Data Edited <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
			if($_GET["msg"] == 'disable')
			{
				echo '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">Category Status Change to Disable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
	
			if($_GET['msg'] == 'enable')
			{
				echo '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">Category Status Change to Enable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
	
			if($_GET['msg'] == 'delete')
			{
				echo '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">Category Deleted <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
		}
		?>
	</div>


	<!-- Add -->
	<?php
	if(isset($_GET['action']))
	{
		if($_GET['action'] == 'add')
		{
	?>

	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card mb-3">
				<div class="card-header">
					<div class="col col-md-6 mt-1">
						<i class="fa-solid fa-tags me-1"></i> Add New Category
					</div>
                </div>
                <div class="card-body">

                	<form method="POST">

                		<div class="mb-3">
                			<label class="form-label">Category Name</label>
                			<input type="text" name="category_name" id="category_name" class="form-control" />
                		</div>

                		<div class="mt-3 mb-0">
                			<input type="submit" name="add_category" value="Add" class="btn btn-success" />
                		</div>

                	</form>

                </div>
            </div>
		</div>
	</div>
	<!-- Add -->

	<!-- Edit -->
	<?php 
		}
		else if($_GET["action"] == 'edit')
		{
			$category_id = $_GET["id"];

			if($category_id > 0)
			{
				$query = "
				SELECT * FROM category 
                WHERE category_id = '$category_id'
				";

				$category_result = $connect->query($query);

				foreach($category_result as $category_row)
				{
	?>
	
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card mb-3">
				<div class="card-header">
					<div class="col col-md-6 mt-1">
						<i class="fa-solid fa-tags me-1"></i> Edit Category Details
					</div>
				</div>
				<div class="card-body">

					<form method="post">

						<div class="mb-3">
							<label class="form-label">Category Name</label>
							<input type="text" name="category_name" id="category_name" class="form-control" value="<?php echo $category_row['category_name']; ?>" />
						</div>

						<div class="mt-3 mb-0">
							<input type="hidden" name="category_id" value="<?php echo $_GET['id']; ?>" />
							<input type="submit" name="edit_category" class="btn btn-primary" value="Edit" />
						</div>

					</form>

				</div>
			</div>
		</div>
	</div>
	<!-- Edit -->

	<?php 
				}
			}
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
							<i class="fa-solid fa-tags me-1"></i> Category Management
						</div>
						<div class="col col-md-6" align="right">
							<a href="category.php?action=add" class="btn btn-success btn-sm">Add</a>
						</div>
					</div>
				</div>

				<div class="card-body">
					<table id=datatablesSimple>
						<thead>
							<tr>
								<th>Category Name</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Category Name</th>
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
									if($row['category_status'] == 'Enable')
									{
										$category_status = '<div class="badge bg-success">Enable</div>';
									}
									else
									{
										$category_status = '<div class="badge bg-danger">Disable</div>';
									}
									echo '
									<tr>
										<td>'.$row["category_name"].'</td>
										<td>'.$category_status.'</td>
										<td>
											<a href="category.php?action=edit&id='.$row["category_id"].'" 
												class="btn btn-sm btn-primary">
												Edit
											</a>
											<button
												class="btn btn-warning btn-sm" 
												onclick="change_status(`'.$row["category_id"].'`, `'.$row["category_status"].'`)">
												Change
											</button>
											<button
												class="btn btn-danger btn-sm" 
												onclick="delete_record(`'.$row["category_id"].'`)">
												Delete
										</button>
										</td>
									</tr>
									';
								}
							}
							?>
						</tbody>
					</table>
					<script>
						function change_status(id, status)
						{
							var new_status = 'Enable';

							if(status == 'Enable')
							{
								new_status = 'Disable';
							}

							if(confirm("Are you sure you want to "+new_status+" this Category?"))
							{
								window.location.href="category.php?action=change&id="+id+"&status="+new_status+"";
							}
						}
						function delete_record(id)
						{
							if(confirm("Are you sure you want to detele this Category?"))
							{
								window.location.href="category.php?action=delete&id="+id;
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