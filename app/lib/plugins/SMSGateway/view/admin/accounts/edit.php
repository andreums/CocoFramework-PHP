<div class="span-16 last">
	<h2><?php print _("Editar cuenta {$account->getName()}");?></h2>
	<hr/>
</div>

<div class="span-16 last container">

    <?php
        print form::formTag("frmEditAccount","#","post");
    ?>
    <?php
        print form::openFieldsetTag();
        print form::legendTag(_("Datos generales"));
    ?>

    <div class="span-4 column">
		<?php print form::labelTag("name",_("Nombre")); ?>
    </div>

    <div class="span-10 column las">
    	<?php print form::textInput("name",$account->getName(),array("disabled"=>true),"span-8"); ?>
    </div>

    <div class="span-4 column">
		<?php print form::labelTag("description",_("Descripción")); ?>
    </div>

    <div class="span-10 column las">
    	<?php print form::textArea("description",$account->getDescription(),0,0,"span-8","height: 115px;"); ?>
    </div>

    <?php print form::closeFieldsetTag(); ?>

    <?php
        print form::openFieldsetTag();
        print form::legendTag(_("Proveedor"));
    ?>

    <div class="span-4 column">
		<?php print form::labelTag("provider",_("Proveedor")); ?>
    </div>

    <div class="span-10 column last">
    	<?php print form::selectTag("provider",array("BulkSMS"=>"BulkSMS"),false,false,"span-8"); ?>
    </div>


    <div class="span-4 column">
		<?php print form::labelTag("username",_("Usuario")); ?>
    </div>

    <div class="span-10 column last">
    	<?php print form::textInput("username",$account->getUsername(),array(),"span-8"); ?>
    </div>

    <div class="span-4 column">
		<?php print form::labelTag("password",_("Contraseña")); ?>
    </div>

    <div class="span-10 column last">
    	<?php print form::passwordInput("password",$account->getPassword(),array(),"span-8"); ?>
    </div>

    <div class="span-4 column">
		<?php print form::labelTag("sender",_("Remitente")); ?>
    </div>

    <div class="span-10 column last">
    	<?php print form::textInput("sender",$account->getSender(),array(),"span-8"); ?>
    </div>


    <div class="span-4 column">
		<?php print form::labelTag("status",_("Estado")); ?>
    </div>

    <div class="span-10 column last">
    	<?php
    	    if ($account->getActive()) {
    	         print form::checkboxInput("status","true",_("activa"),true,"span-8");
    	    }
    	    else {
    	        print form::checkboxInput("status","false",_("activa"),false,"span-8");
    	    }

        ?>
    </div>


    <?php print form::closeFieldsetTag(); ?>

    <?php print form::buttonInput("bSubmit","submit",_("Enviar"),"submit");  ?>
    <?php print form::buttonInput("bReset","reset",_("Reiniciar"),"reset");  ?>

    <?php
        print form::closeFormTag();
	?>
</div>
