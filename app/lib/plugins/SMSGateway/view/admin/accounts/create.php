<div class="span-16 last">
	<h2><?php print _("Crear cuenta");?></h2>
	<hr/>
</div>

<div class="span-16 last container">

    <?php
        print form::formTag("frmCreateAccount","#","post");
    ?>
    <?php
        print form::openFieldsetTag();
        print form::legendTag(_("Datos generales"));
    ?>

    <div class="span-4 column">
		<?php print form::labelTag("name",_("Nombre")); ?>
    </div>

    <div class="span-10 column las">
    	<?php print form::textInput("name","",array("disabled"=>true),"span-8"); ?>
    </div>

    <div class="span-4 column">
		<?php print form::labelTag("description",_("Descripción")); ?>
    </div>

    <div class="span-10 column las">
    	<?php print form::textArea("description","",0,0,"span-8","height: 115px;"); ?>
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
    	<?php print form::textInput("username","",array(),"span-8"); ?>
    </div>

    <div class="span-4 column">
		<?php print form::labelTag("password",_("Contraseña")); ?>
    </div>

    <div class="span-10 column last">
    	<?php print form::passwordInput("password","",array(),"span-8"); ?>
    </div>

    <div class="span-4 column">
		<?php print form::labelTag("sender",_("Remitente")); ?>
    </div>

    <div class="span-10 column last">
    	<?php print form::textInput("sender","",array(),"span-8"); ?>
    </div>


    <div class="span-4 column">
		<?php print form::labelTag("status",_("Estado")); ?>
    </div>

    <div class="span-10 column last">
    	<?php print form::checkboxInput("status","false",_("activa"),false,"span-8"); ?>
    </div>


    <?php print form::closeFieldsetTag(); ?>

    <?php print form::buttonInput("bSubmit","submit",_("Enviar"),"submit");  ?>
    <?php print form::buttonInput("bReset","reset",_("Reiniciar"),"reset");  ?>

    <?php
        print form::closeFormTag();
	?>
</div>
