<?php
include 'database_connection.php';
include 'function.php';
include 'header.php';
?>

<div class="container">
	<div class="row p-5 bg-dark rounded-3 mb-3">
		<div class="col-md-9 container">
			<h1 class="display-5 fw-bold mb-5 text-white">Library Management System</h1>
			<p class="fs-4 mb-5 text-white">
			Hệ thống quản lý thư viện cho phép người quản lý có thể dễ dàng kiểm soát thông tin sách, thông tin mượn sách và người dùng.
			<br><br>
			Đồng thời Hệ thống cũng cho phép người dùng tìm kiếm sách cũng như theo dõi thông tin mượn sách nhanh chóng và tiện lợi. 
			</p>
		</div>

		<div class="col-md-3 container">
			<div class="mb-4">
				<div class="h-100 p-4 bg-light rounded-3">
					<h2 class="fw-bold mb-4 text-center">Admin</h2>
					<div class="d-grid gap-2">
						<a href="admin_login.php" class="btn btn-success">Login</a>
					</div>
				</div>
			</div>

			<div class="">
				<div class="h-100 p-4 bg-light rounded-3">
					<h2 class="fw-bold mb-4 text-center">User</h2>
					<div class="d-grid gap-2 mb-2">
						<a href="user_login.php" class="btn btn-success">Login</a>
					</div>
					<div class="d-grid gap-2">
						<a href="user_registration.php" class="btn btn-primary">Sign Up</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
include 'footer.php';
?>