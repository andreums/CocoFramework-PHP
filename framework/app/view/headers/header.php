<div id="header">
    <div id="logo">
        <a href="<?php print $this->getBaseURL();?>"><img src="/images/logo.png" alt="" /></a>
    </div><!-- end #logo -->

        <?php
        
        if($this->user()){
            $role = array_shift($this->user()->getRole());
            if ($role === "particular"){
                $this->renderGlobalView("menu/menuParticular",array());
            } else if($role === "inmobiliaria"){
                $this->renderGlobalView("menu/menuInmobiliaria",array());
            } else if($role === "banco"){
                $this->renderGlobalView("menu/menuBanco",array());
            } else if($role === "promotor"){
                $this->renderGlobalView("menu/menuProfesional",array());
            } else if($role === "root"){
                $this->renderGlobalView("menu/menuRoot",array());
            } else if($role === "administrador"){
                $this->renderGlobalView("menu/menuAdmin",array());
            } else if($role === "editor"){
                $this->renderGlobalView("menu/menuEditor",array());
            } else if($role === "anunciante"){
                $this->renderGlobalView("menu/menuAnunciante",array());
            }
        } else {
            $this->renderGlobalView("menu/menu",array());
        }
    ?>
    <div class="clr"></div>    
    
</div><!-- end #header -->