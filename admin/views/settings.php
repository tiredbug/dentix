<div class="wrap">
    <h2>WordPress Dental Records</h2>
    <form method="post" action="options.php"> 
        <?php @settings_fields('dentix_settings-group'); ?>
        <?php @do_settings_fields('dentix_settings-group'); ?>

        <?php do_settings_sections('dentix_settings'); ?>

        <?php @submit_button(); ?>
    </form>
</div>
