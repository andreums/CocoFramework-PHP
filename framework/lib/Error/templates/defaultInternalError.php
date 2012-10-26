<h2><?php print _("Error interno"); ?></h2>
<p><?php print _("Se ha producido un error interno en la aplicacion"); ?></p>
<br />
<h3><?php print _("Acciones"); ?></h3>
<ol>
	<li><?php print _("Vuelva a la"); ?> <a
		href="<?php print FW_Config::getInstance()->get("core.global.baseURL"); ?>index.php" title="Pagina principal"> <?php print _("pagina de inicio"); ?>
	</a></li>
	<li><?php print _("Intente volver a cargar esta pagina"); ?></li>
	<li><?php print _("Notifique un error al <em>webmaster</em>"); ?></li>
</ol>

