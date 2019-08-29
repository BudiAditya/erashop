<!--
	You can change this template to fulfill your requirement.
	DON'T DELETE THIS TEMPLATE BECAUSE REQUIRED AS CORE PART OF THE FRAMEWORK
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Error : Missing Controller</title>
</head>

<body>
	<h2>Missing controller file for '<?php print($controller); ?>'</h2>
	<h3>
		Please make sure that you have '<?php print($controller); ?>_controller.php' in controller folder<br />
		Controller seek location : 'controller<?php print($folder); ?>'<br /><br />
		Make sure you are type correct URL !<br />
		If problem persist please contact Site Administrator
	</h3>
	<?php print($helper->a("Go to home", $helper->url("home"))); ?>
</body>
</html>
