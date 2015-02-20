<div id="navbar">
	<a class="whitelink" href="../en/login.php">English</a>&nbsp|
	<a class="whitelink" href="../pt/login.php">Português</a>&nbsp|
	<a class="whitelink" href="../docs/user_manual_pt.html">Manual</a>
	<?php	
		if (isset($ssh_con) && $ssh_con->logged()) {
			print '|&nbsp<a class="whitelink" href="../logoff.php">Logoff</a>';
		}
	?>
</div> 
