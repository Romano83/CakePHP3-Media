<!DOCTYPE html>
<html>
<head>
<title>Uploader</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <?= $this->Html->css('/Media/css/style.css'); ?>
        <?= $this->fetch('css'); ?>
    </head>
<body>

	   <?= $this->Flash->render('Auth')?>
	   <?= $this->Flash->render()?>

       <?= $this->fetch('content'); ?>

        <!-- jQuery AND jQueryUI -->
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <?= $this->Html->script('/media/js/main.js') ?>
    <?= $this->fetch('script'); ?>
    <?= $this->fetch('mediaScriptBottom'); ?>
			
    </body>
</html>