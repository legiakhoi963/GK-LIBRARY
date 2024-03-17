<?php
include '../database_connection.php';
include '../function.php';

if(!is_admin_login())
{
	header('location:../admin_login.php');
}

include '../header.php';
?>

<div class="container-fluid px-4">
	<div class="row mt-3 mb-3">
		<div class="col-md-12">
			<h1>Dashboard</h1>
		</div>
	</div>
	<div class="row justify-content-center">
		<div class="col-xl-3 col-md-6">
			<div class="card bg-dark p-1 text-light mb-4 rounded-3">
				<div class="card-body bg-primary">
					<h1 class="text-center"><?php echo Count_total_issue_book_number($connect); ?></h1>
					<h5 class="text-center">Books Issued</h5>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6">
			<div class="card bg-dark p-1 text-light mb-4 rounded-3">
				<div class="card-body bg-success">
					<h1 class="text-center"><?php echo Count_total_returned_book_number($connect); ?></h1>
					<h5 class="text-center">Books Returned</h5>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6">
			<div class="card bg-dark p-1 text-light mb-4 rounded-3">
				<div class="card-body bg-danger">
					<h1 class="text-center"><?php echo Count_total_not_returned_book_number($connect); ?></h1>
					<h5 class="text-center">Books Not Returned</h5>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6">
			<div class="card bg-dark p-1 text-dark mb-4 rounded-3">
				<div class="card-body bg-warning">
					<h1 class="text-center"><?php echo Count_total_fines_received($connect); ?></h1>
					<h5 class="text-center">Fines Received</h5>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6">
			<div class="card bg-dark p-1 text-dark mb-4 rounded-3">
				<div class="card-body bg-light">
					<h1 class="text-center"><?php echo Count_total_book_number($connect); ?></h1>
					<h5 class="text-center">Books</h5>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6">
			<div class="card bg-dark p-1 text-dark mb-4 rounded-3">
				<div class="card-body bg-light">
					<h1 class="text-center"><?php echo Count_total_author_number($connect); ?></h1>
					<h5 class="text-center">Authors</h5>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6">
			<div class="card bg-dark p-1 text-dark mb-4 rounded-3">
				<div class="card-body bg-light">
					<h1 class="text-center"><?php echo Count_total_category_number($connect); ?></h1>
					<h5 class="text-center">Categories</h5>
				</div>
			</div>
		</div>
		<div class="col-xl-3 col-md-6">
			<div class="card bg-dark p-1 text-dark mb-4 rounded-3">
				<div class="card-body bg-light">
					<h1 class="text-center"><?php echo Count_total_publisher_number($connect); ?></h1>
					<h5 class="text-center">Publishers</h5>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
include '../footer.php';
?>