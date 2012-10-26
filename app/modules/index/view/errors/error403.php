<div class="span-10 column" style="height: 100%;">
    <h2>Error 403:Access denied</h2>
    <p>You haven't got valid credentials to enter this resource or area</p>
    <br/>
    <h4>Common causes of this error:</h4>
    <ol>
        <li> You <em>haven't got permission</em> to enter this area </li><li> You have tried to enter in a restricted area <em>without being authorized</em></li>
    </ol>
    <br />
    <h4>Actions</h4>
    <ol>
        <li> Verify that you have the right credentials to enter this resource or area </li><li> Try to login again </li><li> Notify an error to the <em>webmaster</em></li>
    </ol>
</div>
<div class="span-10 column last">
    <h2><?php print _("Incio de sesión");?></h2>
    <?php print $this -> flash() -> displayErrorMessages();?>
    <?php
    print form::formTag("login", html::link_for_internal("index", "index", "login"), "post");
    print form::openFieldsetTag();
    print form::legendTag(_("Iniciar sesión"));
    ?>
    <p class="span-8">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor</p>
    <div class="span-10 last">
        <div class="span-4 column">
            <?php print form::labelTag("username", _("Usuario"));?>
        </div>
        <div class="span-6 column last">
            <?php print form::textInput("username", "", array(), "span-6");?>
        </div>
    </div>
    <div class="span-10 last">
        <div class="span-4 column">
            <?php print form::labelTag("password", _("Contraseña"));?>
        </div>
        <div class="span-6 column last">
            <?php print form::passwordInput("password", "", array(), "span-6");?>
        </div>
    </div>
    <div class="span-10 last">
        <div class="span-4 column">
            <?php print form::buttonInput("submit","submit", _("Enviar"),"submit","span-4");?>
        </div>
        <div class="span-4 column">
            <?php print form::buttonInput("reset", "reset", _("Borrar"),"reset","span-4");?>
        </div>
    </div>
    <?php    
        print form::closeFieldsetTag();
        print form::closeFormTag();
    ?>
</div>