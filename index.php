<?php
	require_once 'require.php';
	$handle = new handle\php();
?>
<!doctype html>
<html>
<head>
	<title>handlePHP</title>
</head>
<body>
	<h1><?php print $handle->className; ?> (<?php print $handle->version; ?>)</h1>
</body>
</html>