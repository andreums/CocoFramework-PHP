DROP TABLE vdydev_category;

CREATE TABLE `vdydev_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_parent` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `slug` varchar(255) COLLATE utf8_bin NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `image` varchar(255) COLLATE utf8_bin NOT NULL,
  `enabled` int(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `author` varchar(100) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO vdydev_category VALUES("1","0","test","test","Lorem Ipsum dolor sit atmet! atenci&oacute;n alt ren!\\r\\n\\r\\nEsto sigue funcionando!","category1.jpg","1","2011-11-25 00:00:00","andreums");



DROP TABLE vdydev_documento_promocion;

CREATE TABLE `vdydev_documento_promocion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_promocion` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_bin NOT NULL,
  `filename` varchar(255) COLLATE utf8_bin NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `creator` varchar(255) COLLATE utf8_bin NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;




DROP TABLE vdydev_foto_promocion;

CREATE TABLE `vdydev_foto_promocion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_promocion` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_bin NOT NULL,
  `filename` varchar(255) COLLATE utf8_bin NOT NULL,
  `creator` varchar(255) COLLATE utf8_bin NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `type` int(2) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO vdydev_foto_promocion VALUES("1","1","Foto de prueba1","1.jpg","andreums","2011-11-27 00:00:00","1","1");
INSERT INTO vdydev_foto_promocion VALUES("2","1","Foto de prueba2","2.jpg","andreums","2011-11-09 00:00:00","0","1");



DROP TABLE vdydev_news;

CREATE TABLE `vdydev_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET latin1 NOT NULL,
  `author` varchar(255) CHARACTER SET latin1 NOT NULL,
  `slug` varchar(255) CHARACTER SET latin1 NOT NULL,
  `created_at` datetime NOT NULL,
  `intro` text CHARACTER SET latin1 NOT NULL,
  `content` text CHARACTER SET latin1 NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO vdydev_news VALUES("2","Prova de SMS","andreums","prova-de-sms","2011-11-25 00:00:00","Prova de SMS .\nLorem Ipsum Dolor Sit Atmet","<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas sed velit nisl. Cras et diam odio, ut interdum est. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi scelerisque aliquam augue a vulputate. Cras sit amet eros sed diam lobortis facilisis. Ut varius iaculis urna a euismod. Maecenas enim tellus, posuere nec rutrum ut, <img src=\"http://upload.wikimedia.org/wikipedia/commons/thumb/1/17/SMS-mobile.jpg/220px-SMS-mobile.jpg\" class=\"span-6\" /> elementum vitae nunc. Morbi tristique quam et est adipiscing id suscipit odio posuere. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam vitae metus nisl. Quisque porta magna eu elit convallis in varius magna lobortis. Suspendisse consectetur varius cursus. Duis eget ligula sem, ut ultrices lectus. </p>\n<img src=\"http://www.motomessage.com/wp-content/uploads/2010/12/sms-marketing.jpg\" class=\"span-6\" />","1");
INSERT INTO vdydev_news VALUES("3","PROVANTsql","andreums","provant-sql","2011-11-29 16:23:01","INSERT INTO vdydev_news (  id , title , author , slug , created_at , intro , content , status , is_commentable  )  VALUES (  NULL , &#039;Provantsdfasdf&#039; , &#039;andreums&#039; , &#039;provant-provant&#039; , &#039;2011-11-29 16:21:23&#039; , &#039;El&amp;middot;le gemin&amp;agrave; xocolat&amp;aacute; masclet&amp;agrave; col&amp;middot;legi.\nE&amp;ntilde;e  l&amp;#039;hist&amp;ograve;ria&#039; , &#039;&amp;lt;p&amp;gt;\n	El&amp;amp;middot;le gemin&amp;amp;agrave; xocolat&amp;amp;aacute; masclet&amp;amp;agrave; col&amp;amp;middot;legi.&amp;lt;br /&amp;gt;\n	E&amp;amp;ntilde;e&amp;amp;nbsp; l&amp;amp;#39;hist&amp;amp;ograve;ria&amp;lt;/p&amp;gt;\n&#039; , &#039;1&#039; , NULL  )","&lt;p&gt;\n	INSERT INTO vdydev_news (&amp;nbsp; id , title , author , slug , created_at , intro , content , status , is_commentable&amp;nbsp; )&amp;nbsp; VALUES (&amp;nbsp; NULL , &amp;#39;Provantsdfasdf&amp;#39; , &amp;#39;andreums&amp;#39; , &amp;#39;provant-provant&amp;#39; , &amp;#39;2011-11-29 16:21:23&amp;#39; , &amp;#39;El&amp;amp;middot;le gemin&amp;amp;agrave; xocolat&amp;amp;aacute; masclet&amp;amp;agrave; col&amp;amp;middot;legi.&lt;br /&gt;\n	E&amp;amp;ntilde;e&amp;nbsp; l&amp;amp;#039;hist&amp;amp;ograve;ria&amp;#39; , &amp;#39;&amp;amp;lt;p&amp;amp;gt;&lt;br /&gt;\n	&amp;nbsp;&amp;nbsp; &amp;nbsp;El&amp;amp;amp;middot;le gemin&amp;amp;amp;agrave; xocolat&amp;amp;amp;aacute; masclet&amp;amp;amp;agrave; col&amp;amp;amp;middot;legi.&amp;amp;lt;br /&amp;amp;gt;&lt;br /&gt;\n	&amp;nbsp;&amp;nbsp; &amp;nbsp;E&amp;amp;amp;ntilde;e&amp;amp;amp;nbsp; l&amp;amp;amp;#39;hist&amp;amp;amp;ograve;ria&amp;amp;lt;/p&amp;amp;gt;&lt;br /&gt;\n	&amp;#39; , &amp;#39;1&amp;#39; , NULL&amp;nbsp; )&lt;/p&gt;\n","0");



DROP TABLE vdydev_news_has_category;

CREATE TABLE `vdydev_news_has_category` (
  `id_news` int(11) NOT NULL,
  `id_category` int(11) NOT NULL,
  PRIMARY KEY (`id_news`,`id_category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO vdydev_news_has_category VALUES("1","1");
INSERT INTO vdydev_news_has_category VALUES("2","1");



DROP TABLE vdydev_news_has_tag;

CREATE TABLE `vdydev_news_has_tag` (
  `id_news` int(11) NOT NULL,
  `id_tag` int(11) NOT NULL,
  PRIMARY KEY (`id_news`,`id_tag`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO vdydev_news_has_tag VALUES("1","1");



DROP TABLE vdydev_oficina_profesional;

CREATE TABLE `vdydev_oficina_profesional` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_objeto` varchar(255) CHARACTER SET latin1 NOT NULL,
  `nombre` varchar(255) CHARACTER SET latin1 NOT NULL,
  `slug` varchar(255) CHARACTER SET latin1 NOT NULL,
  `via` varchar(255) CHARACTER SET latin1 NOT NULL,
  `numero` varchar(255) CHARACTER SET latin1 NOT NULL,
  `municipio` varchar(255) CHARACTER SET latin1 NOT NULL,
  `codigo_postal` varchar(20) CHARACTER SET latin1 NOT NULL,
  `provincia` varchar(100) CHARACTER SET latin1 NOT NULL,
  `estado` varchar(100) CHARACTER SET latin1 NOT NULL,
  `pais` varchar(100) COLLATE utf8_bin NOT NULL,
  `telefono` int(25) NOT NULL,
  `fax` int(25) NOT NULL,
  `description` text CHARACTER SET latin1 NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `creator` varchar(255) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO vdydev_oficina_profesional VALUES("1","P1","prueba1","prueba-1","Paseo de la Acequia","1","Rocafort","46111","Valencia","Comunitat Valenciana","España","961312426","961312426","<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non dui tortor, eu rhoncus nisl. Cras luctus vulputate lobortis. Fusce sit amet elit nibh, eget cursus massa. Sed eget enim dapibus elit imperdiet sollicitudin eu vitae nunc. Sed gravida metus a est viverra sodales. Fusce nec est risus, sed ornare nunc. Etiam sed mauris tellus, quis tempor sem. Aenean a dolor dui. Pellentesque posuere scelerisque massa vitae auctor. Aenean commodo quam at risus cursus sed rutrum metus hendrerit. Mauris volutpat posuere sem, ut tincidunt odio tincidunt eget. Proin vitae sodales augue. Praesent accumsan nisl non lectus ullamcorper aliquet. Suspendisse pulvinar neque et turpis luctus sed lobortis lectus viverra. Quisque rhoncus imperdiet tincidunt. </p>","1","2011-11-06 00:00:00","andreums");
INSERT INTO vdydev_oficina_profesional VALUES("2","P1","Colón 1","colon-1-valencia","Calle Colón","1","Valencia","46001","Valencia","Comunitat Valenciana","España","961312426","961312426","Blah Blah","1","2011-11-06 00:00:00","andreums");
INSERT INTO vdydev_oficina_profesional VALUES("3","P1","prueba3","vinalesa-30-moncada","Calle de Vinalesa","30","Moncada","46113","Valencia","Comunitat Valenciana","España","961312426","961312426","Blah Blah","1","2011-11-24 00:00:00","andreums");



DROP TABLE vdydev_page;

CREATE TABLE `vdydev_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_parent` int(11) DEFAULT NULL,
  `title` varchar(512) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `slug` varchar(512) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `author` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_bin,
  `type` int(11) NOT NULL DEFAULT '1',
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO vdydev_page VALUES("1","0","Provant","provant-les-pagines","andreums","2011-11-28 22:54:02","<h1>\n	<img alt=\"\" src=\"/uploads/images/Mobiliario_oficina_Ofitec_2011_pecera.jpg\" style=\"width: 400px; height: 281px;\" /></h1>\n<h1 id=\"mainHeader\">\n	jQuery Adapter</h1>\n<p>\n	CKEditor offers native jQuery integration through its jQuery Adapter (a jQuery plugin basically). It provides deep integration of CKEditor into jQuery, using its native features.</p>\n<p>\n	<a id=\"Creating_Editor_Instances\" name=\"Creating_Editor_Instances\"></a></p>\n<h2>\n	<span class=\"mw-headline\">Creating Editor Instances </span></h2>\n<p>\n	In order to create editor instances, load the usual CKEditor core script file as well as the jQuery Adapter file, in the following order:</p>\n<div class=\"mw-geshi\" dir=\"ltr\" style=\"text-align: left;\">\n	<div class=\"javascript source-javascript\">\n		<pre class=\"de1\">\n<span class=\"sy0\">&lt;</span>script type<span class=\"sy0\">=</span><span class=\"st0\">&quot;text/javascript&quot;</span> src<span class=\"sy0\">=</span><span class=\"st0\">&quot;/ckeditor/ckeditor.js&quot;</span><span class=\"sy0\">&gt;<!--</span-->script<span class=\"sy0\">&gt;</span>\n<span class=\"sy0\">&lt;</span>script type<span class=\"sy0\">=</span><span class=\"st0\">&quot;text/javascript&quot;</span> src<span class=\"sy0\">=</span><span class=\"st0\">&quot;/ckeditor/adapters/jquery.js&quot;</span><span class=\"sy0\">&gt;<!--</span-->script<span class=\"sy0\">&gt;</span></span></span></pre>\n	</div>\n</div>\n<p>\n	At this point any <code>textarea</code>, <code>p</code>, or <code>div</code> element can be transformed into a rich text editor by using the <code>ckeditor()</code> method.</p>\n<div class=\"mw-geshi\" dir=\"ltr\" style=\"text-align: left;\">\n	<div class=\"javascript source-javascript\">\n		<pre class=\"de1\">\n$<span class=\"br0\">(</span> <span class=\"st0\">&#39;textarea.editor&#39;</span> <span class=\"br0\">)</span>.<span class=\"me1\">ckeditor</span><span class=\"br0\">(</span><span class=\"br0\">)</span><span class=\"sy0\">;</span></pre>\n	</div>\n</div>\n<p>\n	Note that you can also make use of the jQuery chaining.</p>\n<div class=\"mw-geshi\" dir=\"ltr\" style=\"text-align: left;\">\n	<div class=\"javascript source-javascript\">\n		<pre class=\"de1\">\n$<span class=\"br0\">(</span> <span class=\"st0\">&#39;.section-x&#39;</span> <span class=\"br0\">)</span>\n    .<span class=\"me1\">find</span><span class=\"br0\">(</span> <span class=\"st0\">&#39;textarea.editor&#39;</span> <span class=\"br0\">)</span>\n        .<span class=\"me1\">ckeditor</span><span class=\"br0\">(</span><span class=\"br0\">)</span>\n    .<span class=\"me1\">end</span><span class=\"br0\">(</span><span class=\"br0\">)</span>\n    .<span class=\"me1\">find</span><span class=\"br0\">(</span> <span class=\"st0\">&#39;a&#39;</span> <span class=\"br0\">)</span>\n        .<span class=\"me1\">addClass</span><span class=\"br0\">(</span> <span class=\"st0\">&#39;mylink&#39;</span> <span class=\"br0\">)</span>\n    .<span class=\"me1\">end</span><span class=\"br0\">(</span><span class=\"br0\">)</span><span class=\"sy0\">;</span></pre>\n	</div>\n</div>\n<p>\n	The <code>ckeditor()</code> method is the main method of the jQuery adapter. It accepts two optional parameters:</p>\n<ul>\n	<li>\n		A <b>callback function</b> to be executed when the editor is ready.</li>\n	<li>\n		<b>Configuration options</b> specific to the created editor instance.</li>\n</ul>\n<p>\n	The <a class=\"external text\" href=\"http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html\" rel=\"nofollow\" title=\"http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html\">configuration options</a> are passed through a simple object that contains properties, each one related to a specific editor setting.</p>\n<div class=\"mw-geshi\" dir=\"ltr\" style=\"text-align: left;\">\n	<div class=\"javascript source-javascript\">\n		<pre class=\"de1\">\n$<span class=\"br0\">(</span><span class=\"st0\">&#39;.jquery_ckeditor&#39;</span><span class=\"br0\">)</span>\n    .<span class=\"me1\">ckeditor</span><span class=\"br0\">(</span> <span class=\"kw2\">function</span><span class=\"br0\">(</span><span class=\"br0\">)</span> <span class=\"br0\">{</span> <span class=\"co2\">/* callback code */</span> <span class=\"br0\">}</span><span class=\"sy0\">,</span> <span class=\"br0\">{</span> skin <span class=\"sy0\">:</span> <span class=\"st0\">&#39;office2003&#39;</span> <span class=\"br0\">}</span> <span class=\"br0\">)</span>\n    .<span class=\"me1\">ckeditor</span><span class=\"br0\">(</span> callback2 <span class=\"br0\">)</span><span class=\"sy0\">;</span></pre>\n	</div>\n</div>\n<p>\n	The code presented above will not create two editors. On discovering that one editor is already being created, it will wait with the second callback. Each of the callback functions will be executed in the context of the <a class=\"external text\" href=\"http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.editor.html\" rel=\"nofollow\" title=\"http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.editor.html\">CKEDITOR.editor</a> object (so <code>this</code> will be the editor) and the DOM element object will be passed as parameter.</p>\n<p>\n	<a id=\"Code_Interaction_with_Editor_Instances\" name=\"Code_Interaction_with_Editor_Instances\"></a></p>\n<h2>\n	<span class=\"mw-headline\">Code Interaction with Editor Instances </span></h2>\n<p>\n	When an editor instance is ready (after the callback call demonstrated above), the <code>ckeditorGet()</code> method can be used to retrieve the <code><a class=\"external text\" href=\"http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.editor.html\" rel=\"nofollow\" title=\"http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.editor.html\">CKEDITOR.editor</a></code> object that represents an editor instance. For example:</p>\n<div class=\"mw-geshi\" dir=\"ltr\" style=\"text-align: left;\">\n	<div class=\"javascript source-javascript\">\n		<pre class=\"de1\">\n<span class=\"kw2\">var</span> editor <span class=\"sy0\">=</span> $<span class=\"br0\">(</span><span class=\"st0\">&#39;.jquery_ckeditor&#39;</span><span class=\"br0\">)</span>.<span class=\"me1\">ckeditorGet</span><span class=\"br0\">(</span><span class=\"br0\">)</span><span class=\"sy0\">;</span>\n<span class=\"kw3\">alert</span><span class=\"br0\">(</span> editor.<span class=\"me1\">checkDirty</span><span class=\"br0\">(</span><span class=\"br0\">)</span> <span class=\"br0\">)</span><span class=\"sy0\">;</span></pre>\n	</div>\n</div>\n<p>\n	Because setting and retrieving the editor data is a common operation, the jQuery Adapter also provides a dedicated <code>val()</code> method that is an extension of the original jQuery <code>val()</code> method. This method works exactly the same as the jQuery version, but additionally it allows to get and set the editor contents.</p>\n<div class=\"mw-geshi\" dir=\"ltr\" style=\"text-align: left;\">\n	<div class=\"javascript source-javascript\">\n		<pre class=\"de1\">\n<span class=\"co1\">// Get the editor data.</span>\n<span class=\"kw2\">var</span> data <span class=\"sy0\">=</span> $<span class=\"br0\">(</span> <span class=\"st0\">&#39;textarea.editor&#39;</span> <span class=\"br0\">)</span>.<span class=\"me1\">val</span><span class=\"br0\">(</span><span class=\"br0\">)</span><span class=\"sy0\">;</span>\n<span class=\"co1\">// Set the editor data.</span>\n$<span class=\"br0\">(</span> <span class=\"st0\">&#39;textarea.editor&#39;</span> <span class=\"br0\">)</span>.<span class=\"me1\">val</span><span class=\"br0\">(</span> <span class=\"st0\">&#39;my new content&#39;</span> <span class=\"br0\">)</span><span class=\"sy0\">;</span></pre>\n	</div>\n</div>\n<p>\n	This feature can be disabled by setting <code><a class=\"external text\" href=\"http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html#.jqueryOverrideVal\" rel=\"nofollow\" title=\"http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html#.jqueryOverrideVal\">CKEDITOR.config.jqueryOverrideVal</a></code> to false before loading the adapter code.</p>\n<p>\n	For <code><textarea>&lt;/code&gt; elements the editor will automatically return its content back to the form when it is submitted. CKEditor also automatically works with the official &lt;a class=&quot;external text&quot;  data-cke-saved-href=&quot;http://jquery.malsup.com/form/&quot; href=&quot;http://jquery.malsup.com/form/&quot; rel=&quot;nofollow&quot; title=&quot;http://jquery.malsup.com/form/&quot;&gt;jQuery Form Plugin&lt;/a&gt; for AJAX based forms. It does not require any action from the developer&#39;s side to support it.&lt;/p&gt;\n&lt;p&gt;\n	&lt;a id=&quot;Event_handling&quot;  data-cke-saved-name=&quot;Event_handling&quot; name=&quot;Event_handling&quot;&gt;&lt;/a&gt;&lt;/p&gt;\n&lt;h2&gt;\n	&lt;span class=&quot;mw-headline&quot;&gt;Event handling &lt;/span&gt;&lt;/h2&gt;\n&lt;p&gt;\n	Although CKEditor uses its own event system, there are four main events which are being exposed to the jQuery event system. All events use the event namespace, which is simply named &lt;code&gt;.ckeditor&lt;/code&gt;.&lt;/p&gt;\n&lt;p&gt;\n	The following events are available:&lt;/p&gt;\n&lt;ul&gt;\n	&lt;li&gt;\n		&lt;code&gt;&lt;strong&gt;instanceReady.ckeditor&lt;/strong&gt;&lt;/code&gt; &ndash; fired when the editor is created, but before any callback is being passed to the &lt;code&gt;ckeditor()&lt;/code&gt; method.&lt;/li&gt;\n	&lt;li&gt;\n		&lt;code&gt;&lt;strong&gt;setData.ckeditor&lt;/strong&gt;&lt;/code&gt; &ndash; fired when data is set in the editor.&lt;/li&gt;\n	&lt;li&gt;\n		&lt;code&gt;&lt;strong&gt;getData.ckeditor&lt;/strong&gt;&lt;/code&gt; &ndash; fired when data is fetched from the editor. The current editor data is also passed in the arguments.&lt;/li&gt;\n	&lt;li&gt;\n		&lt;code&gt;&lt;strong&gt;destroy.ckeditor&lt;/strong&gt;&lt;/code&gt; &ndash; fired when the editor gets destroyed. It can be used, for example, to execute some cleanup code on the page.&lt;/li&gt;\n&lt;/ul&gt;\n&lt;p&gt;\n	The editor instance is always passed as the first data argument for the listener. Both &lt;code&gt;getData&lt;/code&gt; and &lt;code&gt;setData&lt;/code&gt; are often used internally, so listening to them should be done with care.&lt;/p&gt;\n&lt;p&gt;\n	jQuery events &lt;i&gt;do&lt;/i&gt; bubble up through the DOM, so they can be listened to selectively in certain parts of the document.&lt;/p&gt;\n</textarea></code></p>\n","0","1");
INSERT INTO vdydev_page VALUES("2","1","proves","fent-mes-proves","andreums","2011-11-17 05:00:00","Altra prova de pàgina","0","1");
INSERT INTO vdydev_page VALUES("3","0","Editando Financiamos tus viviendas","financiamos-tus-viviendas","promotor","2011-11-30 06:57:43","Lorem ipsum dolor sit amet, consectetur adipiscing elit. In nec tellus diam, ac pellentesque massa. Donec justo velit, aliquet sed tempor nec, interdum in nunc. Curabitur aliquet est quis ligula iaculis sed aliquet nisl posuere. Fusce vulputate lacus sed enim pharetra porta. Integer viverra dapibus tortor, sed faucibus diam ultrices at. Aliquam vehicula, mi in euismod sagittis, tortor nunc consectetur quam, dignissim lacinia magna neque congue sapien. Sed sodales, lectus et suscipit semper, orci tortor convallis felis, sit amet viverra ipsum lectus sit amet dui. Morbi adipiscing quam eu augue porta vitae porta odio bibendum. Aliquam blandit fermentum mi, non porttitor eros volutpat at.\n\nDuis non risus a ipsum varius aliquet vel bibendum est. Suspendisse eu metus at nisl auctor porttitor at quis sapien. Etiam mattis placerat massa eget sodales. Nam pellentesque viverra enim, ut euismod dui luctus venenatis. Etiam dui purus, commodo sit amet rhoncus eu, varius ac arcu. Suspendisse eu bibendum ipsum. In hac habitasse platea dictumst. Mauris scelerisque convallis mi sed fermentum. In interdum pulvinar magna nec iaculis. Ut at felis libero, vel elementum arcu.\n\n","0","1");
INSERT INTO vdydev_page VALUES("15","","Esto es una p&aacute;gina de prueba","esto-es-una-pagina-de-prueba","promotor","2011-11-30 07:05:18","Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam enim nulla, semper nec auctor nec, interdum vel tortor. Sed auctor condimentum sem, vel laoreet nibh dapibus non. Proin pharetra cursus tortor, ut vestibulum nibh rhoncus vitae. Sed mauris felis, ornare vitae aliquet quis, consectetur quis sem. Donec nulla purus, placerat vitae placerat quis, vestibulum vel libero. Morbi eget lobortis nisl. Nam hendrerit augue quis tortor tempor tempus. Nulla facilisi. Nulla cursus ante ut ipsum hendrerit sodales in eu odio. Nulla faucibus dui quis risus iaculis eu cursus nulla pellentesque. Fusce eget luctus neque. ","0","1");
INSERT INTO vdydev_page VALUES("16","","Esto es una p&aacute;gina de prueba","esto-es-una-pagina-de-prueba","promotor","2011-11-30 07:10:55","&lt;p&gt;\n	Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam enim nulla, semper nec auctor nec, interdum vel tortor. Sed auctor condimentum sem, vel laoreet nibh dapibus non. Proin pharetra cursus tortor, ut vestibulum nibh rhoncus vitae. Sed mauris felis, ornare vitae aliquet quis, consectetur quis sem. Donec nulla purus, placerat vitae placerat quis, vestibulum vel libero. Morbi eget lobortis nisl. Nam hendrerit augue quis tortor tempor tempus. Nulla facilisi. Nulla cursus ante ut ipsum hendrerit sodales in eu odio. Nulla faucibus dui quis risus iaculis eu cursus nulla pellentesque. Fusce eget luctus neque.&lt;/p&gt;\n","2","1");
INSERT INTO vdydev_page VALUES("17","","Esto es una p&aacute;gina de prueba","esto-es-una-pagina-de-prueba","promotor","2011-11-30 07:12:10","&lt;p&gt;\n	Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam enim nulla, semper nec auctor nec, interdum vel tortor. Sed auctor condimentum sem, vel laoreet nibh dapibus non. Proin pharetra cursus tortor, ut vestibulum nibh rhoncus vitae. Sed mauris felis, ornare vitae aliquet quis, consectetur quis sem. Donec nulla purus, placerat vitae placerat quis, vestibulum vel libero. Morbi eget lobortis nisl. Nam hendrerit augue quis tortor tempor tempus. Nulla facilisi. Nulla cursus ante ut ipsum hendrerit sodales in eu odio. Nulla faucibus dui quis risus iaculis eu cursus nulla pellentesque. Fusce eget luctus neque.&lt;/p&gt;\n","2","1");
INSERT INTO vdydev_page VALUES("4","0","Frequence3","Frequence3","andreums","2011-11-28 23:00:39","","0","1");
INSERT INTO vdydev_page VALUES("5","","Frequence3","Frequence3","andreums","2011-11-28 23:01:27","","0","0");
INSERT INTO vdydev_page VALUES("6","","Frequence3","Frequence3","andreums","2011-11-28 23:01:30","","0","0");
INSERT INTO vdydev_page VALUES("18","","Esto es una p&aacute;gina de prueba","esto-es-una-pagina-de-prueba","promotor","2011-11-30 07:15:36","&lt;p&gt;\n	Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam enim nulla, semper nec auctor nec, interdum vel tortor. Sed auctor condimentum sem, vel laoreet nibh dapibus non. Proin pharetra cursus tortor, ut vestibulum nibh rhoncus vitae. Sed mauris felis, ornare vitae aliquet quis, consectetur quis sem. Donec nulla purus, placerat vitae placerat quis, vestibulum vel libero. Morbi eget lobortis nisl. Nam hendrerit augue quis tortor tempor tempus. Nulla facilisi. Nulla cursus ante ut ipsum hendrerit sodales in eu odio. Nulla faucibus dui quis risus iaculis eu cursus nulla pellentesque. Fusce eget luctus neque.&lt;/p&gt;\n","2","1");
INSERT INTO vdydev_page VALUES("19","","Esto es una p&aacute;gina de prueba","esto-es-una-pagina-de-prueba","promotor","2011-11-30 07:15:51","&lt;p&gt;\n	Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam enim nulla, semper nec auctor nec, interdum vel tortor. Sed auctor condimentum sem, vel laoreet nibh dapibus non. Proin pharetra cursus tortor, ut vestibulum nibh rhoncus vitae. Sed mauris felis, ornare vitae aliquet quis, consectetur quis sem. Donec nulla purus, placerat vitae placerat quis, vestibulum vel libero. Morbi eget lobortis nisl. Nam hendrerit augue quis tortor tempor tempus. Nulla facilisi. Nulla cursus ante ut ipsum hendrerit sodales in eu odio. Nulla faucibus dui quis risus iaculis eu cursus nulla pellentesque. Fusce eget luctus neque.&lt;/p&gt;\n","2","1");
INSERT INTO vdydev_page VALUES("20","","Esto es una p&aacute;gina de prueba","esto-es-una-pagina-de-prueba","promotor","2011-11-30 07:16:40","&lt;p&gt;\n	Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam enim nulla, semper nec auctor nec, interdum vel tortor. Sed auctor condimentum sem, vel laoreet nibh dapibus non. Proin pharetra cursus tortor, ut vestibulum nibh rhoncus vitae. Sed mauris felis, ornare vitae aliquet quis, consectetur quis sem. Donec nulla purus, placerat vitae placerat quis, vestibulum vel libero. Morbi eget lobortis nisl. Nam hendrerit augue quis tortor tempor tempus. Nulla facilisi. Nulla cursus ante ut ipsum hendrerit sodales in eu odio. Nulla faucibus dui quis risus iaculis eu cursus nulla pellentesque. Fusce eget luctus neque.&lt;/p&gt;\n","2","1");
INSERT INTO vdydev_page VALUES("21","","Esto es una p&aacute;gina de prueba","esto-es-una-pagina-de-prueba","promotor","2011-11-30 07:17:38","&lt;p&gt;\n	Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam enim nulla, semper nec auctor nec, interdum vel tortor. Sed auctor condimentum sem, vel laoreet nibh dapibus non. Proin pharetra cursus tortor, ut vestibulum nibh rhoncus vitae. Sed mauris felis, ornare vitae aliquet quis, consectetur quis sem. Donec nulla purus, placerat vitae placerat quis, vestibulum vel libero. Morbi eget lobortis nisl. Nam hendrerit augue quis tortor tempor tempus. Nulla facilisi. Nulla cursus ante ut ipsum hendrerit sodales in eu odio. Nulla faucibus dui quis risus iaculis eu cursus nulla pellentesque. Fusce eget luctus neque.&lt;/p&gt;\n","2","1");
INSERT INTO vdydev_page VALUES("22","","Esto es una p&aacute;gina de prueba","esto-es-una-pagina-de-prueba","promotor","2011-11-30 07:18:39","&lt;p&gt;\n	Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam enim nulla, semper nec auctor nec, interdum vel tortor. Sed auctor condimentum sem, vel laoreet nibh dapibus non. Proin pharetra cursus tortor, ut vestibulum nibh rhoncus vitae. Sed mauris felis, ornare vitae aliquet quis, consectetur quis sem. Donec nulla purus, placerat vitae placerat quis, vestibulum vel libero. Morbi eget lobortis nisl. Nam hendrerit augue quis tortor tempor tempus. Nulla facilisi. Nulla cursus ante ut ipsum hendrerit sodales in eu odio. Nulla faucibus dui quis risus iaculis eu cursus nulla pellentesque. Fusce eget luctus neque.&lt;/p&gt;\n","2","1");
INSERT INTO vdydev_page VALUES("23","","Esto es una p&aacute;gina de prueba","esto-es-una-pagina-de-prueba","promotor","2011-11-30 07:18:54","&lt;p&gt;\n	Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam enim nulla, semper nec auctor nec, interdum vel tortor. Sed auctor condimentum sem, vel laoreet nibh dapibus non. Proin pharetra cursus tortor, ut vestibulum nibh rhoncus vitae. Sed mauris felis, ornare vitae aliquet quis, consectetur quis sem. Donec nulla purus, placerat vitae placerat quis, vestibulum vel libero. Morbi eget lobortis nisl. Nam hendrerit augue quis tortor tempor tempus. Nulla facilisi. Nulla cursus ante ut ipsum hendrerit sodales in eu odio. Nulla faucibus dui quis risus iaculis eu cursus nulla pellentesque. Fusce eget luctus neque.&lt;/p&gt;\n","2","1");
INSERT INTO vdydev_page VALUES("24","","Esto es una p&aacute;gina de prueba","esto-es-una-pagina-de-prueba","promotor","2011-11-30 07:20:48","&lt;p&gt;\n	Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam enim nulla, semper nec auctor nec, interdum vel tortor. Sed auctor condimentum sem, vel laoreet nibh dapibus non. Proin pharetra cursus tortor, ut vestibulum nibh rhoncus vitae. Sed mauris felis, ornare vitae aliquet quis, consectetur quis sem. Donec nulla purus, placerat vitae placerat quis, vestibulum vel libero. Morbi eget lobortis nisl. Nam hendrerit augue quis tortor tempor tempus. Nulla facilisi. Nulla cursus ante ut ipsum hendrerit sodales in eu odio. Nulla faucibus dui quis risus iaculis eu cursus nulla pellentesque. Fusce eget luctus neque.&lt;/p&gt;\n","2","1");



DROP TABLE vdydev_profesional_has_pages;

CREATE TABLE `vdydev_profesional_has_pages` (
  `id_profesional` varchar(100) NOT NULL,
  `id_page` int(11) NOT NULL,
  PRIMARY KEY (`id_profesional`,`id_page`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO vdydev_profesional_has_pages VALUES("P1","3");



DROP TABLE vdydev_promocion;

CREATE TABLE `vdydev_promocion` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `slug` varchar(255) COLLATE utf8_bin NOT NULL,
  `descripcion` text COLLATE utf8_bin,
  `descripcion_corta` text COLLATE utf8_bin,
  `tipo` int(3) NOT NULL DEFAULT '0',
  `numero` int(11) NOT NULL,
  `via` varchar(255) COLLATE utf8_bin NOT NULL,
  `municipio` varchar(255) COLLATE utf8_bin NOT NULL,
  `codigo_postal` varchar(25) COLLATE utf8_bin NOT NULL,
  `provincia` varchar(255) COLLATE utf8_bin NOT NULL,
  `estado` varchar(255) COLLATE utf8_bin NOT NULL,
  `pais` varchar(255) COLLATE utf8_bin NOT NULL,
  `map_lat` double NOT NULL,
  `map_lng` double NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `creator` varchar(100) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO vdydev_promocion VALUES("1","Obra nueva en Rocafort","obra-nueva-en-rocafort","NUEVAS VENTAJAS FICSA: 100% mejor precio y hasta 100% de financiación además le regalamos los electrodomésticos ecológicos “su cocina totalmente equipada con la garantía Balay”.","NUEVAS VENTAJAS FICSA: 100% mejor precio y hasta 100% de financiación además le regalamos los electrodomésticos ecológicos “su cocina totalmente equipada con la garantía Balay”. Conjunto residencial de 22 viviendas en un entorno rodeado de naturaleza y con todos los servicios a tu alcance. Viviendas de 2 habitaciones desde 92.400 euros. Vive en L’Alt Residencial, viviendas muy luminosas en la mejor zona residencial de Náquera y junto al centro urbano. ","0","1","Paseo de la Acequia","Rocafort","46111","Valencia","Comunitat Valenciana","España","-0.40964","39.53144","1","2011-11-24 00:00:00","andreums");



DROP TABLE vdydev_promotor;

CREATE TABLE `vdydev_promotor` (
  `id` varchar(255) NOT NULL,
  `razon_social` varchar(150) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `cif` varchar(100) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `logotipo` varchar(512) NOT NULL,
  `icono` varchar(255) NOT NULL,
  `descripcion_corta` text,
  `descripcion` text,
  `tipo` int(2) NOT NULL DEFAULT '0',
  `creator` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `telefono` int(20) NOT NULL,
  `fax` int(20) NOT NULL,
  `webpage` varchar(512) NOT NULL,
  `approved` int(1) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `via` varchar(100) NOT NULL,
  `municipio` varchar(100) NOT NULL,
  `codigo_postal` varchar(20) NOT NULL,
  `provincia` varchar(100) NOT NULL,
  `estado` varchar(100) NOT NULL,
  `pais` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO vdydev_promotor VALUES("1","Promotor ficticio S.L.","Promociones Ficticio","CIF FICTICIO","1","2011-11-24 00:00:00","promotor_p1.png","http://ohohoh.es/favicon.ico","<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non dui tortor, eu rhoncus nisl. Cras luctus vulputate lobortis.</p>","<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non dui tortor, eu rhoncus nisl. Cras luctus vulputate lobortis. Fusce sit amet elit nibh, eget cursus massa. Sed eget enim dapibus elit imperdiet sollicitudin eu vitae nunc. Sed gravida metus a est viverra sodales. Fusce nec est risus, sed ornare nunc.</p>","0","promotor","promotor-ficticio","961399069","961399069","http://www.google.es","1","1","Calle Colón","Valencia","46001","Valencia","Comunitat Valenciana","España");
INSERT INTO vdydev_promotor VALUES("P2","Promotor ficticio S.L.","Promociones Ficticio","CIF FICTICIO","1","2011-11-24 00:00:00","YA_HABRA_LOGOTIPO_ALGUN_DIA","http://ohohoh.es/favicon.ico","","","0","andreums","promociones-ficticio","0","0","","0","","","","","","","0");



DROP TABLE vdydev_promotor_has_oficinas;

CREATE TABLE `vdydev_promotor_has_oficinas` (
  `id_promotor` int(11) NOT NULL,
  `id_oficina` int(11) NOT NULL,
  PRIMARY KEY (`id_promotor`,`id_oficina`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO vdydev_promotor_has_oficinas VALUES("1","1");
INSERT INTO vdydev_promotor_has_oficinas VALUES("1","2");



DROP TABLE vdydev_promotor_has_pages;

CREATE TABLE `vdydev_promotor_has_pages` (
  `id_promotor` int(11) NOT NULL,
  `id_page` int(11) NOT NULL,
  PRIMARY KEY (`id_promotor`,`id_page`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO vdydev_promotor_has_pages VALUES("1","3");
INSERT INTO vdydev_promotor_has_pages VALUES("1","24");



DROP TABLE vdydev_promotor_has_promociones;

CREATE TABLE `vdydev_promotor_has_promociones` (
  `id_promotor` int(11) NOT NULL,
  `id_promocion` int(11) NOT NULL,
  PRIMARY KEY (`id_promotor`,`id_promocion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO vdydev_promotor_has_promociones VALUES("1","1");



DROP TABLE vdydev_role;

CREATE TABLE `vdydev_role` (
  `role` varchar(20) COLLATE utf8_bin NOT NULL,
  `enabled` int(1) NOT NULL DEFAULT '1',
  `description` text COLLATE utf8_bin,
  PRIMARY KEY (`role`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO vdydev_role VALUES("administrador","1","");
INSERT INTO vdydev_role VALUES("root","1","");



DROP TABLE vdydev_status_promocion;

CREATE TABLE `vdydev_status_promocion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_promocion` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  `status` int(2) NOT NULL,
  `description` varchar(512) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;




DROP TABLE vdydev_tag;

CREATE TABLE `vdydev_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO vdydev_tag VALUES("1","foo","foo","FooBarTargezeta","");



DROP TABLE vdydev_user;

CREATE TABLE `vdydev_user` (
  `username` varchar(100) COLLATE utf8_bin NOT NULL,
  `password` varchar(100) COLLATE utf8_bin NOT NULL,
  `email` varchar(50) COLLATE utf8_bin NOT NULL,
  `name` varchar(50) COLLATE utf8_bin NOT NULL,
  `activation_key` varchar(100) COLLATE utf8_bin NOT NULL,
  `date_register` datetime DEFAULT NULL,
  `language` varchar(10) COLLATE utf8_bin NOT NULL,
  `theme` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT 'default',
  `display_name` varchar(128) COLLATE utf8_bin NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `type` int(2) NOT NULL DEFAULT '0',
  `telephone` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO vdydev_user VALUES("andreums","9f73319327070d948b8d0d44eb35780470bc4df8","andresmartinezsoto@gmail.com","AndrÃ©s Ignacio MartÃ­nez Soto","9f73319327070d948b8d0d44eb35780470bc4df8","2011-11-23 00:00:00","ca_ES","default","AndrÃ©s MartÃ­nez","0","0","0");
INSERT INTO vdydev_user VALUES("paraborrar","paraborrar","paraborrar@paraborrar.es","usuario paraborrar","paraborrar","2011-11-23 00:00:00","ca_ES","default","paraborrar","1","1","123132");
INSERT INTO vdydev_user VALUES("promotor","956944f50c2960021ad20cbbd1f074fab5ce6d07","promotor@promotor.es","Usuario de prueba Promotor","956944f50c2960021ad20cbbd1f074fab5ce6d07","2011-11-29 00:00:00","ca_ES","default","Usuario promotor de pruebas","0","2","961399069");



DROP TABLE vdydev_user_has_roles;

CREATE TABLE `vdydev_user_has_roles` (
  `username` varchar(50) COLLATE utf8_bin NOT NULL,
  `role` varchar(20) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`username`,`role`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO vdydev_user_has_roles VALUES("andreums","administrador");
INSERT INTO vdydev_user_has_roles VALUES("andreums","editor");
INSERT INTO vdydev_user_has_roles VALUES("andreums","root");



DROP TABLE vdydev_zone;

CREATE TABLE `vdydev_zone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_parent` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `description` text COLLATE utf8_bin,
  `status` int(11) NOT NULL DEFAULT '0',
  `type` int(2) NOT NULL DEFAULT '1',
  `center_lon` double NOT NULL,
  `center_lat` double NOT NULL,
  `creator` varchar(255) COLLATE utf8_bin NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO vdydev_zone VALUES("2","","foo","dfg","0","1","2341234","12341234","2314sdfasdf","2011-11-24 00:00:00");
INSERT INTO vdydev_zone VALUES("3","","La Plana Baixa","La Plana Baixa","1","3","39.8686416555806","-0.124969482421875","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("4","","Alt Millars","Alt Millars","1","3","40.0833252155441","-0.60150146484375","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("5","","Alcalatén","Alcalatén","1","3","40.2019526895406","-0.46417236328125","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("6","","La Plana Alta","La Plana Alta","1","3","40.1830701485253","0.10986328125","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("7","","Alt Maestrat","Alt Maestrat","1","3","40.3570097457756","-0.32958984375","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("8","","Els Ports","Els Ports","1","3","40.6868863821511","-0.223846435546875","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("9","","Baix Maestrar","Baix Maestrar","1","3","40.4553072121315","0.27740478515625","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("10","","Castellón","Castellón","1","3","39.9597540454482","-0.0240325927734375","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("11","","El Camp del Turia","El Camp del Turia","1","3","39.6245825","-0.5947581","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("12","","Los Serranos","Los Serranos","1","3","39.830686335335","-1.15081787109375","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("13","","La Plana de Utiel-Requena","La Plana de Utiel-Requena","1","3","39.4701251223582","-1.4556884765625","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("14","","La Hoya de Buñol","La Hoya de Buñol","1","3","39.5199329405009","-0.9393310546875","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("15","","Valle de Cofrentes-Ayora","Valle de Cofrentes-Ayora","1","3","39.1257989811816","-1.23733520507813","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("16","","La Canal de Navarrés","La Canal de Navarrés","1","3","39.1460376744642","-0.89263916015625","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("17","","La Ribera Alta","La Ribera Alta","1","3","39.2428896908264","-0.692138671875","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("18","","La Costera","La Costera","1","3","38.9156130251313","-0.81298828125","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("19","","El Camp de Morvedre","El Camp de Morvedre","1","3","39.6801381","-0.2782788","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("20","","L´Horta Nord","L´Horta Nord","1","3","39.6009267806137","-0.290451049804687","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("21","","L´Horta Oest","L´Horta Oest","1","3","39.4404354190848","-0.665359497070313","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("22","","L´Horta Sud","L´Horta Sud","1","3","39.3560688711655","-0.432586669921875","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("23","","La Ribera Baixa","La Ribera Baixa","1","3","39.2026363","-0.3106423","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("24","","La Safor","La Safor","1","3","39.0938313069236","-0.23345947265625","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("25","","La Vall d´Albaida","La Vall d´Albaida","1","3","38.8525423903642","-0.5712890625","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("26","","Valencia","Valencia","1","3","39.4759555404206","-0.37628173828125","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("27","","Ademuz","Ademuz","1","3","40.0617367","-1.2865257","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("28","","Marina Baixa","Marina Baixa","1","3","38.6072127893516","-0.086517333984375","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("29","","Comtat","Comtat","1","3","38.8097507972383","-0.45867919921875","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("30","","L´Alcoià","L´Alcoià","1","3","38.6983723058933","-0.704498291015625","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("31","","El Alto Vinalopó","El Alto Vinalopó","1","3","38.6007736173876","-1.0052490234375","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("32","","El Vinalopó Medio","El Vinalopó Medio","1","3","38.4105582509461","-1.06430053710938","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("33","","Baix Vinalopó","Baix Vinalopó","1","3","38.1777509666256","-0.682525634765625","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("34","","La Vega Baja","La Vega Baja","1","3","38.0372756881656","-0.784149169921875","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("35","","Alicante","Alicante","1","3","38.3316922656393","-0.515327453613281","andreums","2011-11-23 19:23:18");
INSERT INTO vdydev_zone VALUES("36","","L´Alicantí","L´Alicantí","1","3","38.4686433103605","-0.503997802734375","andreums","2011-11-23 19:23:18");



DROP TABLE vdydev_zone_has_point;

CREATE TABLE `vdydev_zone_has_point` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `id_zone` int(11) NOT NULL,
  `point_lon` double NOT NULL,
  `point_lat` double NOT NULL,
  `point_order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=978 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO vdydev_zone_has_point VALUES("67","1","40.124447","-0.452614","2");
INSERT INTO vdydev_zone_has_point VALUES("66","1","40.093464","-0.425148","1");
INSERT INTO vdydev_zone_has_point VALUES("65","1","40.062912","-0.368443","0");
INSERT INTO vdydev_zone_has_point VALUES("64","0","39.73164","-0.184135","29");
INSERT INTO vdydev_zone_has_point VALUES("63","0","39.782314","-0.350304","28");
INSERT INTO vdydev_zone_has_point VALUES("62","0","39.846661","-0.337944","27");
INSERT INTO vdydev_zone_has_point VALUES("61","0","39.906208","-0.4533","26");
INSERT INTO vdydev_zone_has_point VALUES("60","0","39.946754","-0.447121","25");
INSERT INTO vdydev_zone_has_point VALUES("59","0","39.993587","-0.403862","24");
INSERT INTO vdydev_zone_has_point VALUES("58","0","40.042492","-0.380516","23");
INSERT INTO vdydev_zone_has_point VALUES("57","0","40.062832","-0.369415","22");
INSERT INTO vdydev_zone_has_point VALUES("56","0","40.062992","-0.334511","21");
INSERT INTO vdydev_zone_has_point VALUES("55","0","40.054054","-0.329018","20");
INSERT INTO vdydev_zone_has_point VALUES("54","0","40.020412","-0.302238","19");
INSERT INTO vdydev_zone_has_point VALUES("53","0","40.011997","-0.226707","18");
INSERT INTO vdydev_zone_has_point VALUES("52","0","39.992535","-0.210228","17");
INSERT INTO vdydev_zone_has_point VALUES("51","0","39.979382","-0.166283","16");
INSERT INTO vdydev_zone_has_point VALUES("50","0","39.96307","-0.118217","15");
INSERT INTO vdydev_zone_has_point VALUES("49","0","39.953594","-0.085945","14");
INSERT INTO vdydev_zone_has_point VALUES("48","0","39.935696","-0.065346","13");
INSERT INTO vdydev_zone_has_point VALUES("47","0","39.921482","-0.031014","12");
INSERT INTO vdydev_zone_has_point VALUES("46","0","39.907261","-0.011101","11");
INSERT INTO vdydev_zone_has_point VALUES("45","0","39.898308","-0.024834","10");
INSERT INTO vdydev_zone_has_point VALUES("44","0","39.871433","-0.05024","9");
INSERT INTO vdydev_zone_has_point VALUES("43","0","39.859314","-0.070152","8");
INSERT INTO vdydev_zone_has_point VALUES("42","0","39.839809","-0.092812","7");
INSERT INTO vdydev_zone_has_point VALUES("41","0","39.825043","-0.091438","6");
INSERT INTO vdydev_zone_has_point VALUES("40","0","39.82188","-0.106545","5");
INSERT INTO vdydev_zone_has_point VALUES("39","0","39.808693","-0.117531","4");
INSERT INTO vdydev_zone_has_point VALUES("38","0","39.792339","-0.129204","3");
INSERT INTO vdydev_zone_has_point VALUES("37","0","39.773346","-0.14431","2");
INSERT INTO vdydev_zone_has_point VALUES("36","0","39.756454","-0.162163","1");
INSERT INTO vdydev_zone_has_point VALUES("35","0","39.73164","-0.184135","0");
INSERT INTO vdydev_zone_has_point VALUES("68","1","40.155945","-0.46154","3");
INSERT INTO vdydev_zone_has_point VALUES("69","1","40.176933","-0.4842","4");
INSERT INTO vdydev_zone_has_point VALUES("70","1","40.229374","-0.494499","5");
INSERT INTO vdydev_zone_has_point VALUES("71","1","40.23724","-0.528145","6");
INSERT INTO vdydev_zone_has_point VALUES("72","1","40.254536","-0.545311","7");
INSERT INTO vdydev_zone_has_point VALUES("73","1","40.206306","-0.56179","8");
INSERT INTO vdydev_zone_has_point VALUES("74","1","40.172211","-0.573463","9");
INSERT INTO vdydev_zone_has_point VALUES("75","1","40.143871","-0.578957","10");
INSERT INTO vdydev_zone_has_point VALUES("76","1","40.131798","-0.58445","11");
INSERT INTO vdydev_zone_has_point VALUES("77","1","40.132851","-0.594063","12");
INSERT INTO vdydev_zone_has_point VALUES("78","1","40.124973","-0.613289","13");
INSERT INTO vdydev_zone_has_point VALUES("79","1","40.101341","-0.629082","14");
INSERT INTO vdydev_zone_has_point VALUES("80","1","40.067562","-0.615921","15");
INSERT INTO vdydev_zone_has_point VALUES("81","1","40.017258","-0.569344","16");
INSERT INTO vdydev_zone_has_point VALUES("82","1","39.967808","-0.515099","17");
INSERT INTO vdydev_zone_has_point VALUES("83","1","39.925694","-0.47802","18");
INSERT INTO vdydev_zone_has_point VALUES("84","1","39.914108","-0.46978","19");
INSERT INTO vdydev_zone_has_point VALUES("85","1","39.905155","-0.451927","20");
INSERT INTO vdydev_zone_has_point VALUES("86","1","39.946068","-0.447006","21");
INSERT INTO vdydev_zone_has_point VALUES("87","1","39.991852","-0.404434","22");
INSERT INTO vdydev_zone_has_point VALUES("88","1","40.062912","-0.368443","23");
INSERT INTO vdydev_zone_has_point VALUES("89","2","40.011917","-0.22665","0");
INSERT INTO vdydev_zone_has_point VALUES("90","2","40.019543","-0.192318","1");
INSERT INTO vdydev_zone_has_point VALUES("91","2","40.028744","-0.163822","2");
INSERT INTO vdydev_zone_has_point VALUES("92","2","40.036633","-0.150433","3");
INSERT INTO vdydev_zone_has_point VALUES("93","2","40.047932","-0.139446","4");
INSERT INTO vdydev_zone_has_point VALUES("94","2","40.055553","-0.146313","5");
INSERT INTO vdydev_zone_has_point VALUES("95","2","40.067379","-0.155926","6");
INSERT INTO vdydev_zone_has_point VALUES("96","2","40.078598","-0.165482","7");
INSERT INTO vdydev_zone_has_point VALUES("97","2","40.089104","-0.173035","8");
INSERT INTO vdydev_zone_has_point VALUES("98","2","40.120693","-0.184765","9");
INSERT INTO vdydev_zone_has_point VALUES("99","2","40.174835","-0.183449","10");
INSERT INTO vdydev_zone_has_point VALUES("100","2","40.219414","-0.188255","11");
INSERT INTO vdydev_zone_has_point VALUES("101","2","40.262028","-0.232315","12");
INSERT INTO vdydev_zone_has_point VALUES("102","2","40.30917","-0.383377","13");
INSERT INTO vdydev_zone_has_point VALUES("103","2","40.292412","-0.40535","14");
INSERT INTO vdydev_zone_has_point VALUES("104","2","40.274601","-0.382004","15");
INSERT INTO vdydev_zone_has_point VALUES("105","2","40.25993","-0.395737","16");
INSERT INTO vdydev_zone_has_point VALUES("106","2","40.245258","-0.428696","17");
INSERT INTO vdydev_zone_has_point VALUES("107","2","40.234776","-0.460281","18");
INSERT INTO vdydev_zone_has_point VALUES("108","2","40.231628","-0.495987","19");
INSERT INTO vdydev_zone_has_point VALUES("109","2","40.17709","-0.487747","20");
INSERT INTO vdydev_zone_has_point VALUES("110","2","40.155052","-0.461655","21");
INSERT INTO vdydev_zone_has_point VALUES("111","2","40.121456","-0.452042","22");
INSERT INTO vdydev_zone_has_point VALUES("112","2","40.090996","-0.424576","23");
INSERT INTO vdydev_zone_has_point VALUES("113","2","40.062622","-0.369644","24");
INSERT INTO vdydev_zone_has_point VALUES("114","2","40.063675","-0.335312","25");
INSERT INTO vdydev_zone_has_point VALUES("115","2","40.018463","-0.303726","26");
INSERT INTO vdydev_zone_has_point VALUES("116","2","40.011917","-0.22665","27");
INSERT INTO vdydev_zone_has_point VALUES("117","3","40.04916","-0.14007","0");
INSERT INTO vdydev_zone_has_point VALUES("118","3","40.0756","-0.16559","1");
INSERT INTO vdydev_zone_has_point VALUES("119","3","40.083851","-0.15724","2");
INSERT INTO vdydev_zone_has_point VALUES("120","3","40.090408","-0.14865","3");
INSERT INTO vdydev_zone_has_point VALUES("121","3","40.118141","-0.14156","4");
INSERT INTO vdydev_zone_has_point VALUES("122","3","40.158936","-0.101624","5");
INSERT INTO vdydev_zone_has_point VALUES("123","3","40.214012","-0.079651","6");
INSERT INTO vdydev_zone_has_point VALUES("124","3","40.261189","-0.079651","7");
INSERT INTO vdydev_zone_has_point VALUES("125","3","40.322468","-0.045319","8");
INSERT INTO vdydev_zone_has_point VALUES("126","3","40.379662","-0.00492","9");
INSERT INTO vdydev_zone_has_point VALUES("127","3","40.399899","0.01373","10");
INSERT INTO vdydev_zone_has_point VALUES("128","3","40.391537","0.052185","11");
INSERT INTO vdydev_zone_has_point VALUES("129","3","40.385929","0.09052","12");
INSERT INTO vdydev_zone_has_point VALUES("130","3","40.335178","0.11524","13");
INSERT INTO vdydev_zone_has_point VALUES("131","3","40.32262","0.14476","14");
INSERT INTO vdydev_zone_has_point VALUES("132","3","40.273918","0.17841","15");
INSERT INTO vdydev_zone_has_point VALUES("133","3","40.2561","0.21892","16");
INSERT INTO vdydev_zone_has_point VALUES("134","3","40.224651","0.24295","17");
INSERT INTO vdydev_zone_has_point VALUES("135","3","40.202629","0.26287","18");
INSERT INTO vdydev_zone_has_point VALUES("136","3","40.19791","0.26355","19");
INSERT INTO vdydev_zone_has_point VALUES("137","3","40.190571","0.22098","20");
INSERT INTO vdydev_zone_has_point VALUES("138","3","40.16486","0.18734","21");
INSERT INTO vdydev_zone_has_point VALUES("139","3","40.133888","0.16811","22");
INSERT INTO vdydev_zone_has_point VALUES("140","3","40.086632","0.14888","23");
INSERT INTO vdydev_zone_has_point VALUES("141","3","40.077702","0.13652","24");
INSERT INTO vdydev_zone_has_point VALUES("142","3","40.057732","0.12142","25");
INSERT INTO vdydev_zone_has_point VALUES("143","3","40.046692","0.10494","26");
INSERT INTO vdydev_zone_has_point VALUES("144","3","40.052479","0.08503","27");
INSERT INTO vdydev_zone_has_point VALUES("145","3","40.041962","0.06786","28");
INSERT INTO vdydev_zone_has_point VALUES("146","3","40.031448","0.04932","29");
INSERT INTO vdydev_zone_has_point VALUES("147","3","40.027481","0.0448","30");
INSERT INTO vdydev_zone_has_point VALUES("148","3","40.02446","0.0436","31");
INSERT INTO vdydev_zone_has_point VALUES("149","3","40.019329","0.04119","32");
INSERT INTO vdydev_zone_has_point VALUES("150","3","40.013538","0.0381","33");
INSERT INTO vdydev_zone_has_point VALUES("151","3","40.012619","0.03536","34");
INSERT INTO vdydev_zone_has_point VALUES("152","3","40.015251","0.03313","35");
INSERT INTO vdydev_zone_has_point VALUES("153","3","40.01709","0.03347","36");
INSERT INTO vdydev_zone_has_point VALUES("154","3","40.0163","0.0278","37");
INSERT INTO vdydev_zone_has_point VALUES("155","3","40.01907","0.02283","38");
INSERT INTO vdydev_zone_has_point VALUES("156","3","40.020908","0.02077","39");
INSERT INTO vdydev_zone_has_point VALUES("157","3","40.023399","0.01545","40");
INSERT INTO vdydev_zone_has_point VALUES("158","3","40.024319","0.01098","41");
INSERT INTO vdydev_zone_has_point VALUES("159","3","40.026299","-0.00051","42");
INSERT INTO vdydev_zone_has_point VALUES("160","3","40.027222","-0.01098","43");
INSERT INTO vdydev_zone_has_point VALUES("161","3","40.030762","-0.02008","44");
INSERT INTO vdydev_zone_has_point VALUES("162","3","40.043121","-0.04755","45");
INSERT INTO vdydev_zone_has_point VALUES("163","3","40.034309","-0.0805","46");
INSERT INTO vdydev_zone_has_point VALUES("164","3","40.036678","-0.14883","47");
INSERT INTO vdydev_zone_has_point VALUES("165","3","40.021301","-0.18573","48");
INSERT INTO vdydev_zone_has_point VALUES("166","3","40.00684","-0.17269","49");
INSERT INTO vdydev_zone_has_point VALUES("167","3","40.000259","-0.14556","50");
INSERT INTO vdydev_zone_has_point VALUES("168","3","39.985802","-0.11295","51");
INSERT INTO vdydev_zone_has_point VALUES("169","3","39.984482","-0.09063","52");
INSERT INTO vdydev_zone_has_point VALUES("170","3","39.971329","-0.08445","53");
INSERT INTO vdydev_zone_has_point VALUES("171","3","39.969219","-0.06712","54");
INSERT INTO vdydev_zone_has_point VALUES("172","3","39.95409","-0.03175","55");
INSERT INTO vdydev_zone_has_point VALUES("173","3","39.941719","0.00291","56");
INSERT INTO vdydev_zone_has_point VALUES("174","3","39.912521","0.00263","57");
INSERT INTO vdydev_zone_has_point VALUES("175","3","39.905682","-0.01316","58");
INSERT INTO vdydev_zone_has_point VALUES("176","3","39.922001","-0.03238","59");
INSERT INTO vdydev_zone_has_point VALUES("177","3","39.935692","-0.06603","60");
INSERT INTO vdydev_zone_has_point VALUES("178","3","39.95359","-0.08731","61");
INSERT INTO vdydev_zone_has_point VALUES("179","3","39.965172","-0.12714","62");
INSERT INTO vdydev_zone_has_point VALUES("180","3","39.977798","-0.1601","63");
INSERT INTO vdydev_zone_has_point VALUES("181","3","39.992001","-0.21022","64");
INSERT INTO vdydev_zone_has_point VALUES("182","3","40.01199","-0.22739","65");
INSERT INTO vdydev_zone_has_point VALUES("183","3","40.021301","-0.18402","66");
INSERT INTO vdydev_zone_has_point VALUES("184","3","40.03339","-0.15432","67");
INSERT INTO vdydev_zone_has_point VALUES("185","3","40.041279","-0.14522","68");
INSERT INTO vdydev_zone_has_point VALUES("186","3","40.04916","-0.14007","69");
INSERT INTO vdydev_zone_has_point VALUES("187","4","40.306019","-0.37239","0");
INSERT INTO vdydev_zone_has_point VALUES("188","4","40.358372","-0.3202","1");
INSERT INTO vdydev_zone_has_point VALUES("189","4","40.369881","-0.27763","2");
INSERT INTO vdydev_zone_has_point VALUES("190","4","40.413811","-0.31471","3");
INSERT INTO vdydev_zone_has_point VALUES("191","4","40.45039","-0.34629","4");
INSERT INTO vdydev_zone_has_point VALUES("192","4","40.462929","-0.32569","5");
INSERT INTO vdydev_zone_has_point VALUES("193","4","40.479649","-0.27214","6");
INSERT INTO vdydev_zone_has_point VALUES("194","4","40.534988","-0.1609","7");
INSERT INTO vdydev_zone_has_point VALUES("195","4","40.469929","-0.08102","8");
INSERT INTO vdydev_zone_has_point VALUES("196","4","40.51828","-0.02357","9");
INSERT INTO vdydev_zone_has_point VALUES("197","4","40.529449","0.01647","10");
INSERT INTO vdydev_zone_has_point VALUES("198","4","40.531849","0.06294","11");
INSERT INTO vdydev_zone_has_point VALUES("199","4","40.453529","0.04921","12");
INSERT INTO vdydev_zone_has_point VALUES("200","4","40.39362","0.00824","13");
INSERT INTO vdydev_zone_has_point VALUES("201","4","40.337429","-0.03593","14");
INSERT INTO vdydev_zone_has_point VALUES("202","4","40.261711","-0.07827","15");
INSERT INTO vdydev_zone_has_point VALUES("203","4","40.21558","-0.07827","16");
INSERT INTO vdydev_zone_has_point VALUES("204","4","40.155048","-0.10322","17");
INSERT INTO vdydev_zone_has_point VALUES("205","4","40.121449","-0.1403","18");
INSERT INTO vdydev_zone_has_point VALUES("206","4","40.090988","-0.14717","19");
INSERT INTO vdydev_zone_has_point VALUES("207","4","40.077332","-0.16502","20");
INSERT INTO vdydev_zone_has_point VALUES("208","4","40.087528","-0.172348","21");
INSERT INTO vdydev_zone_has_point VALUES("209","4","40.09856","-0.177155","22");
INSERT INTO vdydev_zone_has_point VALUES("210","4","40.119564","-0.186081","23");
INSERT INTO vdydev_zone_has_point VALUES("211","4","40.147915","-0.184708","24");
INSERT INTO vdydev_zone_has_point VALUES("212","4","40.176041","-0.18562","25");
INSERT INTO vdydev_zone_has_point VALUES("213","4","40.22345","-0.192261","26");
INSERT INTO vdydev_zone_has_point VALUES("214","4","40.262238","-0.233459","27");
INSERT INTO vdydev_zone_has_point VALUES("215","4","40.306019","-0.37239","28");
INSERT INTO vdydev_zone_has_point VALUES("216","5","40.479492","-0.274773","0");
INSERT INTO vdydev_zone_has_point VALUES("217","5","40.498814","-0.279579","1");
INSERT INTO vdydev_zone_has_point VALUES("218","5","40.510822","-0.298805","2");
INSERT INTO vdydev_zone_has_point VALUES("219","5","40.541618","-0.296059","3");
INSERT INTO vdydev_zone_has_point VALUES("220","5","40.602642","-0.293312","4");
INSERT INTO vdydev_zone_has_point VALUES("221","5","40.610981","-0.296059","5");
INSERT INTO vdydev_zone_has_point VALUES("222","5","40.611137","-0.373764","6");
INSERT INTO vdydev_zone_has_point VALUES("223","5","40.620522","-0.377884","7");
INSERT INTO vdydev_zone_has_point VALUES("224","5","40.662201","-0.379257","8");
INSERT INTO vdydev_zone_has_point VALUES("225","5","40.681992","-0.358658","9");
INSERT INTO vdydev_zone_has_point VALUES("226","5","40.68095","-0.317459","10");
INSERT INTO vdydev_zone_has_point VALUES("227","5","40.668453","-0.30098","11");
INSERT INTO vdydev_zone_has_point VALUES("228","5","40.692406","-0.239182","12");
INSERT INTO vdydev_zone_has_point VALUES("229","5","40.737164","-0.233688","13");
INSERT INTO vdydev_zone_has_point VALUES("230","5","40.782932","-0.197983","14");
INSERT INTO vdydev_zone_has_point VALUES("231","5","40.788132","-0.145798","15");
INSERT INTO vdydev_zone_has_point VALUES("232","5","40.75069","-0.123825","16");
INSERT INTO vdydev_zone_has_point VALUES("233","5","40.72884","-0.055161","17");
INSERT INTO vdydev_zone_has_point VALUES("234","5","40.726757","0.013504","18");
INSERT INTO vdydev_zone_has_point VALUES("235","5","40.69553","0.02861","19");
INSERT INTO vdydev_zone_has_point VALUES("236","5","40.718433","0.093155","20");
INSERT INTO vdydev_zone_has_point VALUES("237","5","40.717392","0.143967","21");
INSERT INTO vdydev_zone_has_point VALUES("238","5","40.676785","0.104141","22");
INSERT INTO vdydev_zone_has_point VALUES("239","5","40.645531","0.061569","23");
INSERT INTO vdydev_zone_has_point VALUES("240","5","40.612179","0.083542","24");
INSERT INTO vdydev_zone_has_point VALUES("241","5","40.589241","0.121994","25");
INSERT INTO vdydev_zone_has_point VALUES("242","5","40.55899","0.131607","26");
INSERT INTO vdydev_zone_has_point VALUES("243","5","40.53186","0.058823","27");
INSERT INTO vdydev_zone_has_point VALUES("244","5","40.529457","0.017853","28");
INSERT INTO vdydev_zone_has_point VALUES("245","5","40.51902","-0.017853","29");
INSERT INTO vdydev_zone_has_point VALUES("246","5","40.469204","-0.07988","30");
INSERT INTO vdydev_zone_has_point VALUES("247","5","40.532902","-0.162277","31");
INSERT INTO vdydev_zone_has_point VALUES("248","5","40.479492","-0.274773","32");
INSERT INTO vdydev_zone_has_point VALUES("249","6","40.718796","0.142708","0");
INSERT INTO vdydev_zone_has_point VALUES("250","6","40.733887","0.173607","1");
INSERT INTO vdydev_zone_has_point VALUES("251","6","40.732327","0.225105","2");
INSERT INTO vdydev_zone_has_point VALUES("252","6","40.70266","0.240211","3");
INSERT INTO vdydev_zone_has_point VALUES("253","6","40.704742","0.266304","4");
INSERT INTO vdydev_zone_has_point VALUES("254","6","40.684959","0.291023","5");
INSERT INTO vdydev_zone_has_point VALUES("255","6","40.644333","0.266991","6");
INSERT INTO vdydev_zone_has_point VALUES("256","6","40.62714","0.277977","7");
INSERT INTO vdydev_zone_has_point VALUES("257","6","40.619843","0.306816","8");
INSERT INTO vdydev_zone_has_point VALUES("258","6","40.609417","0.332222","9");
INSERT INTO vdydev_zone_has_point VALUES("259","6","40.613586","0.361061","10");
INSERT INTO vdydev_zone_has_point VALUES("260","6","40.605247","0.379601","11");
INSERT INTO vdydev_zone_has_point VALUES("261","6","40.593258","0.411873","12");
INSERT INTO vdydev_zone_has_point VALUES("262","6","40.574482","0.440712","13");
INSERT INTO vdydev_zone_has_point VALUES("263","6","40.547878","0.435905","14");
INSERT INTO vdydev_zone_has_point VALUES("264","6","40.531181","0.475044","15");
INSERT INTO vdydev_zone_has_point VALUES("265","6","40.520741","0.519676","16");
INSERT INTO vdydev_zone_has_point VALUES("266","6","40.476883","0.488777","17");
INSERT INTO vdydev_zone_has_point VALUES("267","6","40.458599","0.475044","18");
INSERT INTO vdydev_zone_has_point VALUES("268","6","40.447624","0.461998","19");
INSERT INTO vdydev_zone_has_point VALUES("269","6","40.43195","0.464058","20");
INSERT INTO vdydev_zone_has_point VALUES("270","6","40.436653","0.443459","21");
INSERT INTO vdydev_zone_has_point VALUES("271","6","40.411037","0.437279","22");
INSERT INTO vdydev_zone_has_point VALUES("272","6","40.397968","0.416679","23");
INSERT INTO vdydev_zone_has_point VALUES("273","6","40.373909","0.405693","24");
INSERT INTO vdydev_zone_has_point VALUES("274","6","40.354549","0.411873","25");
INSERT INTO vdydev_zone_has_point VALUES("275","6","40.347225","0.3899","26");
INSERT INTO vdydev_zone_has_point VALUES("276","6","40.327335","0.38372","27");
INSERT INTO vdydev_zone_has_point VALUES("277","6","40.327858","0.371361","28");
INSERT INTO vdydev_zone_has_point VALUES("278","6","40.306915","0.354881","29");
INSERT INTO vdydev_zone_has_point VALUES("279","6","40.293823","0.349388","30");
INSERT INTO vdydev_zone_has_point VALUES("280","6","40.273918","0.328789","31");
INSERT INTO vdydev_zone_has_point VALUES("281","6","40.259251","0.306129","32");
INSERT INTO vdydev_zone_has_point VALUES("282","6","40.246674","0.286217","33");
INSERT INTO vdydev_zone_has_point VALUES("283","6","40.236713","0.276604","34");
INSERT INTO vdydev_zone_has_point VALUES("284","6","40.217842","0.271797","35");
INSERT INTO vdydev_zone_has_point VALUES("285","6","40.197392","0.267677","36");
INSERT INTO vdydev_zone_has_point VALUES("286","6","40.255058","0.218925","37");
INSERT INTO vdydev_zone_has_point VALUES("287","6","40.274445","0.178413","38");
INSERT INTO vdydev_zone_has_point VALUES("288","6","40.322624","0.144081","39");
INSERT INTO vdydev_zone_has_point VALUES("289","6","40.335709","0.112495","40");
INSERT INTO vdydev_zone_has_point VALUES("290","6","40.384892","0.089836","41");
INSERT INTO vdydev_zone_has_point VALUES("291","6","40.397446","0.009499","42");
INSERT INTO vdydev_zone_has_point VALUES("292","6","40.450397","0.046463","43");
INSERT INTO vdydev_zone_has_point VALUES("293","6","40.530815","0.058823","44");
INSERT INTO vdydev_zone_has_point VALUES("294","6","40.563164","0.130234","45");
INSERT INTO vdydev_zone_has_point VALUES("295","6","40.591328","0.119247","46");
INSERT INTO vdydev_zone_has_point VALUES("296","6","40.612179","0.083542","47");
INSERT INTO vdydev_zone_has_point VALUES("297","6","40.646572","0.058823","48");
INSERT INTO vdydev_zone_has_point VALUES("298","6","40.670536","0.098648","49");
INSERT INTO vdydev_zone_has_point VALUES("299","6","40.718796","0.142708","50");
INSERT INTO vdydev_zone_has_point VALUES("300","7","40.012699","0.03736","0");
INSERT INTO vdydev_zone_has_point VALUES("301","7","40.016911","0.0329","1");
INSERT INTO vdydev_zone_has_point VALUES("302","7","40.016651","0.02672","2");
INSERT INTO vdydev_zone_has_point VALUES("303","7","40.020512","0.02059","3");
INSERT INTO vdydev_zone_has_point VALUES("304","7","40.022091","0.01785","4");
INSERT INTO vdydev_zone_has_point VALUES("305","7","40.02401","0.01333","5");
INSERT INTO vdydev_zone_has_point VALUES("306","7","40.027161","-0.00486","6");
INSERT INTO vdydev_zone_has_point VALUES("307","7","40.026958","-0.008926","7");
INSERT INTO vdydev_zone_has_point VALUES("308","7","40.028534","-0.01339","8");
INSERT INTO vdydev_zone_has_point VALUES("309","7","40.031631","-0.02031","9");
INSERT INTO vdydev_zone_has_point VALUES("310","7","40.038471","-0.0361","10");
INSERT INTO vdydev_zone_has_point VALUES("311","7","40.043255","-0.046692","11");
INSERT INTO vdydev_zone_has_point VALUES("312","7","40.03997","-0.061283","12");
INSERT INTO vdydev_zone_has_point VALUES("313","7","40.03558","-0.07558","13");
INSERT INTO vdydev_zone_has_point VALUES("314","7","40.034519","-0.09309","14");
INSERT INTO vdydev_zone_has_point VALUES("315","7","40.036098","-0.11678","15");
INSERT INTO vdydev_zone_has_point VALUES("316","7","40.037418","-0.1494","16");
INSERT INTO vdydev_zone_has_point VALUES("317","7","40.02927","-0.16519","17");
INSERT INTO vdydev_zone_has_point VALUES("318","7","40.021381","-0.18579","18");
INSERT INTO vdydev_zone_has_point VALUES("319","7","40.007439","-0.17412","19");
INSERT INTO vdydev_zone_has_point VALUES("320","7","39.99947","-0.14453","20");
INSERT INTO vdydev_zone_has_point VALUES("321","7","39.986141","-0.11404","21");
INSERT INTO vdydev_zone_has_point VALUES("322","7","39.985088","-0.09138","22");
INSERT INTO vdydev_zone_has_point VALUES("323","7","39.972198","-0.08451","23");
INSERT INTO vdydev_zone_has_point VALUES("324","7","39.96851","-0.06494","24");
INSERT INTO vdydev_zone_has_point VALUES("325","7","39.958778","-0.04228","25");
INSERT INTO vdydev_zone_has_point VALUES("326","7","39.94799","-0.01447","26");
INSERT INTO vdydev_zone_has_point VALUES("327","7","39.941929","0.00268","27");
INSERT INTO vdydev_zone_has_point VALUES("328","7","39.955879","0.00406","28");
INSERT INTO vdydev_zone_has_point VALUES("329","7","39.967991","0.00989","29");
INSERT INTO vdydev_zone_has_point VALUES("330","7","39.965618","0.02157","30");
INSERT INTO vdydev_zone_has_point VALUES("331","7","39.96904","0.03084","31");
INSERT INTO vdydev_zone_has_point VALUES("332","7","39.976669","0.02809","32");
INSERT INTO vdydev_zone_has_point VALUES("333","7","39.985882","0.025","33");
INSERT INTO vdydev_zone_has_point VALUES("334","7","39.99456","0.02843","34");
INSERT INTO vdydev_zone_has_point VALUES("335","7","40.001919","0.03187","35");
INSERT INTO vdydev_zone_has_point VALUES("336","7","40.012699","0.03736","36");
INSERT INTO vdydev_zone_has_point VALUES("337","8","39.803604","-0.632458","0");
INSERT INTO vdydev_zone_has_point VALUES("338","8","39.763763","-0.579243","1");
INSERT INTO vdydev_zone_has_point VALUES("339","8","39.775375","-0.569973","2");
INSERT INTO vdydev_zone_has_point VALUES("340","8","39.72496","-0.395222","3");
INSERT INTO vdydev_zone_has_point VALUES("341","8","39.682434","-0.418224","4");
INSERT INTO vdydev_zone_has_point VALUES("342","8","39.63195","-0.340633","5");
INSERT INTO vdydev_zone_has_point VALUES("343","8","39.565285","-0.385265","6");
INSERT INTO vdydev_zone_has_point VALUES("344","8","39.527508","-0.556297","7");
INSERT INTO vdydev_zone_has_point VALUES("345","8","39.58733","-0.68264","8");
INSERT INTO vdydev_zone_has_point VALUES("346","8","39.590343","-0.669136","9");
INSERT INTO vdydev_zone_has_point VALUES("347","8","39.626846","-0.655746","10");
INSERT INTO vdydev_zone_has_point VALUES("348","8","39.646568","-0.681267","11");
INSERT INTO vdydev_zone_has_point VALUES("349","8","39.680401","-0.745811","12");
INSERT INTO vdydev_zone_has_point VALUES("350","8","39.697678","-0.752907","13");
INSERT INTO vdydev_zone_has_point VALUES("351","8","39.72319","-0.746498","14");
INSERT INTO vdydev_zone_has_point VALUES("352","8","39.746426","-0.727272","15");
INSERT INTO vdydev_zone_has_point VALUES("353","8","39.766483","-0.68676","16");
INSERT INTO vdydev_zone_has_point VALUES("354","8","39.768597","-0.664787","17");
INSERT INTO vdydev_zone_has_point VALUES("355","8","39.78849","-0.63858","18");
INSERT INTO vdydev_zone_has_point VALUES("356","8","39.803604","-0.632458","19");
INSERT INTO vdydev_zone_has_point VALUES("357","9","39.803947","-0.633202","0");
INSERT INTO vdydev_zone_has_point VALUES("358","9","39.841389","-0.659294","1");
INSERT INTO vdydev_zone_has_point VALUES("359","9","39.863529","-0.684013","2");
INSERT INTO vdydev_zone_has_point VALUES("360","9","39.841915","-0.720406","3");
INSERT INTO vdydev_zone_has_point VALUES("361","9","39.883026","-0.79937","4");
INSERT INTO vdydev_zone_has_point VALUES("362","9","39.846661","-0.865974","5");
INSERT INTO vdydev_zone_has_point VALUES("363","9","39.868275","-0.910606","6");
INSERT INTO vdydev_zone_has_point VALUES("364","9","39.895145","-0.89962","7");
INSERT INTO vdydev_zone_has_point VALUES("365","9","39.919903","-0.914726","8");
INSERT INTO vdydev_zone_has_point VALUES("366","9","39.962017","-0.917473","9");
INSERT INTO vdydev_zone_has_point VALUES("367","9","39.9757","-0.974464","10");
INSERT INTO vdydev_zone_has_point VALUES("368","9","39.980961","-1.01017","11");
INSERT INTO vdydev_zone_has_point VALUES("369","9","39.979382","-1.071968","12");
INSERT INTO vdydev_zone_has_point VALUES("370","9","39.964123","-1.123467","13");
INSERT INTO vdydev_zone_has_point VALUES("371","9","39.972015","-1.142693","14");
INSERT INTO vdydev_zone_has_point VALUES("372","9","39.943069","-1.208611","15");
INSERT INTO vdydev_zone_has_point VALUES("373","9","39.912003","-1.196251","16");
INSERT INTO vdydev_zone_has_point VALUES("374","9","39.803417","-1.21891","17");
INSERT INTO vdydev_zone_has_point VALUES("375","9","39.774929","-1.205177","18");
INSERT INTO vdydev_zone_has_point VALUES("376","9","39.695194","-1.21685","19");
INSERT INTO vdydev_zone_has_point VALUES("377","9","39.641811","-1.190758","20");
INSERT INTO vdydev_zone_has_point VALUES("378","9","39.567219","-1.078835","21");
INSERT INTO vdydev_zone_has_point VALUES("379","9","39.623833","-0.964165","22");
INSERT INTO vdydev_zone_has_point VALUES("380","9","39.571453","-0.869408","23");
INSERT INTO vdydev_zone_has_point VALUES("381","9","39.5868","-0.68058","24");
INSERT INTO vdydev_zone_has_point VALUES("382","9","39.590424","-0.668163","25");
INSERT INTO vdydev_zone_has_point VALUES("383","9","39.626926","-0.65546","26");
INSERT INTO vdydev_zone_has_point VALUES("384","9","39.646492","-0.680866","27");
INSERT INTO vdydev_zone_has_point VALUES("385","9","39.680847","-0.745411","28");
INSERT INTO vdydev_zone_has_point VALUES("386","9","39.698814","-0.751247","29");
INSERT INTO vdydev_zone_has_point VALUES("387","9","39.722847","-0.746784","30");
INSERT INTO vdydev_zone_has_point VALUES("388","9","39.746609","-0.727558","31");
INSERT INTO vdydev_zone_has_point VALUES("389","9","39.766666","-0.686359","32");
INSERT INTO vdydev_zone_has_point VALUES("390","9","39.769043","-0.664387","33");
INSERT INTO vdydev_zone_has_point VALUES("391","9","39.787964","-0.636864","34");
INSERT INTO vdydev_zone_has_point VALUES("392","9","39.803947","-0.633202","35");
INSERT INTO vdydev_zone_has_point VALUES("393","10","39.804634","-1.219711","0");
INSERT INTO vdydev_zone_has_point VALUES("394","10","39.741302","-1.280136","1");
INSERT INTO vdydev_zone_has_point VALUES("395","10","39.697994","-1.26915","2");
INSERT INTO vdydev_zone_has_point VALUES("396","10","39.668404","-1.313095","3");
INSERT INTO vdydev_zone_has_point VALUES("397","10","39.689541","-1.370773","4");
INSERT INTO vdydev_zone_has_point VALUES("398","10","39.55732","-1.503983","5");
INSERT INTO vdydev_zone_has_point VALUES("399","10","39.469383","-1.509476","6");
INSERT INTO vdydev_zone_has_point VALUES("400","10","39.450298","-1.532822","7");
INSERT INTO vdydev_zone_has_point VALUES("401","10","39.362228","-1.450424","8");
INSERT INTO vdydev_zone_has_point VALUES("402","10","39.378151","-1.413345","9");
INSERT INTO vdydev_zone_has_point VALUES("403","10","39.338863","-1.344681","10");
INSERT INTO vdydev_zone_has_point VALUES("404","10","39.30912","-1.163406","11");
INSERT INTO vdydev_zone_has_point VALUES("405","10","39.378525","-1.031456","12");
INSERT INTO vdydev_zone_has_point VALUES("406","10","39.550278","-0.957985","13");
INSERT INTO vdydev_zone_has_point VALUES("407","10","39.593147","-0.908546","14");
INSERT INTO vdydev_zone_has_point VALUES("408","10","39.624359","-0.964851","15");
INSERT INTO vdydev_zone_has_point VALUES("409","10","39.567219","-1.080208","16");
INSERT INTO vdydev_zone_has_point VALUES("410","10","39.642342","-1.192131","17");
INSERT INTO vdydev_zone_has_point VALUES("411","10","39.695724","-1.217537","18");
INSERT INTO vdydev_zone_has_point VALUES("412","10","39.775982","-1.204491","19");
INSERT INTO vdydev_zone_has_point VALUES("413","10","39.804634","-1.219711","20");
INSERT INTO vdydev_zone_has_point VALUES("414","11","39.378708","-1.031399","0");
INSERT INTO vdydev_zone_has_point VALUES("415","11","39.369949","-1.000843","1");
INSERT INTO vdydev_zone_has_point VALUES("416","11","39.3596","-0.968571","2");
INSERT INTO vdydev_zone_has_point VALUES("417","11","39.348717","-0.934925","3");
INSERT INTO vdydev_zone_has_point VALUES("418","11","39.341812","-0.801029","4");
INSERT INTO vdydev_zone_has_point VALUES("419","11","39.321709","-0.694313","5");
INSERT INTO vdydev_zone_has_point VALUES("420","11","39.355698","-0.57827","6");
INSERT INTO vdydev_zone_has_point VALUES("421","11","39.388607","-0.632515","7");
INSERT INTO vdydev_zone_has_point VALUES("422","11","39.434761","-0.545311","8");
INSERT INTO vdydev_zone_has_point VALUES("423","11","39.528038","-0.556297","9");
INSERT INTO vdydev_zone_has_point VALUES("424","11","39.58733","-0.681953","10");
INSERT INTO vdydev_zone_has_point VALUES("425","11","39.57198","-0.869408","11");
INSERT INTO vdydev_zone_has_point VALUES("426","11","39.593678","-0.909233","12");
INSERT INTO vdydev_zone_has_point VALUES("427","11","39.550278","-0.958672","13");
INSERT INTO vdydev_zone_has_point VALUES("428","11","39.378708","-1.031399","14");
INSERT INTO vdydev_zone_has_point VALUES("429","12","39.31002","-1.163979","0");
INSERT INTO vdydev_zone_has_point VALUES("430","12","39.269634","-1.178398","1");
INSERT INTO vdydev_zone_has_point VALUES("431","12","39.199963","-1.188698","2");
INSERT INTO vdydev_zone_has_point VALUES("432","12","39.14513","-1.23333","3");
INSERT INTO vdydev_zone_has_point VALUES("433","12","39.071606","-1.266289","4");
INSERT INTO vdydev_zone_has_point VALUES("434","12","39.042278","-1.266289","5");
INSERT INTO vdydev_zone_has_point VALUES("435","12","38.943016","-1.154366","6");
INSERT INTO vdydev_zone_has_point VALUES("436","12","38.928059","-1.141319","7");
INSERT INTO vdydev_zone_has_point VALUES("437","12","38.943016","-0.960045","8");
INSERT INTO vdydev_zone_has_point VALUES("438","12","39.003338","-0.892754","9");
INSERT INTO vdydev_zone_has_point VALUES("439","12","39.210602","-0.929146","10");
INSERT INTO vdydev_zone_has_point VALUES("440","12","39.227093","-0.942192","11");
INSERT INTO vdydev_zone_has_point VALUES("441","12","39.344547","-0.854988","12");
INSERT INTO vdydev_zone_has_point VALUES("442","12","39.348797","-0.934639","13");
INSERT INTO vdydev_zone_has_point VALUES("443","12","39.378525","-1.030769","14");
INSERT INTO vdydev_zone_has_point VALUES("444","12","39.31002","-1.163979","15");
INSERT INTO vdydev_zone_has_point VALUES("445","13","38.94347","-0.959988","0");
INSERT INTO vdydev_zone_has_point VALUES("446","13","38.9189","-0.955181","1");
INSERT INTO vdydev_zone_has_point VALUES("447","13","38.890846","-0.925312","2");
INSERT INTO vdydev_zone_has_point VALUES("448","13","38.927364","-0.785522","3");
INSERT INTO vdydev_zone_has_point VALUES("449","13","38.947662","-0.718231","4");
INSERT INTO vdydev_zone_has_point VALUES("450","13","38.973289","-0.660553","5");
INSERT INTO vdydev_zone_has_point VALUES("451","13","38.998909","-0.608368","6");
INSERT INTO vdydev_zone_has_point VALUES("452","13","39.03001","-0.559731","7");
INSERT INTO vdydev_zone_has_point VALUES("453","13","39.135014","-0.638008","8");
INSERT INTO vdydev_zone_has_point VALUES("454","13","39.224964","-0.705299","9");
INSERT INTO vdydev_zone_has_point VALUES("455","13","39.321709","-0.693626","10");
INSERT INTO vdydev_zone_has_point VALUES("456","13","39.334988","-0.767784","11");
INSERT INTO vdydev_zone_has_point VALUES("457","13","39.342422","-0.800743","12");
INSERT INTO vdydev_zone_has_point VALUES("458","13","39.344547","-0.855675","13");
INSERT INTO vdydev_zone_has_point VALUES("459","13","39.257938","-0.920219","14");
INSERT INTO vdydev_zone_has_point VALUES("460","13","39.226562","-0.944252","15");
INSERT INTO vdydev_zone_has_point VALUES("461","13","39.208473","-0.928459","16");
INSERT INTO vdydev_zone_has_point VALUES("462","13","39.003872","-0.89344","17");
INSERT INTO vdydev_zone_has_point VALUES("463","13","38.94347","-0.959988","18");
INSERT INTO vdydev_zone_has_point VALUES("464","14","39.031078","-0.561104","0");
INSERT INTO vdydev_zone_has_point VALUES("465","14","39.05241","-0.405235","1");
INSERT INTO vdydev_zone_has_point VALUES("466","14","39.094524","-0.312538","2");
INSERT INTO vdydev_zone_has_point VALUES("467","14","39.186764","-0.412331","3");
INSERT INTO vdydev_zone_has_point VALUES("468","14","39.260597","-0.399742","4");
INSERT INTO vdydev_zone_has_point VALUES("469","14","39.302052","-0.401115","5");
INSERT INTO vdydev_zone_has_point VALUES("470","14","39.31374","-0.436821","6");
INSERT INTO vdydev_zone_has_point VALUES("471","14","39.319054","-0.519218","7");
INSERT INTO vdydev_zone_has_point VALUES("472","14","39.368439","-0.558357","8");
INSERT INTO vdydev_zone_has_point VALUES("473","14","39.356228","-0.579643","9");
INSERT INTO vdydev_zone_has_point VALUES("474","14","39.321896","-0.694256","10");
INSERT INTO vdydev_zone_has_point VALUES("475","14","39.224354","-0.705585","11");
INSERT INTO vdydev_zone_has_point VALUES("476","14","39.101372","-0.612545","12");
INSERT INTO vdydev_zone_has_point VALUES("477","14","39.031078","-0.561104","13");
INSERT INTO vdydev_zone_has_point VALUES("478","15","38.892422","-0.925827","0");
INSERT INTO vdydev_zone_has_point VALUES("479","15","38.780102","-0.93544","1");
INSERT INTO vdydev_zone_has_point VALUES("480","15","38.770466","-0.958786","2");
INSERT INTO vdydev_zone_has_point VALUES("481","15","38.726555","-0.936813","3");
INSERT INTO vdydev_zone_has_point VALUES("482","15","38.730839","-0.836563","4");
INSERT INTO vdydev_zone_has_point VALUES("483","15","38.946915","-0.39711","5");
INSERT INTO vdydev_zone_has_point VALUES("484","15","38.983219","-0.391617","6");
INSERT INTO vdydev_zone_has_point VALUES("485","15","39.054703","-0.403976","7");
INSERT INTO vdydev_zone_has_point VALUES("486","15","39.032307","-0.559158","8");
INSERT INTO vdydev_zone_has_point VALUES("487","15","38.991436","-0.624847","9");
INSERT INTO vdydev_zone_has_point VALUES("488","15","38.952999","-0.705872","10");
INSERT INTO vdydev_zone_has_point VALUES("489","15","38.892422","-0.925827","11");
INSERT INTO vdydev_zone_has_point VALUES("490","16","39.632214","-0.340977","0");
INSERT INTO vdydev_zone_has_point VALUES("491","16","39.646225","-0.316601","1");
INSERT INTO vdydev_zone_has_point VALUES("492","16","39.636974","-0.293255","2");
INSERT INTO vdydev_zone_has_point VALUES("493","16","39.614761","-0.258923","3");
INSERT INTO vdydev_zone_has_point VALUES("494","16","39.636627","-0.236206","4");
INSERT INTO vdydev_zone_has_point VALUES("495","16","39.647549","-0.213947","5");
INSERT INTO vdydev_zone_has_point VALUES("496","16","39.672394","-0.203648","6");
INSERT INTO vdydev_zone_has_point VALUES("497","16","39.696438","-0.199184","7");
INSERT INTO vdydev_zone_has_point VALUES("498","16","39.73209","-0.183735","8");
INSERT INTO vdydev_zone_has_point VALUES("499","16","39.781181","-0.3475","9");
INSERT INTO vdydev_zone_has_point VALUES("500","16","39.72496","-0.395565","10");
INSERT INTO vdydev_zone_has_point VALUES("501","16","39.682171","-0.418568","11");
INSERT INTO vdydev_zone_has_point VALUES("502","16","39.632214","-0.340977","12");
INSERT INTO vdydev_zone_has_point VALUES("503","17","39.546543","-0.470181","0");
INSERT INTO vdydev_zone_has_point VALUES("504","17","39.529015","-0.445347","1");
INSERT INTO vdydev_zone_has_point VALUES("505","17","39.508358","-0.423374","2");
INSERT INTO vdydev_zone_has_point VALUES("506","17","39.497856","-0.403948","3");
INSERT INTO vdydev_zone_has_point VALUES("507","17","39.502357","-0.396051","4");
INSERT INTO vdydev_zone_has_point VALUES("508","17","39.525536","-0.396566","5");
INSERT INTO vdydev_zone_has_point VALUES("509","17","39.525799","-0.374937","6");
INSERT INTO vdydev_zone_has_point VALUES("510","17","39.499046","-0.371161","7");
INSERT INTO vdydev_zone_has_point VALUES("511","17","39.493351","-0.351248","8");
INSERT INTO vdydev_zone_has_point VALUES("512","17","39.486729","-0.345755","9");
INSERT INTO vdydev_zone_has_point VALUES("513","17","39.480633","-0.332537","10");
INSERT INTO vdydev_zone_has_point VALUES("514","17","39.478512","-0.322409","11");
INSERT INTO vdydev_zone_has_point VALUES("515","17","39.501564","-0.323439","12");
INSERT INTO vdydev_zone_has_point VALUES("516","17","39.519974","-0.31743","13");
INSERT INTO vdydev_zone_has_point VALUES("517","17","39.54129","-0.302839","14");
INSERT INTO vdydev_zone_has_point VALUES("518","17","39.557304","-0.28842","15");
INSERT INTO vdydev_zone_has_point VALUES("519","17","39.562202","-0.284128","16");
INSERT INTO vdydev_zone_has_point VALUES("520","17","39.56842","-0.278978","17");
INSERT INTO vdydev_zone_has_point VALUES("521","17","39.575699","-0.274515","18");
INSERT INTO vdydev_zone_has_point VALUES("522","17","39.591309","-0.270395","19");
INSERT INTO vdydev_zone_has_point VALUES("523","17","39.607052","-0.265417","20");
INSERT INTO vdydev_zone_has_point VALUES("524","17","39.614986","-0.258894","21");
INSERT INTO vdydev_zone_has_point VALUES("525","17","39.629002","-0.280695","22");
INSERT INTO vdydev_zone_has_point VALUES("526","17","39.637463","-0.2946","23");
INSERT INTO vdydev_zone_has_point VALUES("527","17","39.646187","-0.316572","24");
INSERT INTO vdydev_zone_has_point VALUES("528","17","39.632439","-0.341291","25");
INSERT INTO vdydev_zone_has_point VALUES("529","17","39.565376","-0.385408","26");
INSERT INTO vdydev_zone_has_point VALUES("530","17","39.546543","-0.470181","27");
INSERT INTO vdydev_zone_has_point VALUES("531","18","39.368092","-0.558643","0");
INSERT INTO vdydev_zone_has_point VALUES("532","18","39.401264","-0.514698","1");
INSERT INTO vdydev_zone_has_point VALUES("533","18","39.404446","-0.476933","2");
INSERT INTO vdydev_zone_has_point VALUES("534","18","39.415058","-0.440197","3");
INSERT INTO vdydev_zone_has_point VALUES("535","18","39.429111","-0.426464","4");
INSERT INTO vdydev_zone_has_point VALUES("536","18","39.436008","-0.422344","5");
INSERT INTO vdydev_zone_has_point VALUES("537","18","39.447674","-0.417881","6");
INSERT INTO vdydev_zone_has_point VALUES("538","18","39.45536","-0.410671","7");
INSERT INTO vdydev_zone_has_point VALUES("539","18","39.469105","-0.419569","8");
INSERT INTO vdydev_zone_has_point VALUES("540","18","39.470165","-0.406008","9");
INSERT INTO vdydev_zone_has_point VALUES("541","18","39.472286","-0.405664","10");
INSERT INTO vdydev_zone_has_point VALUES("542","18","39.474274","-0.413218","11");
INSERT INTO vdydev_zone_has_point VALUES("543","18","39.476925","-0.412016","12");
INSERT INTO vdydev_zone_has_point VALUES("544","18","39.482754","-0.410299","13");
INSERT INTO vdydev_zone_has_point VALUES("545","18","39.496662","-0.412016","14");
INSERT INTO vdydev_zone_has_point VALUES("546","18","39.498119","-0.403605","15");
INSERT INTO vdydev_zone_has_point VALUES("547","18","39.509682","-0.424404","16");
INSERT INTO vdydev_zone_has_point VALUES("548","18","39.528751","-0.445004","17");
INSERT INTO vdydev_zone_has_point VALUES("549","18","39.546757","-0.469379","18");
INSERT INTO vdydev_zone_has_point VALUES("550","18","39.528038","-0.555611","19");
INSERT INTO vdydev_zone_has_point VALUES("551","18","39.434231","-0.545998","20");
INSERT INTO vdydev_zone_has_point VALUES("552","18","39.389137","-0.633202","21");
INSERT INTO vdydev_zone_has_point VALUES("553","18","39.356228","-0.58033","22");
INSERT INTO vdydev_zone_has_point VALUES("554","18","39.368092","-0.558643","23");
INSERT INTO vdydev_zone_has_point VALUES("555","19","39.444492","-0.418568","0");
INSERT INTO vdydev_zone_has_point VALUES("556","19","39.43203","-0.400715","1");
INSERT INTO vdydev_zone_has_point VALUES("557","19","39.428051","-0.384579","2");
INSERT INTO vdydev_zone_has_point VALUES("558","19","39.422749","-0.373592","3");
INSERT INTO vdydev_zone_has_point VALUES("559","19","39.417709","-0.360203","4");
INSERT INTO vdydev_zone_has_point VALUES("560","19","39.35854","-0.32793","5");
INSERT INTO vdydev_zone_has_point VALUES("561","19","39.354027","-0.345783","6");
INSERT INTO vdydev_zone_has_point VALUES("562","19","39.355885","-0.366383","7");
INSERT INTO vdydev_zone_has_point VALUES("563","19","39.346325","-0.381489","8");
INSERT INTO vdydev_zone_has_point VALUES("564","19","39.328533","-0.390759","9");
INSERT INTO vdydev_zone_has_point VALUES("565","19","39.316051","-0.388699","10");
INSERT INTO vdydev_zone_has_point VALUES("566","19","39.309147","-0.403805","11");
INSERT INTO vdydev_zone_has_point VALUES("567","19","39.301708","-0.401058","12");
INSERT INTO vdydev_zone_has_point VALUES("568","19","39.314194","-0.437107","13");
INSERT INTO vdydev_zone_has_point VALUES("569","19","39.319241","-0.519161","14");
INSERT INTO vdydev_zone_has_point VALUES("570","19","39.369156","-0.558643","15");
INSERT INTO vdydev_zone_has_point VALUES("571","19","39.401527","-0.514011","16");
INSERT INTO vdydev_zone_has_point VALUES("572","19","39.404446","-0.475559","17");
INSERT INTO vdydev_zone_has_point VALUES("573","19","39.416119","-0.43951","18");
INSERT INTO vdydev_zone_has_point VALUES("574","19","39.433357","-0.423718","19");
INSERT INTO vdydev_zone_has_point VALUES("575","19","39.444492","-0.418568","20");
INSERT INTO vdydev_zone_has_point VALUES("576","20","39.09399","-0.311165","0");
INSERT INTO vdydev_zone_has_point VALUES("577","20","39.106781","-0.222588","1");
INSERT INTO vdydev_zone_has_point VALUES("578","20","39.141937","-0.237694","2");
INSERT INTO vdydev_zone_has_point VALUES("579","20","39.166962","-0.2425","3");
INSERT INTO vdydev_zone_has_point VALUES("580","20","39.180271","-0.237694","4");
INSERT INTO vdydev_zone_has_point VALUES("581","20","39.185593","-0.217094","5");
INSERT INTO vdydev_zone_has_point VALUES("582","20","39.225498","-0.24456","6");
INSERT INTO vdydev_zone_has_point VALUES("583","20","39.280266","-0.276146","7");
INSERT INTO vdydev_zone_has_point VALUES("584","20","39.316929","-0.297432","8");
INSERT INTO vdydev_zone_has_point VALUES("585","20","39.305241","-0.321465","9");
INSERT INTO vdydev_zone_has_point VALUES("586","20","39.315865","-0.329018","10");
INSERT INTO vdydev_zone_has_point VALUES("587","20","39.359943","-0.329018","11");
INSERT INTO vdydev_zone_has_point VALUES("588","20","39.354633","-0.345497","12");
INSERT INTO vdydev_zone_has_point VALUES("589","20","39.356758","-0.366783","13");
INSERT INTO vdydev_zone_has_point VALUES("590","20","39.343487","-0.385323","14");
INSERT INTO vdydev_zone_has_point VALUES("591","20","39.324898","-0.393562","15");
INSERT INTO vdydev_zone_has_point VALUES("592","20","39.315865","-0.388069","16");
INSERT INTO vdydev_zone_has_point VALUES("593","20","39.31002","-0.403862","17");
INSERT INTO vdydev_zone_has_point VALUES("594","20","39.25528","-0.401115","18");
INSERT INTO vdydev_zone_has_point VALUES("595","20","39.187187","-0.413475","19");
INSERT INTO vdydev_zone_has_point VALUES("596","20","39.131817","-0.353737","20");
INSERT INTO vdydev_zone_has_point VALUES("597","20","39.09399","-0.311165","21");
INSERT INTO vdydev_zone_has_point VALUES("598","21","38.974678","-0.391617","0");
INSERT INTO vdydev_zone_has_point VALUES("599","21","38.952255","-0.29686","1");
INSERT INTO vdydev_zone_has_point VALUES("600","21","38.899902","-0.240555","2");
INSERT INTO vdydev_zone_has_point VALUES("601","21","38.832542","-0.331192","3");
INSERT INTO vdydev_zone_has_point VALUES("602","21","38.802582","-0.214462","4");
INSERT INTO vdydev_zone_has_point VALUES("603","21","38.855003","-0.154038","5");
INSERT INTO vdydev_zone_has_point VALUES("604","21","38.875317","-0.015335","6");
INSERT INTO vdydev_zone_has_point VALUES("605","21","38.927685","-0.090866","7");
INSERT INTO vdydev_zone_has_point VALUES("606","21","38.998161","-0.159531","8");
INSERT INTO vdydev_zone_has_point VALUES("607","21","39.108006","-0.226822","9");
INSERT INTO vdydev_zone_has_point VALUES("608","21","39.096283","-0.311966","10");
INSERT INTO vdydev_zone_has_point VALUES("609","21","39.05257","-0.403976","11");
INSERT INTO vdydev_zone_has_point VALUES("610","21","38.974678","-0.391617","12");
INSERT INTO vdydev_zone_has_point VALUES("611","22","38.831844","-0.329704","0");
INSERT INTO vdydev_zone_has_point VALUES("612","22","38.83559","-0.398369","1");
INSERT INTO vdydev_zone_has_point VALUES("613","22","38.814194","-0.456734","2");
INSERT INTO vdydev_zone_has_point VALUES("614","22","38.787971","-0.479393","3");
INSERT INTO vdydev_zone_has_point VALUES("615","22","38.746212","-0.519905","4");
INSERT INTO vdydev_zone_has_point VALUES("616","22","38.716213","-0.607109","5");
INSERT INTO vdydev_zone_has_point VALUES("617","22","38.664764","-0.751991","6");
INSERT INTO vdydev_zone_has_point VALUES("618","22","38.700676","-0.776024","7");
INSERT INTO vdydev_zone_has_point VALUES("619","22","38.731216","-0.837822","8");
INSERT INTO vdydev_zone_has_point VALUES("620","22","38.812054","-0.675774","9");
INSERT INTO vdydev_zone_has_point VALUES("621","22","38.884247","-0.527458","10");
INSERT INTO vdydev_zone_has_point VALUES("622","22","38.950493","-0.396996","11");
INSERT INTO vdydev_zone_has_point VALUES("623","22","38.975052","-0.391502","12");
INSERT INTO vdydev_zone_has_point VALUES("624","22","38.951561","-0.293999","13");
INSERT INTO vdydev_zone_has_point VALUES("625","22","38.899208","-0.24044","14");
INSERT INTO vdydev_zone_has_point VALUES("626","22","38.831844","-0.329704","15");
INSERT INTO vdydev_zone_has_point VALUES("627","23","39.316849","-0.296001","0");
INSERT INTO vdydev_zone_has_point VALUES("628","23","39.363049","-0.317287","1");
INSERT INTO vdydev_zone_has_point VALUES("629","23","39.391975","-0.328274","2");
INSERT INTO vdydev_zone_has_point VALUES("630","23","39.420097","-0.33308","3");
INSERT INTO vdydev_zone_has_point VALUES("631","23","39.42593","-0.332737","4");
INSERT INTO vdydev_zone_has_point VALUES("632","23","39.420547","-0.316544","5");
INSERT INTO vdydev_zone_has_point VALUES("633","23","39.436539","-0.302181","6");
INSERT INTO vdydev_zone_has_point VALUES("634","23","39.450855","-0.303555","7");
INSERT INTO vdydev_zone_has_point VALUES("635","23","39.452709","-0.313168","8");
INSERT INTO vdydev_zone_has_point VALUES("636","23","39.459869","-0.317631","9");
INSERT INTO vdydev_zone_has_point VALUES("637","23","39.470203","-0.322437","10");
INSERT INTO vdydev_zone_has_point VALUES("638","23","39.478951","-0.322094","11");
INSERT INTO vdydev_zone_has_point VALUES("639","23","39.481335","-0.333767","12");
INSERT INTO vdydev_zone_has_point VALUES("640","23","39.48637","-0.344753","13");
INSERT INTO vdydev_zone_has_point VALUES("641","23","39.49379","-0.35162","14");
INSERT INTO vdydev_zone_has_point VALUES("642","23","39.498821","-0.371532","15");
INSERT INTO vdydev_zone_has_point VALUES("643","23","39.525574","-0.374966","16");
INSERT INTO vdydev_zone_has_point VALUES("644","23","39.525574","-0.396938","17");
INSERT INTO vdydev_zone_has_point VALUES("645","23","39.502796","-0.396595","18");
INSERT INTO vdydev_zone_has_point VALUES("646","23","39.497231","-0.411701","19");
INSERT INTO vdydev_zone_has_point VALUES("647","23","39.482395","-0.411358","20");
INSERT INTO vdydev_zone_has_point VALUES("648","23","39.474976","-0.413761","21");
INSERT INTO vdydev_zone_has_point VALUES("649","23","39.472324","-0.404835","22");
INSERT INTO vdydev_zone_has_point VALUES("650","23","39.46888","-0.405521","23");
INSERT INTO vdydev_zone_has_point VALUES("651","23","39.46941","-0.420628","24");
INSERT INTO vdydev_zone_has_point VALUES("652","23","39.454567","-0.410671","25");
INSERT INTO vdydev_zone_has_point VALUES("653","23","39.447407","-0.418568","26");
INSERT INTO vdydev_zone_has_point VALUES("654","23","39.444225","-0.419254","27");
INSERT INTO vdydev_zone_has_point VALUES("655","23","39.431767","-0.400028","28");
INSERT INTO vdydev_zone_has_point VALUES("656","23","39.428848","-0.387325","29");
INSERT INTO vdydev_zone_has_point VALUES("657","23","39.417442","-0.360203","30");
INSERT INTO vdydev_zone_has_point VALUES("658","23","39.358803","-0.327587","31");
INSERT INTO vdydev_zone_has_point VALUES("659","23","39.316051","-0.329647","32");
INSERT INTO vdydev_zone_has_point VALUES("660","23","39.30463","-0.321064","33");
INSERT INTO vdydev_zone_has_point VALUES("661","23","39.316849","-0.296001","34");
INSERT INTO vdydev_zone_has_point VALUES("662","24","40.201111","-1.2973","0");
INSERT INTO vdydev_zone_has_point VALUES("663","24","40.206146","-1.288147","1");
INSERT INTO vdydev_zone_has_point VALUES("664","24","40.21244","-1.303253","2");
INSERT INTO vdydev_zone_has_point VALUES("665","24","40.198807","-1.318359","3");
INSERT INTO vdydev_zone_has_point VALUES("666","24","40.177822","-1.329346","4");
INSERT INTO vdydev_zone_has_point VALUES("667","24","40.156837","-1.315613","5");
INSERT INTO vdydev_zone_has_point VALUES("668","24","40.133949","-1.36322","6");
INSERT INTO vdydev_zone_has_point VALUES("669","24","40.152637","-1.448822","7");
INSERT INTO vdydev_zone_has_point VALUES("670","24","40.122192","-1.458435","8");
INSERT INTO vdydev_zone_has_point VALUES("671","24","40.101185","-1.421356","9");
INSERT INTO vdydev_zone_has_point VALUES("672","24","40.070717","-1.404877","10");
INSERT INTO vdydev_zone_has_point VALUES("673","24","40.017097","-1.373291","11");
INSERT INTO vdydev_zone_has_point VALUES("674","24","40.01289","-1.307373","12");
INSERT INTO vdydev_zone_has_point VALUES("675","24","39.99942","-1.23687","13");
INSERT INTO vdydev_zone_has_point VALUES("676","24","40.016251","-1.12152","14");
INSERT INTO vdydev_zone_has_point VALUES("677","24","40.033924","-1.09314","15");
INSERT INTO vdydev_zone_has_point VALUES("678","24","40.04549","-1.079407","16");
INSERT INTO vdydev_zone_has_point VALUES("679","24","40.062309","-1.075287","17");
INSERT INTO vdydev_zone_has_point VALUES("680","24","40.087528","-1.104126","18");
INSERT INTO vdydev_zone_has_point VALUES("681","24","40.112942","-1.14898","19");
INSERT INTO vdydev_zone_has_point VALUES("682","24","40.121349","-1.24237","20");
INSERT INTO vdydev_zone_has_point VALUES("683","24","40.201111","-1.2973","21");
INSERT INTO vdydev_zone_has_point VALUES("684","25","38.625614","-0.017967","0");
INSERT INTO vdydev_zone_has_point VALUES("685","25","38.579468","-0.062599","1");
INSERT INTO vdydev_zone_has_point VALUES("686","25","38.563362","-0.04818","2");
INSERT INTO vdydev_zone_has_point VALUES("687","25","38.543331","-0.069351","3");
INSERT INTO vdydev_zone_has_point VALUES("688","25","38.524158","-0.096931","4");
INSERT INTO vdydev_zone_has_point VALUES("689","25","38.530067","-0.138817","5");
INSERT INTO vdydev_zone_has_point VALUES("690","25","38.500893","-0.22316","6");
INSERT INTO vdydev_zone_has_point VALUES("691","25","38.474178","-0.311852","7");
INSERT INTO vdydev_zone_has_point VALUES("692","25","38.557991","-0.438881","8");
INSERT INTO vdydev_zone_has_point VALUES("693","25","38.592884","-0.338631","9");
INSERT INTO vdydev_zone_has_point VALUES("694","25","38.639561","-0.322838","10");
INSERT INTO vdydev_zone_has_point VALUES("695","25","38.640099","-0.257607","11");
INSERT INTO vdydev_zone_has_point VALUES("696","25","38.668396","-0.22439","12");
INSERT INTO vdydev_zone_has_point VALUES("697","25","38.69891","-0.224533","13");
INSERT INTO vdydev_zone_has_point VALUES("698","25","38.745674","-0.172462","14");
INSERT INTO vdydev_zone_has_point VALUES("699","25","38.74139","-0.104485","15");
INSERT INTO vdydev_zone_has_point VALUES("700","25","38.686745","-0.090752","16");
INSERT INTO vdydev_zone_has_point VALUES("701","25","38.625614","-0.017967","17");
INSERT INTO vdydev_zone_has_point VALUES("702","26","38.557991","-0.438881","0");
INSERT INTO vdydev_zone_has_point VALUES("703","26","38.581615","-0.491066","1");
INSERT INTO vdydev_zone_has_point VALUES("704","26","38.628674","-0.52597","2");
INSERT INTO vdydev_zone_has_point VALUES("705","26","38.631889","-0.500565","3");
INSERT INTO vdydev_zone_has_point VALUES("706","26","38.631516","-0.472527","4");
INSERT INTO vdydev_zone_has_point VALUES("707","26","38.644386","-0.401802","5");
INSERT INTO vdydev_zone_has_point VALUES("708","26","38.660851","-0.383835","6");
INSERT INTO vdydev_zone_has_point VALUES("709","26","38.667282","-0.382462","7");
INSERT INTO vdydev_zone_has_point VALUES("710","26","38.677631","-0.407295","8");
INSERT INTO vdydev_zone_has_point VALUES("711","26","38.705498","-0.425835","9");
INSERT INTO vdydev_zone_has_point VALUES("712","26","38.722645","-0.450554","10");
INSERT INTO vdydev_zone_has_point VALUES("713","26","38.746746","-0.520592","11");
INSERT INTO vdydev_zone_has_point VALUES("714","26","38.814728","-0.45742","12");
INSERT INTO vdydev_zone_has_point VALUES("715","26","38.83559","-0.399055","13");
INSERT INTO vdydev_zone_has_point VALUES("716","26","38.832378","-0.329704","14");
INSERT INTO vdydev_zone_has_point VALUES("717","26","38.802422","-0.212975","15");
INSERT INTO vdydev_zone_has_point VALUES("718","26","38.74728","-0.207481","16");
INSERT INTO vdydev_zone_has_point VALUES("719","26","38.745674","-0.171776","17");
INSERT INTO vdydev_zone_has_point VALUES("720","26","38.697998","-0.223961","18");
INSERT INTO vdydev_zone_has_point VALUES("721","26","38.670124","-0.223274","19");
INSERT INTO vdydev_zone_has_point VALUES("722","26","38.640099","-0.257607","20");
INSERT INTO vdydev_zone_has_point VALUES("723","26","38.640099","-0.321465","21");
INSERT INTO vdydev_zone_has_point VALUES("724","26","38.593422","-0.339317","22");
INSERT INTO vdydev_zone_has_point VALUES("725","26","38.557991","-0.438881","23");
INSERT INTO vdydev_zone_has_point VALUES("726","27","38.664764","-0.751305","0");
INSERT INTO vdydev_zone_has_point VALUES("727","27","38.611668","-0.70118","1");
INSERT INTO vdydev_zone_has_point VALUES("728","27","38.573563","-0.736198","2");
INSERT INTO vdydev_zone_has_point VALUES("729","27","38.490845","-0.714226","3");
INSERT INTO vdydev_zone_has_point VALUES("730","27","38.48278","-0.608482","4");
INSERT INTO vdydev_zone_has_point VALUES("731","27","38.527916","-0.552177","5");
INSERT INTO vdydev_zone_has_point VALUES("732","27","38.626152","-0.528145","6");
INSERT INTO vdydev_zone_has_point VALUES("733","27","38.630978","-0.509605","7");
INSERT INTO vdydev_zone_has_point VALUES("734","27","38.630978","-0.46566","8");
INSERT INTO vdydev_zone_has_point VALUES("735","27","38.644924","-0.401115","9");
INSERT INTO vdydev_zone_has_point VALUES("736","27","38.653881","-0.387268","10");
INSERT INTO vdydev_zone_has_point VALUES("737","27","38.658169","-0.386581","11");
INSERT INTO vdydev_zone_has_point VALUES("738","27","38.662621","-0.380516","12");
INSERT INTO vdydev_zone_has_point VALUES("739","27","38.667446","-0.381203","13");
INSERT INTO vdydev_zone_has_point VALUES("740","27","38.679077","-0.405807","14");
INSERT INTO vdydev_zone_has_point VALUES("741","27","38.706036","-0.427208","15");
INSERT INTO vdydev_zone_has_point VALUES("742","27","38.716057","-0.437393","16");
INSERT INTO vdydev_zone_has_point VALUES("743","27","38.723717","-0.4533","17");
INSERT INTO vdydev_zone_has_point VALUES("744","27","38.733356","-0.48214","18");
INSERT INTO vdydev_zone_has_point VALUES("745","27","38.747124","-0.521164","19");
INSERT INTO vdydev_zone_has_point VALUES("746","27","38.739624","-0.53833","20");
INSERT INTO vdydev_zone_has_point VALUES("747","27","38.733196","-0.561676","21");
INSERT INTO vdydev_zone_has_point VALUES("748","27","38.716751","-0.605049","22");
INSERT INTO vdydev_zone_has_point VALUES("749","27","38.687813","-0.68882","23");
INSERT INTO vdydev_zone_has_point VALUES("750","27","38.664764","-0.751305","24");
INSERT INTO vdydev_zone_has_point VALUES("751","28","38.727921","-0.899906","0");
INSERT INTO vdydev_zone_has_point VALUES("752","28","38.702473","-0.911236","1");
INSERT INTO vdydev_zone_has_point VALUES("753","28","38.694164","-0.918102","2");
INSERT INTO vdydev_zone_has_point VALUES("754","28","38.681301","-0.928745","3");
INSERT INTO vdydev_zone_has_point VALUES("755","28","38.672726","-0.939388","4");
INSERT INTO vdydev_zone_has_point VALUES("756","28","38.663612","-0.953808","5");
INSERT INTO vdydev_zone_has_point VALUES("757","28","38.654041","-0.973778","6");
INSERT INTO vdydev_zone_has_point VALUES("758","28","38.657257","-1.028023","7");
INSERT INTO vdydev_zone_has_point VALUES("759","28","38.5741","-1.00193","8");
INSERT INTO vdydev_zone_has_point VALUES("760","28","38.52507","-1.025162","9");
INSERT INTO vdydev_zone_has_point VALUES("761","28","38.491543","-1.015091","10");
INSERT INTO vdydev_zone_has_point VALUES("762","28","38.467194","-0.89138","11");
INSERT INTO vdydev_zone_has_point VALUES("763","28","38.492992","-0.834389","12");
INSERT INTO vdydev_zone_has_point VALUES("764","28","38.507504","-0.813789","13");
INSERT INTO vdydev_zone_has_point VALUES("765","28","38.491917","-0.712852","14");
INSERT INTO vdydev_zone_has_point VALUES("766","28","38.572487","-0.736885","15");
INSERT INTO vdydev_zone_has_point VALUES("767","28","38.611668","-0.70118","16");
INSERT INTO vdydev_zone_has_point VALUES("768","28","38.664764","-0.751305","17");
INSERT INTO vdydev_zone_has_point VALUES("769","28","38.700142","-0.776711","18");
INSERT INTO vdydev_zone_has_point VALUES("770","28","38.73175","-0.836449","19");
INSERT INTO vdydev_zone_has_point VALUES("771","28","38.727921","-0.899906","20");
INSERT INTO vdydev_zone_has_point VALUES("772","29","38.492992","-1.015663","0");
INSERT INTO vdydev_zone_has_point VALUES("773","29","38.467728","-1.045189","1");
INSERT INTO vdydev_zone_has_point VALUES("774","29","38.440845","-1.085701","2");
INSERT INTO vdydev_zone_has_point VALUES("775","29","38.415562","-1.087761","3");
INSERT INTO vdydev_zone_has_point VALUES("776","29","38.387043","-1.088448","4");
INSERT INTO vdydev_zone_has_point VALUES("777","29","38.357971","-1.087761","5");
INSERT INTO vdydev_zone_has_point VALUES("778","29","38.345051","-1.078835","6");
INSERT INTO vdydev_zone_has_point VALUES("779","29","38.339127","-1.030083","7");
INSERT INTO vdydev_zone_has_point VALUES("780","29","38.320271","-0.988197","8");
INSERT INTO vdydev_zone_has_point VALUES("781","29","38.304649","-0.982704","9");
INSERT INTO vdydev_zone_has_point VALUES("782","29","38.26638","-0.950432","10");
INSERT INTO vdydev_zone_has_point VALUES("783","29","38.253983","-0.914726","11");
INSERT INTO vdydev_zone_has_point VALUES("784","29","38.326736","-0.776024","12");
INSERT INTO vdydev_zone_has_point VALUES("785","29","38.334816","-0.709419","13");
INSERT INTO vdydev_zone_has_point VALUES("786","29","38.342358","-0.657234","14");
INSERT INTO vdydev_zone_has_point VALUES("787","29","38.370354","-0.642128","15");
INSERT INTO vdydev_zone_has_point VALUES("788","29","38.399422","-0.642815","16");
INSERT INTO vdydev_zone_has_point VALUES("789","29","38.418789","-0.719032","17");
INSERT INTO vdydev_zone_has_point VALUES("790","29","38.489231","-0.687447","18");
INSERT INTO vdydev_zone_has_point VALUES("791","29","38.508038","-0.813789","19");
INSERT INTO vdydev_zone_has_point VALUES("792","29","38.492992","-0.835762","20");
INSERT INTO vdydev_zone_has_point VALUES("793","29","38.468266","-0.892067","21");
INSERT INTO vdydev_zone_has_point VALUES("794","29","38.492992","-1.015663","22");
INSERT INTO vdydev_zone_has_point VALUES("795","30","38.321995","-0.510864","0");
INSERT INTO vdydev_zone_has_point VALUES("796","30","38.281391","-0.51916","1");
INSERT INTO vdydev_zone_has_point VALUES("797","30","38.255779","-0.51538","2");
INSERT INTO vdydev_zone_has_point VALUES("798","30","38.21236","-0.50611","3");
INSERT INTO vdydev_zone_has_point VALUES("799","30","38.196449","-0.51298","4");
INSERT INTO vdydev_zone_has_point VALUES("800","30","38.186729","-0.5322","5");
INSERT INTO vdydev_zone_has_point VALUES("801","30","38.185921","-0.55521","6");
INSERT INTO vdydev_zone_has_point VALUES("802","30","38.184841","-0.58061","7");
INSERT INTO vdydev_zone_has_point VALUES("803","30","38.178101","-0.60121","8");
INSERT INTO vdydev_zone_has_point VALUES("804","30","38.158932","-0.61906","9");
INSERT INTO vdydev_zone_has_point VALUES("805","30","38.134899","-0.63486","10");
INSERT INTO vdydev_zone_has_point VALUES("806","30","38.113838","-0.64138","11");
INSERT INTO vdydev_zone_has_point VALUES("807","30","38.121132","-0.66885","12");
INSERT INTO vdydev_zone_has_point VALUES("808","30","38.13166","-0.72927","13");
INSERT INTO vdydev_zone_has_point VALUES("809","30","38.131119","-0.76223","14");
INSERT INTO vdydev_zone_has_point VALUES("810","30","38.13139","-0.83021","15");
INSERT INTO vdydev_zone_has_point VALUES("811","30","38.156231","-0.84806","16");
INSERT INTO vdydev_zone_has_point VALUES("812","30","38.177292","-0.89441","17");
INSERT INTO vdydev_zone_has_point VALUES("813","30","38.255589","-0.91541","18");
INSERT INTO vdydev_zone_has_point VALUES("814","30","38.32835","-0.77533","19");
INSERT INTO vdydev_zone_has_point VALUES("815","30","38.336971","-0.69019","20");
INSERT INTO vdydev_zone_has_point VALUES("816","30","38.343971","-0.65723","21");
INSERT INTO vdydev_zone_has_point VALUES("817","30","38.369808","-0.64281","22");
INSERT INTO vdydev_zone_has_point VALUES("818","30","38.324039","-0.61603","23");
INSERT INTO vdydev_zone_has_point VALUES("819","30","38.316341","-0.609741","24");
INSERT INTO vdydev_zone_has_point VALUES("820","30","38.319035","-0.596008","25");
INSERT INTO vdydev_zone_has_point VALUES("821","30","38.331692","-0.576439","26");
INSERT INTO vdydev_zone_has_point VALUES("822","30","38.338158","-0.54142","27");
INSERT INTO vdydev_zone_has_point VALUES("823","30","38.336269","-0.53318","28");
INSERT INTO vdydev_zone_has_point VALUES("824","30","38.33223","-0.525627","29");
INSERT INTO vdydev_zone_has_point VALUES("825","30","38.321995","-0.510864","30");
INSERT INTO vdydev_zone_has_point VALUES("826","31","38.294872","-0.974536","0");
INSERT INTO vdydev_zone_has_point VALUES("827","31","38.286049","-0.970588","1");
INSERT INTO vdydev_zone_has_point VALUES("828","31","38.273449","-0.968442","2");
INSERT INTO vdydev_zone_has_point VALUES("829","31","38.21299","-0.983391","3");
INSERT INTO vdydev_zone_has_point VALUES("830","31","38.174133","-1.003304","4");
INSERT INTO vdydev_zone_has_point VALUES("831","31","38.137421","-1.038322","5");
INSERT INTO vdydev_zone_has_point VALUES("832","31","38.100143","-1.031456","6");
INSERT INTO vdydev_zone_has_point VALUES("833","31","38.059608","-1.01223","7");
INSERT INTO vdydev_zone_has_point VALUES("834","31","38.028786","-0.979958","8");
INSERT INTO vdydev_zone_has_point VALUES("835","31","37.970348","-0.937386","9");
INSERT INTO vdydev_zone_has_point VALUES("836","31","37.940029","-0.916786","10");
INSERT INTO vdydev_zone_has_point VALUES("837","31","37.902653","-0.881081","11");
INSERT INTO vdydev_zone_has_point VALUES("838","31","37.865257","-0.832329","12");
INSERT INTO vdydev_zone_has_point VALUES("839","31","37.858753","-0.80349","13");
INSERT INTO vdydev_zone_has_point VALUES("840","31","37.846825","-0.787697","14");
INSERT INTO vdydev_zone_has_point VALUES("841","31","37.842487","-0.760231","15");
INSERT INTO vdydev_zone_has_point VALUES("842","31","37.889107","-0.740318","16");
INSERT INTO vdydev_zone_has_point VALUES("843","31","37.906986","-0.730705","17");
INSERT INTO vdydev_zone_has_point VALUES("844","31","37.908611","-0.708733","18");
INSERT INTO vdydev_zone_has_point VALUES("845","31","37.923779","-0.706673","19");
INSERT INTO vdydev_zone_has_point VALUES("846","31","37.936237","-0.713539","20");
INSERT INTO vdydev_zone_has_point VALUES("847","31","37.942196","-0.694313","21");
INSERT INTO vdydev_zone_has_point VALUES("848","31","37.964394","-0.677834","22");
INSERT INTO vdydev_zone_has_point VALUES("849","31","37.980091","-0.652428","23");
INSERT INTO vdydev_zone_has_point VALUES("850","31","38.00444","-0.640755","24");
INSERT INTO vdydev_zone_has_point VALUES("851","31","38.029865","-0.647621","25");
INSERT INTO vdydev_zone_has_point VALUES("852","31","38.06123","-0.644875","26");
INSERT INTO vdydev_zone_has_point VALUES("853","31","38.083935","-0.639381","27");
INSERT INTO vdydev_zone_has_point VALUES("854","31","38.114193","-0.641441","28");
INSERT INTO vdydev_zone_has_point VALUES("855","31","38.132019","-0.727272","29");
INSERT INTO vdydev_zone_has_point VALUES("856","31","38.131477","-0.830269","30");
INSERT INTO vdydev_zone_has_point VALUES("857","31","38.15686","-0.847435","31");
INSERT INTO vdydev_zone_has_point VALUES("858","31","38.177914","-0.894127","32");
INSERT INTO vdydev_zone_has_point VALUES("859","31","38.255058","-0.9161","33");
INSERT INTO vdydev_zone_has_point VALUES("860","31","38.266918","-0.950432","34");
INSERT INTO vdydev_zone_has_point VALUES("861","31","38.294872","-0.974536","35");
INSERT INTO vdydev_zone_has_point VALUES("862","32","38.378201","-0.407438","0");
INSERT INTO vdydev_zone_has_point VALUES("863","32","38.364079","-0.40861","1");
INSERT INTO vdydev_zone_has_point VALUES("864","32","38.351158","-0.40071","2");
INSERT INTO vdydev_zone_has_point VALUES("865","32","38.351158","-0.42406","3");
INSERT INTO vdydev_zone_has_point VALUES("866","32","38.357349","-0.44157","4");
INSERT INTO vdydev_zone_has_point VALUES("867","32","38.354118","-0.45667","5");
INSERT INTO vdydev_zone_has_point VALUES("868","32","38.34362","-0.47178","6");
INSERT INTO vdydev_zone_has_point VALUES("869","32","38.327728","-0.48654","7");
INSERT INTO vdydev_zone_has_point VALUES("870","32","38.326382","-0.49924","8");
INSERT INTO vdydev_zone_has_point VALUES("871","32","38.327999","-0.50439","9");
INSERT INTO vdydev_zone_has_point VALUES("872","32","38.322803","-0.508804","10");
INSERT INTO vdydev_zone_has_point VALUES("873","32","38.323612","-0.513268","11");
INSERT INTO vdydev_zone_has_point VALUES("874","32","38.327381","-0.520821","12");
INSERT INTO vdydev_zone_has_point VALUES("875","32","38.3325","-0.526657","13");
INSERT INTO vdydev_zone_has_point VALUES("876","32","38.337349","-0.542793","14");
INSERT INTO vdydev_zone_has_point VALUES("877","32","38.346771","-0.541763","15");
INSERT INTO vdydev_zone_has_point VALUES("878","32","38.356464","-0.536613","16");
INSERT INTO vdydev_zone_has_point VALUES("879","32","38.346771","-0.540047","17");
INSERT INTO vdydev_zone_has_point VALUES("880","32","38.356735","-0.535927","18");
INSERT INTO vdydev_zone_has_point VALUES("881","32","38.347042","-0.540047","19");
INSERT INTO vdydev_zone_has_point VALUES("882","32","38.349197","-0.542793","20");
INSERT INTO vdydev_zone_has_point VALUES("883","32","38.347851","-0.540047","21");
INSERT INTO vdydev_zone_has_point VALUES("884","32","38.350811","-0.537643","22");
INSERT INTO vdydev_zone_has_point VALUES("885","32","38.346771","-0.54142","23");
INSERT INTO vdydev_zone_has_point VALUES("886","32","38.347042","-0.542793","24");
INSERT INTO vdydev_zone_has_point VALUES("887","32","38.348118","-0.540733","25");
INSERT INTO vdydev_zone_has_point VALUES("888","32","38.355118","-0.537987","26");
INSERT INTO vdydev_zone_has_point VALUES("889","32","38.359158","-0.53627","27");
INSERT INTO vdydev_zone_has_point VALUES("890","32","38.360504","-0.5373","28");
INSERT INTO vdydev_zone_has_point VALUES("891","32","38.366695","-0.532494","29");
INSERT INTO vdydev_zone_has_point VALUES("892","32","38.361313","-0.53421","30");
INSERT INTO vdydev_zone_has_point VALUES("893","32","38.363735","-0.532837","31");
INSERT INTO vdydev_zone_has_point VALUES("894","32","38.36562","-0.535583","32");
INSERT INTO vdydev_zone_has_point VALUES("895","32","38.367233","-0.533524","33");
INSERT INTO vdydev_zone_has_point VALUES("896","32","38.366425","-0.53318","34");
INSERT INTO vdydev_zone_has_point VALUES("897","32","38.3675","-0.533867","35");
INSERT INTO vdydev_zone_has_point VALUES("898","32","38.366962","-0.531464","36");
INSERT INTO vdydev_zone_has_point VALUES("899","32","38.372887","-0.533867","37");
INSERT INTO vdydev_zone_has_point VALUES("900","32","38.370193","-0.532494","38");
INSERT INTO vdydev_zone_has_point VALUES("901","32","38.371002","-0.53112","39");
INSERT INTO vdydev_zone_has_point VALUES("902","32","38.3745","-0.526314","40");
INSERT INTO vdydev_zone_has_point VALUES("903","32","38.379616","-0.520134","41");
INSERT INTO vdydev_zone_has_point VALUES("904","32","38.37746","-0.522537","42");
INSERT INTO vdydev_zone_has_point VALUES("905","32","38.378536","-0.521507","43");
INSERT INTO vdydev_zone_has_point VALUES("906","32","38.378269","-0.519447","44");
INSERT INTO vdydev_zone_has_point VALUES("907","32","38.380959","-0.519791","45");
INSERT INTO vdydev_zone_has_point VALUES("908","32","38.389977","-0.533352","46");
INSERT INTO vdydev_zone_has_point VALUES("909","32","38.385929","-0.52253","47");
INSERT INTO vdydev_zone_has_point VALUES("910","32","38.382702","-0.51687","48");
INSERT INTO vdydev_zone_has_point VALUES("911","32","38.378403","-0.510778","49");
INSERT INTO vdydev_zone_has_point VALUES("912","32","38.380016","-0.500479","50");
INSERT INTO vdydev_zone_has_point VALUES("913","32","38.381699","-0.49078","51");
INSERT INTO vdydev_zone_has_point VALUES("914","32","38.38271","-0.485373","52");
INSERT INTO vdydev_zone_has_point VALUES("915","32","38.383652","-0.48116","53");
INSERT INTO vdydev_zone_has_point VALUES("916","32","38.384258","-0.474987","54");
INSERT INTO vdydev_zone_has_point VALUES("917","32","38.384392","-0.471039","55");
INSERT INTO vdydev_zone_has_point VALUES("918","32","38.385201","-0.460396","56");
INSERT INTO vdydev_zone_has_point VALUES("919","32","38.385536","-0.455503","57");
INSERT INTO vdydev_zone_has_point VALUES("920","32","38.38567","-0.452929","58");
INSERT INTO vdydev_zone_has_point VALUES("921","32","38.385738","-0.450954","59");
INSERT INTO vdydev_zone_has_point VALUES("922","32","38.38567","-0.449924","60");
INSERT INTO vdydev_zone_has_point VALUES("923","32","38.386883","-0.447779","61");
INSERT INTO vdydev_zone_has_point VALUES("924","32","38.387623","-0.445633","62");
INSERT INTO vdydev_zone_has_point VALUES("925","32","38.388363","-0.441685","63");
INSERT INTO vdydev_zone_has_point VALUES("926","32","38.38863","-0.438766","64");
INSERT INTO vdydev_zone_has_point VALUES("927","32","38.385941","-0.434475","65");
INSERT INTO vdydev_zone_has_point VALUES("928","32","38.383854","-0.429411","66");
INSERT INTO vdydev_zone_has_point VALUES("929","32","38.377663","-0.4216","67");
INSERT INTO vdydev_zone_has_point VALUES("930","32","38.379414","-0.418253","68");
INSERT INTO vdydev_zone_has_point VALUES("931","32","38.378201","-0.407438","69");
INSERT INTO vdydev_zone_has_point VALUES("932","33","38.474098","-0.312138","0");
INSERT INTO vdydev_zone_has_point VALUES("933","33","38.558449","-0.439854","1");
INSERT INTO vdydev_zone_has_point VALUES("934","33","38.572678","-0.470409","2");
INSERT INTO vdydev_zone_has_point VALUES("935","33","38.581802","-0.491009","3");
INSERT INTO vdydev_zone_has_point VALUES("936","33","38.594952","-0.500622","4");
INSERT INTO vdydev_zone_has_point VALUES("937","33","38.608902","-0.510578","5");
INSERT INTO vdydev_zone_has_point VALUES("938","33","38.627144","-0.524654","6");
INSERT INTO vdydev_zone_has_point VALUES("939","33","38.626339","-0.529118","7");
INSERT INTO vdydev_zone_has_point VALUES("940","33","38.598171","-0.535984","8");
INSERT INTO vdydev_zone_has_point VALUES("941","33","38.527836","-0.55212","9");
INSERT INTO vdydev_zone_has_point VALUES("942","33","38.483238","-0.608425","10");
INSERT INTO vdydev_zone_has_point VALUES("943","33","38.489151","-0.688419","11");
INSERT INTO vdydev_zone_has_point VALUES("944","33","38.41898","-0.719662","12");
INSERT INTO vdydev_zone_has_point VALUES("945","33","38.399071","-0.642414","13");
INSERT INTO vdydev_zone_has_point VALUES("946","33","38.369469","-0.642414","14");
INSERT INTO vdydev_zone_has_point VALUES("947","33","38.316422","-0.610142","15");
INSERT INTO vdydev_zone_has_point VALUES("948","33","38.319115","-0.596409","16");
INSERT INTO vdydev_zone_has_point VALUES("949","33","38.331772","-0.576839","17");
INSERT INTO vdydev_zone_has_point VALUES("950","33","38.337967","-0.543537","18");
INSERT INTO vdydev_zone_has_point VALUES("951","33","38.346851","-0.541821","19");
INSERT INTO vdydev_zone_has_point VALUES("952","33","38.364082","-0.534954","20");
INSERT INTO vdydev_zone_has_point VALUES("953","33","38.372967","-0.526371","21");
INSERT INTO vdydev_zone_has_point VALUES("954","33","38.378887","-0.518818","22");
INSERT INTO vdydev_zone_has_point VALUES("955","33","38.379696","-0.508175","23");
INSERT INTO vdydev_zone_has_point VALUES("956","33","38.383465","-0.489979","24");
INSERT INTO vdydev_zone_has_point VALUES("957","33","38.383732","-0.478992","25");
INSERT INTO vdydev_zone_has_point VALUES("958","33","38.385078","-0.464573","26");
INSERT INTO vdydev_zone_has_point VALUES("959","33","38.385345","-0.453243","27");
INSERT INTO vdydev_zone_has_point VALUES("960","33","38.388577","-0.4426","28");
INSERT INTO vdydev_zone_has_point VALUES("961","33","38.388306","-0.437107","29");
INSERT INTO vdydev_zone_has_point VALUES("962","33","38.38427","-0.431271","30");
INSERT INTO vdydev_zone_has_point VALUES("963","33","38.378349","-0.421314","31");
INSERT INTO vdydev_zone_has_point VALUES("964","33","38.379425","-0.417194","32");
INSERT INTO vdydev_zone_has_point VALUES("965","33","38.378616","-0.407581","33");
INSERT INTO vdydev_zone_has_point VALUES("966","33","38.411446","-0.397625","34");
INSERT INTO vdydev_zone_has_point VALUES("967","33","38.412792","-0.388355","35");
INSERT INTO vdydev_zone_has_point VALUES("968","33","38.419518","-0.386982","36");
INSERT INTO vdydev_zone_has_point VALUES("969","33","38.429199","-0.382175","37");
INSERT INTO vdydev_zone_has_point VALUES("970","33","38.434307","-0.375652","38");
INSERT INTO vdydev_zone_has_point VALUES("971","33","38.441032","-0.370159","39");
INSERT INTO vdydev_zone_has_point VALUES("972","33","38.444527","-0.357456","40");
INSERT INTO vdydev_zone_has_point VALUES("973","33","38.453938","-0.34029","41");
INSERT INTO vdydev_zone_has_point VALUES("974","33","38.460659","-0.335827","42");
INSERT INTO vdydev_zone_has_point VALUES("975","33","38.463615","-0.324497","43");
INSERT INTO vdydev_zone_has_point VALUES("976","33","38.468456","-0.317974","44");
INSERT INTO vdydev_zone_has_point VALUES("977","33","38.474098","-0.312138","45");



