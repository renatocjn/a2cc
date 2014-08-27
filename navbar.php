<div id="navbar">
	<a class="whitelink" href="../en/login.php">English</a>&nbsp|
	<a class="whitelink" href="../pt/login.php">Português</a>
	<?php	
		if (isset($ssh_con) && $ssh_con->logged()) {
			print '|&nbsp<a class="whitelink" href="../logoff.php">Logoff</a>';
		}
	?>
</div> 
