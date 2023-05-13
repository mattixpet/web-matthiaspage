---
title: Dream of songs !
layout: bare
permalink: dreamofsongs.php
---

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<meta property="og:title" content="Dream of songs">
	<meta property="og:description" content="Explore my song demos through an oldschool platformer!">
	<meta property="og:image" content="img/dreamofsongs/startscreen.jpg">
	<meta property="og:url" content="https://matthiaspetursson.com">
	<meta name="twitter:card" content="summary">
	<meta name="twitter:site" content="@mattixpet">
	<meta name="twitter:title" content="Dream of songs">
	<meta name="twitter:description" content="Explore my song demos through an oldschool platformer!">
	<meta name="twitter:image" content="https://matthiaspetursson.com/img/dreamofsongs/startscreen.jpg">

	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="icon" href="/favicon.ico">
</head>

<body class="dreamofsongs-body">
	<main>
		<canvas id="dreamOfSongs" width="800" height="450">
	        Sorry, but your browser does not support the HTML5 canvas tag.
	    </canvas>
	    <div class="highscore-wrapper">
	    	<div class="highscore-title">
	    		<h4>Highscore</h4>
	    		<div class="highscore-toggle">
	    			<img class="highscore-down hidden" src="img/dreamofsongs/highscore/chevron-down-solid.svg">
	    			<img class="highscore-up" src="img/dreamofsongs/highscore/chevron-up-solid.svg">
	    		</div>
	    	</div>
	    	<?php include('php/highscore.php') ?>
	    	<!-- <ul class="highscore">
        		<li><p>Name</p><p>Score</p><p>Deaths</p><p>Date</p></li>
        		<hr />
        		<li><p>John</p><p>50</p><p>2</p><p>Mon, 01 May 2023 17:24:58 GMT</p></li>
        		<li><p>Buchatansibal</p><p>160</p><p>56</p><p>Tue, 01 September 2023 16:24:57 GMT</p></li>
        		<li><p>Æ</p><p>0</p><p>999</p><p>Thu, 01 November 2023 04:34:00 GMT</p></li>
        	</ul> -->
	    </div>
	</main>
	
	<footer class="gamefooter">
		<a href="/" class="gamehome">Home</a>
		
		<p> Matthías Pétursson </p>

		<a href="mailto:&#111;&#108;&#100;&#115;&#099;&#104;&#111;&#111;&#108;&#048;&#049;&#049;&#050;&#051;&#064;&#103;&#109;&#097;&#105;&#108;&#046;&#099;&#111;&#109;">&#111;&#108;&#100;&#115;&#099;&#104;&#111;&#111;&#108;&#048;&#049;&#049;&#050;&#051;&#064;&#103;&#109;&#097;&#105;&#108;&#046;&#099;&#111;&#109;</a>

		<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
			<input type="hidden" name="cmd" value="_s-xclick" />
			<input type="hidden" name="hosted_button_id" value="5WKUFWNHAHVBA" />
			<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" title="If you like my stuff consider supporting me!" alt="Donate with PayPal button" />
			<img alt="" border="0" src="https://www.paypal.com/en_IS/i/scr/pixel.gif" width="1" height="1" />
		</form>
	</footer>

	<script src="js/ext/mainloop/mainloop.min.js" type="text/javascript"></script>
	<script src="js/ext/jszip/jszip.min.js" type="text/javascript"></script>
	<script src="js/ext/filesaver/FileSaver.min.js" type="text/javascript"></script>
	<script src="js/ext/dreamofsongs/dreamofsongs.min.js" type="text/javascript"></script>

	<!-- for the website, not the game -->
	<script type="text/javascript" src="js/highscore.js"></script>
</body>
