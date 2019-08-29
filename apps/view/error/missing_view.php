<!--
	You can change this template to fulfill your requirement.
	DON'T DELETE THIS TEMPLATE BECAUSE REQUIRED AS CORE PART OF THE FRAMEWORK
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Error : Missing View</title>
</head>

<body>
	<h2>Missing View component for '<?php printf("%s/%s", $fqn, $view); ?>'</h2>
	<h3>
		Please make sure that you have '<?php printf("%s.%s", $view, $ext); ?>' in your View Folder !<br />
		View seek location : '<?php printf("view%s/%s", $folder, $controller); ?>' folder
	</h3>
	<?php print($helper->a("Go to home", $helper->url("home"))); ?>
</body>
</html>
