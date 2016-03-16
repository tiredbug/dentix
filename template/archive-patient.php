<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?php bloginfo('name'); ?></title>
<link rel='stylesheet' id='bootswatch_style-css'  href='//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css?ver=4.1' type='text/css' media='all' />
<style type="text/css" media="screen">
body { margin-top: 40px; margin-bottom: 40px; }
.container{
  max-width: none !important;
  width: 970px;
}
.table-odontogram {
	width: 100%;
	overflow-y: auto;
	_overflow: auto;
	margin: 0 0 1em;
	padding: 10px;
}
.odont_box {
	width: 30px;
	height: 30px;
	margin: 5px 2px;
	font-weight: bold; 
	border: 1px solid #000000;
	font-size: 14px;
	text-align: center;
}.odont_ket {
	width: 25px;
	height: 25px;
	margin: 5px 2px;
	font-weight: bold; 
	border: 1px solid #000000;
	font-size: 14px;
	text-align: center;
}
</style>
</head>

<body <?php body_class(); ?>>

<!--[if lt IE 8]>
<div class="alert alert-warning">
  You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.
</div>
<![endif]-->

<div class="container">

      <div class="row">
        <div class="col-sm-12 text-center">
                    <h1>REKAM MEDIS PASIEN</h1>
                    <h3><?php echo get_option('dentix_setting_dentist_name'); ?></h3>
                    <h5><?php echo get_option('dentix_setting_address'); ?></h5>
	<hr />
        </div>
      </div>

      <div class="row">
<div class="col-sm-12">
	
<?php

if(have_posts()) : while(have_posts()) : the_post();

    the_title();

    echo '<div class="entry-content">';

    the_content();

    echo '</div>';

endwhile; endif;

get_footer();

?>
	
        </div>
      </div>
</div><!-- .container -->

<script type='text/javascript' src='//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.2/modernizr.min.js?ver=4.1'></script>
<script type='text/javascript' src='//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7/html5shiv.min.js?ver=4.1'></script>
<script type='text/javascript' src='//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.js?ver=4.1'></script>
<script type='text/javascript' src='//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js?ver=4.1'></script>

</body>
</html>

