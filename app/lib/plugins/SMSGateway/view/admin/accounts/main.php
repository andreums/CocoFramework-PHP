<div class="span-16 last">
	<h3> <?php print _("Cuentas de servicio SMS"); ?></h3>
	<hr/>
</div>
<hr class="space" />

<div class="span-16 last">

<?php if (count($accounts)) {?>
    	<table id="tAccounts" border="1">
        	<thead>
        		<tr>
    				<th> <?php print _("Nombre"); ?></th>
    				<th> <?php print _("Proveedor"); ?></th>
    				<th> <?php print _("Descripción"); ?></th>
    				<th> <?php print _("Acciones"); ?></th>
        		</tr>
        	</thead>
        	<tbody>
        		<?php foreach ($accounts as $account) {?>
        		<tr>
        			<td> <?php print $this->display($account->getName()); ?></td>
        			<td> <?php print $this->display($account->getProvider()); ?></td>
        			<td> <?php print $this->display($account->getDescription()); ?></td>
        			<td> <?php print $this->display($account->getName()); ?></td>
        		</tr>
        		<?php }?>
        	</tbody>
    	</table>
<?php
}
else { ?>
	<h6> <?php print _("Error"); ?></h6>
	<p> <?php print _("No hay cuentas de servicio SMS configuradas"); ?></p>
<?php } ?>
</div>