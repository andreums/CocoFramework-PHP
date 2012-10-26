<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Associació Ferroviària de Godella</title>
<?php
print html::script_tag("js/jquery/jquery.min.js");
print html::script_tag("js/jquery/jquery-ui.min.js");


// mostrar els estils CSS per aquest modul
print FW_Style_Manager::getInstance()->displayStyle(FW_Context::getInstance()->router->route);

// mostrar els estils CSS per aquesta accio
$styles = FW_Context::getInstance()->router->styles;
if (count($styles)>0) {
    foreach ($styles as $style) {
        $file  = $style["file"];
        $media = $style["media"];
        print html::style_tag($file,$media)."\r\n";
    }
}
?>
</head>

<body>
<div id="wrapper">

<div id="container" class="container">

<div id="header" class="container">
<h1>Blog</h1>
</div>

<div id="sidebar" class="container span-5 column">
<?php
if ($this->hasSlot("sidebar")) {
    print $this->getSlot("sidebar");
}
else {
    $this->renderView("containers/sidebar/default");
}
?></div>

<div id="content" class="container span-16 column last">
<?php
if ($this->hasSlot("flash")) {
    print $this->getSlot("flash");
}
?>

<?php
// si existeix el slot a mostrar ...
if ($this->hasSlot("content")) {
    // imprimir els seus contingut
    print $this->getSlot("content");
}
?></div>

<hr class="space" />

</div>
</div>

<div id="footer" class="container">
<p>Blog</p>
</div>
<?php
// mostrar els scripts per aquesta accio
print html::script_tag("js/core.js");
$scripts = FW_Context::getInstance()->router->scripts;
if (count($scripts)>0) {
    foreach ($scripts as $script) {
        print html::script_tag($script)."\r\n";
    }
}
?>
</body>
</html>