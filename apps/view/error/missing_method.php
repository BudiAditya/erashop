<!--
	You can change this template to fulfill your requirement.
	DON'T DELETE THIS TEMPLATE BECAUSE REQUIRED AS CORE PART OF THE FRAMEWORK
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Error : Missing Controller Method</title>
</head>

<body>
	<h2>Missing required method '<?php printf("%s/%s", $fqn, $method); ?>'</h2>
	<h3>
		Please make sure that you have 'function <?php print($method); ?>()' in your '<?php print($controller); ?>_controller.php'<br />
		Please check your controller in folder 'controller<?php print($folder); ?>'
	</h3>
	<?php print($helper->a("Go to home", $helper->url("home"))); ?>
</body>
</html>
