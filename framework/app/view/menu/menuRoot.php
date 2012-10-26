<div id="navigation">
    <ul id="nav" class="sf-menu">
        <li>
            <a href="<?php print $this->getBaseURL();?>"><?php print _("Inicio"); ?></a>
        </li>
        <li>
            <a href="./noticias"><?php print _("Noticias");?></a>
            <ul>
                <li>
                    <a href="./noticias/archivos"><?php print _("Archivo de noticias"); ?></a>
                </li>
                <li>
                    <a href="./noticias/categorias"><?php print _("Categorías"); ?></a>
                </li>
                <li>
                    <a href="/admin/contenidos"><?php print _("CMS"); ?></a>
                </li>
            </ul>
        </li>
        <li>
            <a href="#"><?php print _("Buscador"); ?></a>
            <ul>
                <li>
                    <a href=".#"><?php print _("Viviendas"); ?></a>
                </li>
                <li>
                    <a href=".#"><?php print _("Chalets y adosados"); ?></a>
                </li>
                <li>
                    <a href=".#"><?php print _("Trasteros"); ?></a>
                </li>
                <li>
                    <a href=".#"><?php print _("Garajes"); ?></a>
                </li>
                <li>
                    <a href=".#"><?php print _("Terrenos"); ?></a>
                </li>
            </ul>
        </li>
        <li>
            <a href=""><?php print _("Zonas");?></a>
            <ul>
                <li>
                    <a href="/admin/zonas"><?php print _("Administración"); ?></a>
                </li>
            </ul>
        </li>
        <li>
            <a href=""><?php print _("Caché");?></a>
            <ul>
                <li>
                    <a href="/admin/system/cache"><?php print _("Administración"); ?></a>
                </li>
                <li>
                    <a href="/admin/system/cache/clean/all"><?php print _("Limpiar toda"); ?></a>
                </li>
                <li>
                    <a href="/admin/system/cache/clean/system"><?php print _("Limpiar sistema"); ?></a>
                </li>
                <li>
                    <a href="/admin/system/cache/clean/data"><?php print _("Limpiar datos"); ?></a>
                </li>
            </ul>
        </li>
        <li>
            <a href="" class="current"><?php print _("Área de usuario");?></a>
            <ul>
                <li>
                    <a href="/usuario/mi_cuenta"><?php print _("Mi cuenta"); ?></a>
                </li>
                <li>
                    <a href="/usuario/perfil/editar"><?php print _("Mi perfil"); ?></a>                                                                                
                </li>
                <li>
                    <a href="/usuario/notificaciones"><?php print _("Notificaciones"); ?></a>                                                                                
                </li>
                <li>
                    <a href="/usuario/contrasena/cambiar"><?php print _("Cambiar password"); ?></a>                                                                                
                </li>
                <li>
                    <a href="/logout"><?php print _("Cerrar sesión"); ?></a>
                </li>                                    
            </ul>
        </li>
        <li>
            <a href="#"><?php print _("Contacto"); ?></a>
        </li>
    </ul>
</div><!-- end #navigation-->