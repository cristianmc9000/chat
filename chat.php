<html>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

<?php

if (!session_id()) {
	session_start();
}
require("config/db.php");
$title = "Chat Room"; /* ---> */
require("layout/head.php"); // $title = "page title"

if (checkUserSession($db) !== TRUE) {
	header("location: $_LOGIN_FILE");
	exit; //$_LOGIN_FILE --> /config/value.php
}

if (!empty($_GET["room_id"])) {
	$room_id = $_GET["room_id"];
	$user = searchUser_bSession($db, $_COOKIE["user_session"]);

	$query = mysqli_query($db, "select * from chat_room where room_id=$room_id") or error("Room id doesn't exist", $_HOME_FILE); //$_HOME_FILE --> /config/value.php
	if (mysqli_num_rows($query) > 0) {
		//$_SESSION["current_room_id"] = $room_id;
		$room_data = mysqli_fetch_array($query) or error("Can't get room data", $_HOME_FILE);


		$isMember = false;
		$isMember = false;
		$mem_query = mysqli_query($db, "select * from room_member where user_id={$user["id"]} and room_id=$room_id");
		if ($user["id"] == $room_data["owner"]) {
			$isOwner = true;
			$isMember = true;
		} elseif (mysqli_num_rows($mem_query) > 0) {
			$isMember = true;
		}

	} else {
		error("Room id doesn't exist", $_HOME_FILE);
	}
}

?>

<body class="pace-done">

	<div id="wrapper">

		<?php
		$userName = $user["firstName"] . " " . $user["lastName"]; /* ---> */
		require("layout/menu.php");
		?>

		<div id="page-wrapper" class="gray-bg" style="min-height: 1935px;">
			<?php
			require("layout/navtop.php");
			?>


			<!-- AQUI COLOCAMOS EL NOMBRE DE LA LINEA BUENO AQUI LLAMAMOS AL NOMBRE DE LA LINEA -->
			<h2 class="room-description">
				<?= $room_data["room_description"] ?>
			</h2>

			<!-- AQUI COLOCAMOS EL NOMBRE DE LA LINEA BUENO AQUI LLAMAMOS AL NOMBRE DE LA LINEA -->

			<div class="col-lg-1">

			</div>




			<!-- <div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content">

					Estas en la <?= $room_data["room_name"] ?> - <strong>ID: </strong> <?= $room_id ?>

				</div>
			</div>
		</div>
	</div> -->

			<?php if ($isMember == true): ?>
				<!-- /chat_area -->
				<div class="row">
					<div class="col-lg-12">
						<div class="ibox chat-view">
							<div class="ibox-title">
								<small class="pull-right text-muted"><!-- /// --></small>
								<?php if ($isOwner == true) { ?> <strong><a
											href="request.php?room_id=<?= $room_id ?>">[Gestión de solicitudes]</a> - <a
											href="member.php?room_id=<?= $room_id ?>">[Gestión de miembros]</a> - <a
											href="room_options.php?room_id=<?= $room_id ?>">[Opciones]</a></strong>
								<?php } ?>
							</div>

							<div class="ibox-content">
								<div class="row">
									<div class="col-md-12">
										<div class="chat-discussion">

											<!-- AREA DE CHAT  -->

										</div>

									</div>


									<form id="send-message">
										<div class="input-group">
											<textarea style="resize: none;" class="form-control message-input"
												id="txt-message" name="txt-message"
												placeholder="Escriba aquí su mensaje"></textarea>
											<button type="submit" class="btn send-btn"><i
													class="fas fa-paper-plane"></i></button>
										</div>
									</form>


								</div>

							</div>
						</div>

					</div>
				<?php else: ?>
					<div class="row">
						<div class="col-lg-12">
							<div class="ibox float-e-margins">
								<div class="ibox-title">
									Solicitud de ingreso
								</div>
								<div class="ibox-content">
									<?php
									$query = mysqli_query($db, "select * from request_join where user_id={$user["id"]} and room_id=$room_id");
									?>
									<center>No eres miembro de esta sala</center>
									<?php if (mysqli_num_rows($query) == 0): ?>
										<center><button id="request_join" class="btn btn-primary">Solicitar unirse</button>
										</center>
									<?php else: ?>
										<center><button id="request_join" class="btn btn-primary" disabled>Pendiente de
												aprobar...</button></center>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>


			</div>

			<!-- footer -->
			<?php require("layout/footer.php") ?>
			<!-- ./fotter -->

		</div>
		<!-- Mainly scripts -->
		<script src="assets/js/jquery-3.1.1.min.js"></script>
		<script src="assets/js/bootstrap.min.js"></script>
		<script src="assets/js/plugins/metisMenu/jquery.metisMenu.js"></script>
		<script src="assets/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

		<!-- Custom and plugin javascript -->
		<script src="assets/js/inspinia.js"></script>
		<script src="assets/js/plugins/pace/pace.min.js"></script>

		<script src="assets/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
		<script>
			var logChat = "";

			Array.prototype.diff = function (a) {
				var me = this;

				for (let i = 0; i < me.length; i++) {
					for (let j = 0; j < a.length; j++) {
						if (a[j].id.indexOf(me[i].id) > -1) {
							a.splice(j, 1)
						}
					}
				}

				return a;
			};

			<?php if ($isMember == true): ?>
				$(document).ready(function () {
					var intercal = setInterval(function () {
						$.ajax({
							url: 'ajax/message/fetch_message.php?room_id=<?= $room_id ?>',
							dataType: 'json',
							contentType: false,
							cache: false,
							processData: false,
							success: function (r) {
								if (JSON.stringify(r) != logChat) {
									if (r.error != null) {
										if (r.error) {
											alert(r.message)
											location.reload();
											clearInterval(intercal);
										}
									}

									var chat_temp = "";
									r.forEach(function (m) {
										if (m.owner) {
											//mensajes correctos
											var temp = '<div class="chat-message right">' +
												'<img class="message-avatar" src="${profilePicture}" alt="">' +
												'<div class="message">' +
												'<a class="message-author" href="#"> ${name} </a>' +
												'<span class="message-date"> ${time} </span>' +
												'<span class="message-content">' +
												'${message}' +
												'</span>' +
												'</div>' +
												'</div>';

											chat_temp += temp.replace("${profilePicture}", m.profilePicture).replace("${name}", m.sender).replace("${message}", m.message).replace("${time}", m.time_ago)
										} else {
											//mensajes dejados
											var temp = '<div class="chat-message left">' +
												'<img class="message-avatar" src="${profilePicture}" alt="">' +
												'<div class="message">' +
												'<a class="message-author" href="#"> ${name} </a>' +
												'<span class="message-date"> ${time} </span>' +
												'<span class="message-content">' +
												'${message}' +
												'</span>' +
												'</div>' +
												'</div>';

											chat_temp += temp.replace("${profilePicture}", m.profilePicture).replace("${name}", m.sender).replace("${message}", m.message).replace("${time}", m.time_ago)
										}
									});

									$('.chat-discussion').html(chat_temp)
									var d = $('.chat-discussion');
									d.scrollTop(d.prop("scrollHeight"));
								}

								logChat = JSON.stringify(r);
							}
						});

					}, 1000);
				});

				$('#txt-message').keypress(function (event) {
					var keycode = (event.keyCode ? event.keyCode : event.which);
					if (keycode == '13') {
						$("#send-message").submit()
					}
				});
<?php endif; ?>

				$("#request_join").on('click', (function (e) {
					$.ajax({
						url: "ajax/request/join_room.php",
						type: "POST",
						data: {
							room_id: "<?= $room_id ?>"
						},
						dataType: 'json',
						beforeSend: function () {
							$('#request_join').text("Solicitando...").prop('disabled', true)
						},
						success: function (r) {
							if (r.success) {
								$('#request_join').text("Pendiente de aprobar...").prop('disabled', true)
							} else {
								$('#request_join').text("Solicitar unirse").prop('disabled', false)
							}
						},
						error: function () {
							$('#request_join').text("Solicitar unirse").prop('disabled', false)
						},
						complete: function () {

						}
					});
				}));

			$("#send-message").on('submit', (function (e) {
				e.preventDefault();
				$.ajax({
					url: "ajax/message/send.php?room_id=<?= $room_id ?>",
					type: "POST",
					data: new FormData(this),
					dataType: 'json',
					contentType: false,
					cache: false,
					processData: false,
					beforeSend: function () {
						$('#txt-message').prop('disabled', true)
					},
					success: function (data) {

					},
					error: function () {
						//... error event
					},
					complete: function () {
						$('#txt-message').prop('disabled', false).focus().val(null)
					}
				});
			}));
		</script>
</body>

</html>