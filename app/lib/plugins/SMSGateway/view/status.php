<?php
$inbox  = $this->getVariable("inbox");
$outbox = $this->getVariable("outbox");
?>

<table border="1px" width="100%">
	<caption>Mensajes Recibidos</caption>
	<thead>
		<tr>
			<th>id</th>
			<th>fecha</th>
			<th>teléfono</th>
			<th>texto</th>
		</tr>
	</thead>
	<tbody>
	<?php
	if (count($inbox)>0) {
	    foreach ($inbox as $msg) { ?>
		<tr>
			<td><?php print $msg->id; ?></td>
			<td><?php print date("r",strtotime($msg->date)); ?></td>
			<td><?php print $msg->telephone; ?></td>
			<td><?php print utf8_encode($msg->text); ?></td>
		</tr>
		<?php
	    }
	}
	?>
	</tbody>
</table>

<table border="1px" width="100%">
	<caption>Mensajes Enviados</caption>
	<thead>
		<tr>
			<th>id</th>
			<th>fecha</th>
			<th>teléfono</th>
			<th>texto</th>
		</tr>
	</thead>
	<tbody>
	<?php
	if (count($outbox)>0) {
	    foreach ($outbox as $msg) { ?>
		<tr>
			<td><?php print $msg->id; ?></td>
			<td><?php print date("r",strtotime($msg->date)); ?></td>
			<td><?php print $msg->telephone; ?></td>
			<td><?php print $msg->text; ?></td>
		</tr>
		<?php
	    }
	}
	?>
	</tbody>

</table>
