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


        <?php if(have_posts()): while(have_posts()): the_post();?>
	<?php $data = get_post_meta(get_the_ID()); ?>

      <div class="row">
<div class="col-sm-3 text-center">

<?php if ( has_post_thumbnail() ) {
	the_post_thumbnail(array(150,150));
}
else {
	echo '<img src="' . plugins_url( '/dentix/assets/img/no-image.jpg' ) . '" width="150">';
}
?>
</div>
<div class="col-sm-9 text-center">
  <h1><?php echo $data["registration_number"][0]; ?></h1>
  <h1><small>Tanggal : <?php the_time('j F Y') ?></small></h1>
</div>
</div>
<hr />
      <div class="row">
        <div class="col-sm-6">
          <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4>Detail Pasien</h4>
                  </div>
                  <div class="panel-body">
                    <p>
                      <label>Nama :</label> <?php echo $data["full_name"][0]; ?><br />
                      <label>Tgl Lahir :</label> <?php echo $data["birthdate"][0]; ?><br />
                      <label>Jenis Kelamin :</label> <?php echo $data["sex"][0]; ?><br />
                      <label>Alamat :</label> <?php echo $data["address"][0]; ?><br />
                      <label>Telepon :</label> <?php echo $data["phone_number"][0]; ?><br />
                      <label>Pekerjaan :</label> <?php echo $data["occupation"][0]; ?>
                    </p>
                  </div>
                </div>
        </div>
        <div class="col-sm-6">
          <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4>Keadaan Umum</h4>
                  </div>
                  <div class="panel-body">
                    <p>
                      <label>Keluhan Utama :</label> <?php echo $data["keluhan_utama"][0]; ?><br />
                      <label>Penyakit Sistemik :</label> 
				<?php if ( empty( $data["ps_hipertensi"][0] ) ) { echo '' ; } else { echo '*' . $data["ps_hipertensi"][0] . ''; } ?>
				<?php if ( empty( $data["ps_diabetes"][0] ) ) { echo '' ; } else { echo '*' . $data["ps_diabetes"][0] . ''; } ?>
				<?php if ( empty( $data["ps_jantung"][0] ) ) { echo '' ; } else { echo '*' . $data["ps_jantung"][0] . ''; } ?>
				<?php if ( empty( $data["ps_ginjal"][0] ) ) { echo '' ; } else { echo '*' . $data["ps_ginjal"][0] . ''; } ?>
				<?php if ( empty( $data["ps_hepatitis"][0] ) ) { echo '' ; } else { echo '*' . $data["ps_hepatitis"][0] . ''; } ?>
				<?php if ( empty( $data["ps_tbc"][0] ) ) { echo '' ; } else { echo '*' . $data["ps_tbc"][0] . ''; } ?>
				<?php if ( empty( $data["ps_aids"][0] ) ) { echo '' ; } else { echo '*' . $data["ps_aids"][0] . ''; } ?>
				<?php if ( empty( $data["ps_pms"][0] ) ) { echo '' ; } else { echo '*' . $data["ps_pms"][0] . ''; } ?>
				<?php if ( empty( $data["ps_hamil"][0] ) ) { echo '' ; } else { echo '*' . $data["ps_hamil"][0] . ''; } ?>
				<?php if ( empty( $data["ps_lain_lain"][0] ) ) { echo '' ; } else { echo '*' . $data["ps_lain_lain"][0] . ''; } ?>			<br />
                      <label>Alergi Obat :</label> <?php echo $data["alergi_obat"][0]; ?><br />
                      <label>Tekanan Darah :</label> <?php echo $data["tekanan_darah"][0]; ?> mmHG<br />
                      <label>Denyut Nadi :</label> <?php echo $data["denyut_nadi"][0]; ?> kali per menit<br />
                      <label>Riwayat Cabut Gigi :</label> <?php echo $data["riwayat_cabut_gigi"][0]; ?>
                    </p>
                  </div>
                </div>
        </div>
      </div> <!-- / end client details section -->

      <div class="row">
        <div class="col-xs-12">
          <div class="panel panel-default">
                  <div class="panel-heading text-center">
                    <h4>Odontogram</h4>
                  </div>
                  <div class="panel-body">


	<div class="table-odontogram">
	<table style="margin: 0 auto; width: 450px; text-align: center;">
		 <tr>
		 <td>8</td><td>7</td><td>6</td><td>5</td><td>4</td><td>3</td><td>2</td><td>1</td><td> </td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8</td>
		</tr>
		<tr>

		<td><div class="odont_box" style="background-color: <?php echo $data["gg_18"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_17"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_16"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_15"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_14"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_13"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_12"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_11"][0]; ?>;"> </div></td>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_21"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_22"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_23"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_24"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_25"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_26"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_27"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_28"][0]; ?>;"> </div></td>

	  	</tr>
		<tr>
		<td> </td>
		<td> </td>
		<td> </td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_55"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_54"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_53"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_52"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_51"][0]; ?>;"> </div></td>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_61"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_62"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_63"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_64"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_65"][0]; ?>;"> </div></td>
	  	<td> </td>
	  	<td> </td>
	  	<td> </td>

	  	</tr>
		<tr>
	  	<td> </td><td> </td><td> </td><td>V</td><td>IV</td><td>III</td><td>II</td><td>I</td><td> </td><td>I</td><td>II</td><td>III</td><td>IV</td><td>V</td><td> </td><td> </td><td> </td>
		</tr>
		<tr>
	  	<td> </td>
	  	<td> </td>
	  	<td> </td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_85"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_84"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_83"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_82"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_81"][0]; ?>;"> </div></td>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_71"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_72"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_73"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_74"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_75"][0]; ?>;"> </div></td>
	  	<td> </td>
	  	<td> </td>
	  	<td> </td>
	  	</tr>
		<tr>

		<td><div class="odont_box" style="background-color: <?php echo $data["gg_48"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_47"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_46"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_45"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_44"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_43"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_42"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_41"][0]; ?>;"> </div></td>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_31"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_32"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_33"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_34"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_35"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_36"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_37"][0]; ?>;"> </div></td>
		<td><div class="odont_box" style="background-color: <?php echo $data["gg_38"][0]; ?>;"> </div></td>

	  	</tr>
		<tr>
	  	<td>8</td><td>7</td><td>6</td><td>5</td><td>4</td><td>3</td><td>2</td><td>1</td><td> </td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8</td>
		</tr>
	</table>
	<br/><br/>

	<table style="margin: 0 auto; width: 450px;">
		<tr>
			<td><div class="odont_ket" style="background-color: #FFFFFF;"> </div></td>
			<td> = Normal</td>
			<td><div class="odont_ket" style="background-color: #FF0000;"> </div></td>
			<td> = Dicabut</td>
			<td><div class="odont_ket" style="background-color: #000000;"> </div></td>
			<td> = Hilang</td>
			<td><div class="odont_ket" style="background-color: #FFFF00;"> </div></td>
			<td> = Karies</td>
		</tr>
		<tr>
			<td><div class="odont_ket" style="background-color: #FF6600;"> </div></td>
			<td> = Sisa Akar</td>
			<td><div class="odont_ket" style="background-color: #0000FF;"> </div></td>
			<td> = Tumpatan</td>
			<td><div class="odont_ket" style="background-color: #FF00FF;"> </div></td>
			<td> = Gigi Tiruan</td>
			<td><div class="odont_ket" style="background-color: #339966;"> </div></td>
			<td> = Goyang</td>
		</tr>
	</table>

	</div>


                  </div>
                </div>
        </div>
      </div>


      <div class="row">
        <div class="col-xs-12">
          <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4>Keterangan Tambahan</h4>
                  </div>

		<div class="panel-body">
          	<div class="col-sm-12"><div id="content" role="main"><p><?php the_content()?></p></div></div>
		</div>

          </div>
        </div>
      </div>

        <?php endwhile; ?>
        <?php else: ?>
        <?php wp_redirect(get_bloginfo('siteurl').'/404', 404); exit; ?>  
        <?php endif;?>

</div><!-- .container -->

<script type='text/javascript' src='//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.2/modernizr.min.js?ver=4.1'></script>
<script type='text/javascript' src='//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7/html5shiv.min.js?ver=4.1'></script>
<script type='text/javascript' src='//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.js?ver=4.1'></script>
<script type='text/javascript' src='//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js?ver=4.1'></script>

</body>
</html>
