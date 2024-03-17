<?php
include '../database_connection.php';
include '../function.php';

if(!is_admin_login())
{
	header('location:../admin_login.php');
}

$message = '';
$error = '';

if(isset($_POST['add_book_category']))
{	
	foreach($_POST["category_name"] as $category_name_array)
	{

		$formdata = array();

		if(empty($_POST['book_name']))
		{
			$error .= '<li>Book Name is required</li>';
		}
		else
		{
			$formdata['book_name'] = trim($_POST['book_name']);
		}

		if(empty($category_name_array))
		{
			$error .= '<li>Category Name is required</li>';
		}
		else
		{
			$formdata['category_name'] = trim($category_name_array);
		}

		if($error == '')
		{
			$query = "
			SELECT book_id FROM book
			WHERE book_name = '".$formdata['book_name']."'
			";
			$book_data = $connect->prepare($query);
			$book_data->execute();

			foreach($book_data->fetchAll() as $row) {
				$formdata['book_id'] = $row['book_id'];
			}

			$query = "
			SELECT category_id FROM category
			WHERE category_name = '".$formdata['category_name']."'
			";
			$category_data = $connect->prepare($query);
			$category_data->execute();

			foreach($category_data->fetchAll() as $row) {
				$formdata['category_id'] = $row['category_id'];
			}

			$data = array(
				':book_id'				=>	$formdata['book_id'],
				':category_id'			=>	$formdata['category_id'],
			);

			$query = "
			INSERT INTO book_category
			(book_id, category_id) 
			VALUES (:book_id, :category_id) 
			";

			$statement = $connect->prepare($query);
			$statement->execute($data);

			header('location:book_category.php?msg=add');
		}
	}
}

if(isset($_GET["action"], $_GET["id"]) && $_GET["action"] == 'delete')
{
	$bc_id = $_GET["id"];
	$query = "
	DELETE FROM book_category
	WHERE bc_id = '".$bc_id."'
	";

	$statement = $connect->prepare($query);
	$statement->execute($data);

	header('location:book_category.php?msg=delete');
}

$query = "
	SELECT * FROM book_category
";

$statement = $connect->prepare($query);
$statement->execute();
include '../header.php';
?>

<div class="container-fluid px-4">
	<div class="row mt-3">
		<div class="col-md-12">
			<h1>Book Category</h1>
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
						<i class="fa-solid fa-tags me-1"></i> Add New Book Category
					</div>
                </div>
                <div class="card-body">

                	<form method="POST">

                		<div class="mb-3">
                			<label class="form-label">Book Name</label>
                			<select name="book_name" id="book_name" class="form-control">
        						<?php echo fill_book($connect); ?>
        					</select>
                		</div>

						<div class="mb-3" id="category_name_field">
                			<label class="form-label">Book Category</label>
							<input type="button" value="Add Category" onclick="createNewElement();"/>
							<input type="button" value="Delete Category" onclick="deleteNewElement();"/>
                			<select name="category_name[]" id="category_name" class="form-control">
        						<?php echo fill_category($connect); ?>
        					</select>
                		</div>

                		<div class="mt-3 mb-0">
                			<input type="submit" name="add_book_category" value="Add" class="btn btn-success" />
                		</div>

						<script>
							var i=1;
							function createNewElement() {
								// First create a DIV element.
								var selectCategory = document.createElement('select');
								selectCategory.setAttribute('name', 'category_name[]');
								selectCategory.setAttribute('id', 'category_name_extra['+i+']');
								selectCategory.setAttribute('class', 'mt-2 mb-2 form-control');

								// Then add the content (a new input box) of the element.
								selectCategory.innerHTML = '<?php echo fill_category($connect); ?>';

								// Finally put it where it is supposed to appear.
								document.getElementById("category_name_field").appendChild(selectCategory);
								i++;
							}

							function deleteNewElement() {
								document.getElementById("category_name_extra["+(i-1)+"]").remove();
								i--;
							}
						</script>

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
							<i class="fa-solid fa-tags me-1"></i> Book Category Management
						</div>
						<div class="col col-md-6" align="right">
							<a href="book_category.php?action=add" class="btn btn-success btn-sm">Add</a>
						</div>
					</div>
				</div>

				<div class="card-body">
					<table id=datatablesSimple>
						<thead>
							<tr>
								<th>Book Name</th>
								<th>Category Name</th>
								<th>Action</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Book Name</th>
								<th>Category Name</th>
								<th>Action</th>
							</tr>
						</tfoot>
						<tbody>
							<?php
							if($statement->rowCount() > 0)
							{
								foreach($statement->fetchAll() as $row)
								{
									$query = "
                					SELECT * FROM book 
                					WHERE book_id = '".$row["book_id"]."'
                					";
									$book_data = $connect->prepare($query);
									$book_data->execute();

									$query = "
                					SELECT * FROM category
                					WHERE category_id = '".$row["category_id"]."'
                					";
									$category_data = $connect->prepare($query);
									$category_data->execute();
									
									echo '<tr>';
									foreach($book_data as $book_data)
									{
									echo '
										<td>'.$book_data ["book_name"].'</td>
									';
									}

									foreach($category_data as $category_data)
									{
									echo '
										<td>'.$category_data ["category_name"].'</td>
									';
									}

									echo '
										<td>
											<button
												class="btn btn-danger btn-sm" 
												onclick="delete_record(`'.$row["bc_id"].'`)">
												Delete
											</button>
										</td>
									';
									echo '</tr>';
								}
							}
							?>
						</tbody>
					</table>
					<script>
						function delete_record(id)
						{
							if(confirm("Are you sure you want to detele this Book Category?"))
							{
								window.location.href="book_category.php?action=delete&id="+id;
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