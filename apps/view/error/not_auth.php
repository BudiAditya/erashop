<!--
	You can change this template to fullfill your requirement.
	DON'T DELETE THIS TEMPLATE BECAUSE REQUIRED AS CORE PART OF THE FRAMEWORK
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Error : Not Authenticated to Access</title>
</head>

<body>
	<h2>Sorry you have not been Authenticated by System !</h2>
	<h3>
		Please make sure that you have been Authenticated to access this resource(s).<br />
		Please perform authentication (login) berfore accessing this resource(s).<br /><br />
		Trying to access : '<?php printf("%s/%s", $fqn, $method); ?>'
	</h3>
	<?php print($helper->a("Go to home", $helper->url("home"))); ?>
</body>
</html>
