<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <link rel="shortcut icon" href="" />
        <meta http-equiv="Content-Script-Type" content="text/javascript" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="robots" content="index, follow" />
        <meta name="keywords" content="" />
        <meta name="title" content="" />
        <meta name="description" content="" />
        
        <title><?php print _("VendoYo");?><?php
            if ( $this->hasSlot("title") ) { print $this->getSlot("title");
            }
            ?></title>
        <!-- ////////////////////////////////// -->
        <!-- //      Start Stylesheets       // -->
        <!-- ////////////////////////////////// -->
        <?php
            print html::style_tag("js/jquery/plugins/gritter/css/jquery.gritter.css", "screen");
            print html::style_tag("js/jquery/plugins/breadcrumb/Base.css", "screen");
            print html::style_tag("js/jquery/plugins/breadcrumb/BreadCrumb.css", "screen");
            print html::style_tag("style/new/blueprint/screen.css", "screen");
            print html::style_tag("style/new/style.css", "screen");
            print html::style_tag("style/new/inner.css", "screen");
            print html::style_tag("style/new/jQueryUI/jquery-ui-1.8.16.custom.css", "screen");
        ?>
        <!--<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>-->

        <?php
            // mostrar els estils per aquesta accio
            $styles = FW_Context::getInstance()->router->styles;
            if ( count($styles) > 0 ) {
                foreach ( $styles as $style ) {
                    print html::style_tag($style ["file"], $style ["media"]);
                }
            }
        ?>

        <?php
            print html::script_tag("js/jquery/jquery.min.js");
        ?>
        
        <!--[if IE 6]>
        <script src="./js/DD_belatedPNG.js"></script>
        <script>
        DD_belatedPNG.fix('img');
        </script>

        <![endif]-->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <div id="top-container">
            <div class="centercolumn">
                <?php $this->renderGlobalView("headers/header",array());?>
            </div><!-- end #centercolumn -->
        </div><!-- end #top-container -->
        <div class="centercolumn">
            <div id="maincontent" class="container span-24">                
                <div id="content"  class="span-24 column">
                    <div id="breadcrumbHolder"  class="span-24">
                    <?php                        
                        if ($this->hasSlot("breadcrumb")) {                            
                            print $this->getSlot("breadcrumb");
                        }                        
                    ?>                    
                    </div>
                    <hr class="space" />
                    <div id="flash"  class="span-24">
                        <?php                        
                            if ( FW_Flash::getInstance()->hasMessages() ) {
                                print FW_Flash::getInstance()->displayMessages();
                                print "<hr class=\"space\">";
                            }                        
                        ?>
                    </div>
                                
                    <?php
                        // si existeix el slot a mostrar ...
                        if ( $this->hasSlot("content") ) {
                            // imprimir els seus contingut
                            print $this->getSlot("content");
                        }
                    ?>
                </div><!-- end #content -->

                </div><!-- end #sidebar_right -->
                <div class="clear"></div>
            </div><!-- end #maincontent -->
        </div><!-- end #centercolumn -->
        <div id="bottom-container">
            <div class="centercolumn">
                <div id="footer"  class="container span-24">
                    <div id="footer-left" class="span-16 column">
                        <div class="span-4 column">
                            <ul>
                                <li class="widget-container">
                                    <h2 class="widget-title">Company</h2>
                                    <ul>
                                        <li>
                                            <a href="#">About Us</a>
                                        </li>
                                        <li>
                                            <a href="#">Services</a>
                                        </li>
                                        <li>
                                            <a href="#">Clients</a>
                                        </li>
                                        <li>
                                            <a href="#">Presentation</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div><!-- end #one_fourth -->
                        <div class="span-4 column">
                            <ul>
                                <li class="widget-container">
                                    <h2 class="widget-title">For Clients</h2>
                                    <ul>
                                        <li>
                                            <a href="#">Sign Up </a>
                                        </li>
                                        <li>
                                            <a href="#">Forum</a>
                                        </li>
                                        <li>
                                            <a href="#">Promitions</a>
                                        </li>
                                        <li>
                                            <?php
                                                $link = html::link_for_internal("guestbook","guestbook","index");
                                            ?>
                                            <a href="<?php print $link ?>"><?php print _("Libro de visitas"); ?></a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div><!-- end #one_fourth -->
                        <div class="span-4 column">
                            <ul>
                                <li class="widget-container">
                                    <h2 class="widget-title">FAQs</h2>
                                    <ul>
                                        <li>
                                            <a href="#">Support </a>
                                        </li>
                                        <li>
                                            <?php
                                                $link = html::link_for_internal("faq","faq","index");
                                            ?>
                                            <a href="<?php print $link ?>"><?php print _("FAQs"); ?></a>
                                        </li>
                                        <li>
                                            <a href="#">Website</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div><!-- end #one_fourth -->
                        <div class="span-4 column last">
                            <ul>
                                <li class="widget-container">
                                    <h2 class="widget-title">Properties</h2>
                                    <ul>
                                        <li>
                                            <a href="#">Luxury</a>
                                        </li>
                                        <li>
                                            <a href="#">Residental</a>
                                        </li>
                                        <li>
                                            <a href="#">Commercial</a>
                                        </li>
                                        <li>
                                            <a href="#">Hometown</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div><!-- end #one_fourth -->
                    </div><!-- end #footer-left -->
                    <div id="footer-right" class="span-6 column last">
                        <h2>NewsLetter Sign Up:</h2>
                        <form method="get" action="./blog.html" id="newsLetter" />
                        <div>
                            <input type="text" class="inputbox" value="Enter your email here..." onblur="if (this.value == ''){this.value = 'Enter your email here...'; }" onfocus="if (this.value == 'Enter your email here...') {this.value = ''; }" />
                            <br />
                            <input type="submit" name="submit" class="button" value="subscribe" />
                        </div>
                        </form>
                    </div><!-- end #footer-right -->
                </div>
                <div class="clear"></div>
                <div id="copyright">
                    Copyright © 2011 <a href="#">Light House</a>. All Rights Reserved
                </div>
                <!-- end #foot -->
            </div><!-- end #centercolumn -->
            <div class="clear"></div>
        </div><!-- end #bottom-container -->
        <!-- to fix cufon problems in IE browser -->
        <?php
        print html::script_tag("js/jquery/jquery-1.6.2.min.js");
        print html::script_tag("js/jquery/jquery-ui-1.8.16.custom.min.js");
        print html::script_tag("js/jquery/plugins/gritter/jquery.gritter.js");
        print html::script_tag("js/jquery/plugins/dotimeout/jquery.ba-dotimeout.js");
        print html::script_tag("js/jquery/plugins/breadcrumb/jquery.easing.1.3.js");
        print html::script_tag("js/jquery/plugins/breadcrumb/jquery.jBreadCrumb.1.1.js");
        print html::script_tag("js/application/notifications.js");
        print html::script_tag("js/core.js");
        print html::script_tag("js/rails.js");        
        print html::script_tag("js/cufon-yui.js");
        print html::script_tag("js/PT_Sans_400.font.js");
        ?>
        <?php
            // mostrar els scripts per aquesta accio
            $scripts = FW_Context::getInstance()->router->scripts;            
            if ( count($scripts) > 0 ) {
                foreach ( $scripts as $script ) {
                    print html::script_tag($script) . "\r\n";
                }
            }
        ?>        
    </body>
</html>
