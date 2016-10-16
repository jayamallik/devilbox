<?php require '../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="icon" href="favicon.ico">
		<link href="/assets/css/custom.css" rel="stylesheet">

		<title>DevilBox</title>
	</head>

	<body>

		<nav class="navbar navbar-inverse navbar-static-top">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<span class="navbar-brand" href="#">DevilBox</span>
				</div>
				<div id="navbar" class="collapse navbar-collapse">
					<ul class="nav navbar-nav">
						<li class="active"><a href="#">Home</a></li>
						<li><a href="/vhosts.php">Virtual Hosts</a></li>
						<li><a href="/databases.php">Databases</a></li>
						<li> | </li>
						<li><a href="/phpinfo.php">PHP info</a></li>
						<li><a href="/opcache.php">PHP opcache</a></li>
						<li><a href="/mysqlinfo.php">MySQL info</a></li>
					</ul>
				</div><!--/.nav-collapse -->
			</div>
		</nav>

		<div class="container">

			<h1>DevilBox Overview</h1>
			<br/>
			<br/>

			<div class="row">
				<div class="col-md-12">

					<table class="table table-striped">
						<tbody>
							<tr></tr>

							<!--
								Docker Images
							-->
							<tr>
								<th colspan="2"><h3>Docker Images</h3></th>
							</tr>
							<tr>
								<th>Webserver</th>
								<td><?php echo getHttpVersion();?></td>
							</tr>
							<tr>
								<th>PHP</th>
								<td><?php echo getPHPVersion(); ?></td>
							</tr>
							<tr>
								<th>MySQL Server</th>
								<td><?php echo getMySQLVersion();?></td>
							</tr>


							<!--
								Docker Host (your computer)
							-->
							<tr>
								<th colspan="2"><h3>Host computer</h3></th>
							</tr>
							<tr>
								<th>MySQL datadir</th>
								<td><?php echo $ENV['HOST_PATH_TO_MYSQL_DATADIR'];?></td>
							</tr>
							<tr>
								<th>MySQL socket</th>
								<td>./run/mysql/mysql.sock</td>
							</tr>
							<tr>
								<th>WWW Document Roots</th>
								<td><?php echo $ENV['HOST_PATH_TO_WWW_DOCROOTS'];?></td>
							</tr>
							<tr>
								<th>Log dir</th>
								<td>./log</td>
							</tr>
							<tr></tr>




							<!-- ############################################################ -->
							<!-- HTTPD Docker -->
							<!-- ############################################################ -->
							<tr>
								<th colspan="2"><h3>[docker] HTTPD</h3></th>
							</tr>
							<tr>
								<th>IP Adress</th>
								<td><?php echo $HTTPD_HOST_ADDR;?></td>
							</tr>


							<!-- ############################################################ -->
							<!-- PHP Docker -->
							<!-- ############################################################ -->
							<?php
							$error_loc;
							$error_127;
							$error_rem;

							my_mysql_connection_test($error_loc, 'localhost');
							my_mysql_connection_test($error_127, '127.0.0.1');
							my_mysql_connection_test($error_rem);
							?>
							<tr>
								<th colspan="2"><h3>[docker] PHP</h3></th>
							</tr>
							<tr>
								<th>IP Adress</th>
								<td><?php echo $PHP_HOST_ADDR;?></td>
							</tr>							<tr>
								<th>MySQL Remote Port forwarded to PHP Docker?</th>
								<td><?php echo ($ENV['FORWARD_MYSQL_PORT_TO_LOCALHOST']) ? 'To: 127.0.0.1:'.$ENV['MYSQL_LOCAL_PORT'] : 'No'; ?></td>
							</tr>
							<tr>
								<th>MySQL Remote Socket mounted on PHP Docker?</th>
								<td class="<?php echo file_exists($ENV['MYSQL_SOCKET_PATH']) && getMySQLConfigByKey('socket') == $ENV['MYSQL_SOCKET_PATH']? 'success' : 'danger'; ?>">
									<?php
										if ($ENV['MOUNT_MYSQL_SOCKET_TO_LOCALDISK']) {
											if (file_exists($ENV['MYSQL_SOCKET_PATH'])) {
												if (getMySQLConfigByKey('socket') == $ENV['MYSQL_SOCKET_PATH']) {
													echo 'OK: '.$ENV['MYSQL_SOCKET_PATH'];
												} else {
													echo 'ERR: Mounted from mysql:'.$ENV['MYSQL_SOCKET_PATH']. ', but socket is in mysql:'.getMySQLConfigByKey('socket');
												}
											} else {
												echo 'ERR: '.$ENV['MYSQL_SOCKET_PATH']. ' does not exist inside docker';
											}
										} else {
											echo 'No';
										}
									?>
								</td>
							</tr>
							<tr>
								<th>PHP-MySQL connection test: localhost</th>
								<td class="<?php echo !$error_loc ? 'success' : 'danger';?>"><?php echo !$error_loc ? 'OK' : $error_loc;?></td>
							</tr>
							<tr>
								<th>PHP-MySQL connection test: 127.0.0.1</th>
								<td class="<?php echo !$error_127 ? 'success' : 'danger';?>"><?php echo !$error_127 ? 'OK' : $error_127;?></td>
							</tr>
							<tr>
								<th>PHP-MySQL connection test: <?php echo $MYSQL_HOST_ADDR;?></th>
								<td class="<?php echo !$error_rem ? 'success' : 'danger';?>"><?php echo !$error_rem ? 'OK' : $error_rem;?></td>
							</tr>
							<tr>
								<th>PHP custom run-time options</th>
								<td>
									<pre>
<?php /* TODO: compare with ini_get() */?>
<?php if ($ENV['PHP_XDEBUG_ENABLE'] == 1): ?>
xdebug.remote_enable  = <?php echo $ENV['PHP_XDEBUG_ENABLE']."\n";?>
xdebug.remote_port    = <?php echo $ENV['PHP_XDEBUG_REMOTE_PORT']."\n";?>
xdebug.remote_host    = <?php echo $ENV['PHP_XDEBUG_REMOTE_HOST']."\n";?>

<?php endif; ?>
max_execution_time    = <?php echo $ENV['PHP_MAX_EXECUTION_TIME']."\n";?>
max_input_time        = <?php echo $ENV['PHP_MAX_INPUT_TIME']."\n";?>
memory_limit          = <?php echo $ENV['PHP_MEMORY_LIMIT']."\n";?>
post_max_size         = <?php echo $ENV['PHP_POST_MAX_SIZE']."\n";?>
upload_max_filesize   = <?php echo $ENV['PHP_UPLOAD_MAX_FILESIZE']."\n";?>
max_input_vars        = <?php echo $ENV['PHP_MAX_INPUT_VARS']."\n";?>

error_reporting       = <?php echo $ENV['PHP_ERROR_REPORTING']."\n";?>
display_errors        = <?php echo $ENV['PHP_DISPLAY_ERRORS']."\n";?>
track_errors          = <?php echo $ENV['PHP_TRACK_ERRORS'];?>
									</pre>
								</td>
							</tr>



							<!-- ############################################################ -->
							<!-- MySQL Docker -->
							<!-- ############################################################ -->
							<tr>
								<th colspan="2"><h3>[docker] MySQL</h3></th>
							</tr>
							<tr>
								<th>IP Adress</th>
								<td><?php echo $MYSQL_HOST_ADDR;?></td>
							</tr>							<tr>
								<th>MySQL root password</th>
								<td><?php echo $MYSQL_ROOT_PASS; ?></td>
							</tr>
							<tr>
								<th>MySQL socket</th>
								<td><?php echo getMySQLConfigByKey('socket'); ?></td>
							</tr>
							<tr>
								<th>MySQL logging</th>
								<td><?php echo getMySQLConfigByKey('general-log');?></td>
							</tr>


						</tbody>
					</table>


				</div><!-- /.col -->
			</div><!-- /.row -->

		</div><!-- /.container -->

		<?php require '../include/footer.php'; ?>
		<script>
		// self executing function here
		(function() {
			// your page initialization code here
			// the DOM will be available here
		})();
		</script>
	</body>
</html>