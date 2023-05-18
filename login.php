<html>
<?php
if (!session_id()) {
	session_start();
}
require("config/db.php");
$title = "Iniciar sesión";
require("layout/head.php"); // $title = "page title"

if (checkUserSession($db) !== False) {
	header("location: $_HOME_FILE");
	exit;
}
?>

<body class=" pace-done">
	<div class="pace  pace-inactive">
		<div class="pace-progress" data-progress-text="100%" data-progress="99"
			style="transform: translate3d(100%, 0px, 0px);">
			<div class="pace-progress-inner"></div>
		</div>
		<div class="pace-activity"></div>
	</div>

	<div id="wrapper">

		<?php
		require("layout/menu.php");
		?>
		<div id="page-wrapper" class="gray-bg" style="min-height: 1263px;">
			
			<div class="row wrapper border-bottom white-bg page-heading">
				<div class="col-lg-10">
					<h2>INICIAR SESION</h2>
					<ol class="breadcrumb">
						<li>
							<a href="<?= $_HOME_FILE ?>">Inicio</a>
						</li>
						<li>
							<a>Forms</a>
						</li>
						<li class="active">
							<strong>Login</strong>
						</li>
					</ol>
				</div>
				<div class="col-lg-2">

				</div>
			</div>


			<!-- footer AQUI TERMINA EL CODIGO HTML PARA EL LOGIN -->

			<div class="form">
				<div class="form__box">
					<div class="form__left">
						<div class="form__padding">
							<img class="form__image" src="logo.png" alt="Logo">
						</div>
					</div>
					<div class="form__right">
						<div class="form__padding-right">
						<h2 class="form__title">Comunicación <span class="typing-animation">Frio Caliente</span></h2>

							<form id="Login" method="POST" action="" class="form-horizontal">
								<div class="form__email">
									<input type="text" name="username" placeholder="Usuario" class="form-control">
								</div>
								<div class="form__email">
    <input type="password" name="password" placeholder="Contraseña" class="form-control">
</div>
								<button class="form__submit-btn" type="submit">Iniciar sesión</button>
							</form>
						</div>
					</div>
				</div>
			</div>



			<!-- footer AQUI TERMINA EL CODIGO HTML PARA EL LOGIN -->
			<!-- footer -->

			<?php require("layout/footer.php") ?>
			<!-- ./fotter -->
		</div>
	</div>

	<!-- Mainly scripts -->
	<script src="assets/js/jquery-3.1.1.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>
	<script src="assets/js/plugins/toastr/toastr.min.js"></script>
	<script src="assets/js/plugins/metisMenu/jquery.metisMenu.js"></script>
	<script src="assets/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

	<!-- Custom and plugin javascript -->
	<script src="assets/js/inspinia.js"></script>
	<script src="assets/js/plugins/pace/pace.min.js"></script>

	<script src="assets/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
	<script>
		$("#Login").on('submit', (function (e) {
			e.preventDefault();
			$.ajax({
				url: "ajax/auth/login.php",
				type: "POST",
				data: new FormData(this),
				dataType: 'json',
				contentType: false,
				cache: false,
				processData: false,
				beforeSend: function () {
					$('#lgbtn').text('Processing...').prop('disabled', true)
				},
				success: function (r) {
					if (r.success) {
						location.reload()
					} else {
						toastr.error(r.message)
					}
				},
				error: function () {


				},
				complete: function () {
					$('#lgbtn').text('Login').prop('disabled', false)
				}
			});
		}));
	</script>
</body>

</html>