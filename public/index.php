<?php
if (!isset($_GET['stylesheet'])) {
	header('Content-Type: text/html');
	header('Link: /index.php?stylesheet; rel=preload; as=stylesheet');
	?>
<!doctype html>
<html>
	<head>
		<link href="/index.php?stylesheet" rel="stylesheet">
	</head>
	<body>
		<h1>Header!</h1>
	</body>
</html>
	<?php
	exit;
}

header('Content-Type: text/css');
?>
h1 {
	color: green;
	font-family: sans-serif;
}
