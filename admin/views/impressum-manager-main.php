<?php

Impressum_Manager_Database::getInstance()->save_option( "impressum_manager_notice", "dismissed" );


if ( @$_GET['tut_finished'] == true && array_key_exists( "submit", $_REQUEST ) ) {
	$db = Impressum_Manager_Database::getInstance();

	$db->save_option( 'impressum_manager_use_imported_impressum', false );
	$db->save_option( "impressum_manager_noindex", @sanitize_text_field( $_POST['impressum_manager_noindex'] ) );
	$db->save_option( "impressum_manager_show_email_as_image", @sanitize_text_field( $_POST['impressum_manager_show_email_as_image'] ) );
}
?>
<div class="wrap">
	<h2 class="logo"><?= __( 'Impressum Manager', SLUG ) ?></h2>

	<h3><?= __( 'Willkommen bei Impressum-Manager. Dieses Plugin hilft dir deine Webseite(n) rechtsicher zu machen ...', SLUG ); ?></h3>

	<?php
if ( get_option( 'impressum_manager_confirmation' ) == false ) {
	show_confirmation();
} else {
	show_hints_box();
	show_preview_box();
}
?>
</div>


<?php
function show_confirmation() {
	?>
	<script>
		(function ($) {
			$(document).ready(function () {
				$("#submit_confirmation").click(function (event) {
					if (false === $("#impressum_manager_confirmation").prop("checked")) {
						event.preventDefault();
						$("#impressum_manager_confirmation").css("border", "2px #f00 solid");
						$("#impressum_manager_confirmation_text").css("color", "#f00");
					} else {
						$("#impressum_manager_confirmation").css("border", "inherit");
					}
				});

				$("#impressum_manager_confirmation").click(function () {
					$(this).css("border", "inherit");
					$(this).parent().css("color", "inherit");
				});
			});
		}(jQuery));
	</script>
	<div class="box primary">
		<div class="box header"><?= __( "Bestätigung des Warnhinweises", SLUG ) ?></div>

		<div
			class="box content"><?= __( 'Ich weiß, dass ich die Nutzung der Impressum, Datenschutz und Haftungsauschluss Inhalte ' .
		                                'auf eigene Gefahr verwende. ' .
		                                'Mir ist bewusst, dass Impressum Manager keine Gewährleistung auf Schadenersatz anbietet,' .
		                                ' sofern rechtliche Schäden bzgl. meiner Webseite durch die Nutzung von dem Impressum Manager Wordpress Plugin entstanden sind. ', SLUG ); ?></div>
		<br>

		<form method="post" action="options.php">
			<?php
			settings_fields( 'impressum-manager-general-settings' );
			do_settings_sections( 'impressum-manager-general-settings' );
			?>
			<label for="impressum_manager_confirmation">
				<p id="impressum_manager_confirmation_text">
					<input type="checkbox"
					       name="impressum_manager_confirmation"
					       id="impressum_manager_confirmation"
						<?= checked( "on", get_option( "impressum_manager_confirmation" ), false ) ?>>
					<?= __( "Ich bestätige hiermit, dass ich das Plugin auf eigene Gefahr nutze.", SLUG ) ?>
				</p>
			</label>
			<?php submit_button( __( "Akzeptieren", SLUG ), 'primary', 'submit_confirmation' ); ?>

		</form>
	</div>
<?php
}

function show_preview_box() {
	$impressum = Impressum_Manager_Impressum_Manager::getInstance()->get_impressum();

	if ( ! $impressum->has_content() ) {
		?>
		<div class="box primary" style="text-align: center">
			<div class="box header"><?= __( "Dein Impressum ist leer!", SLUG ) ?></div>
			<br>

			<div class="box content"
			     style="text-align: center"><?= __( "Wähle eine der Optionen aus, um dein Impressum zu erstellen.", SLUG ) ?>
				<br>

				<p>

				<form action=<?php Impressum_Manager_Admin::get_page_url() ?>>
					<input type="hidden" name="page" value="<?= SLUG ?>">
					<input type="hidden" name="view" value="tutorial"/>
					<input type="hidden" name="step" value="1"/>
					<input class="button button-primary" type="submit" id="configure_impressum_button"
					       value="<?= __( 'Impressum generieren', SLUG ) ?>">
				</form>
				<br>
				<?= __( 'oder', SLUG ) ?>
				<br>
				<br>

				<form action="<?php Impressum_Manager_Admin::get_page_url() ?>#import-tab">
					<input type="hidden" name="page" value="<?= SLUG ?>">
					<input type="hidden" name="view" value="config"/>
					<input class="button button-primary" type="submit" id="import_impressum_button"
					       value="<?= __( 'Impressum importieren', SLUG ) ?>">
				</form>
				</p>
			</div>
		</div>
		<br>
		<div style="text-align: center">
			<p><?= __( 'Alternativ kannst du auch direkt zu den', SLUG ) ?></p>

			<form action="<?php Impressum_Manager_Admin::get_page_url() ?>#general-tab">
				<input type="hidden" name="page" value="<?= SLUG ?>">
				<input type="hidden" name="view" value="config"/>
				<input class="button button-secondary" type="submit" id="settings_button"
				       value="<?= __( 'Einstellungen', SLUG ) ?>">
			</form>
		</div>
	<?php
	} else {
		?>
		<script>
			(function ($) {
				$(document).ready(function () {
					var loaddata = {
						'action': 'impressum_manager_get_shortcode_preview',
						'shortcode_key': ''
					};

					$.post(ajaxurl, loaddata, function (data) {
						$("#impressum-preview-content").html(data);
					});

					$("#impressum_shortcode_preview").change(function () {
						var data = {
							'action': 'impressum_manager_get_shortcode_preview',
							'shortcode_key': $(this).val()
						};

						$.post(ajaxurl, data, function (data) {
							$("#impressum-preview-content").html(data);
						});
					});
				})
			}(jQuery));
		</script>
		<div class="box primary">
			<form action="<?php Impressum_Manager_Admin::get_page_url() ?>" class="right"
			      style="display:inline">
				<input type="hidden" name="page" value="<?= SLUG ?>">
				<input type="hidden" name="view" value="config">
				<input class="button button-primary" type="submit" id="settings_button"
				       value="<?= __( 'Konfigurieren', SLUG ) ?>">
			</form>
			<div class="box header"
			     style="display:inline"><?= __( 'Wähle einen shortcode aus und schau dir die Vorschau an! ', SLUG ); ?></div>
			<br><br>


			<?= __( 'Shortcode: ', SLUG ) ?>

			<select name="impressum_shortcode_preview" id="impressum_shortcode_preview" style="display:inline">
				<?php

				$components = $impressum->get_components();
				foreach ( $components as $component ) {
					if ( $component->has_content() ) {
						$shortcode = $component->get_shortcode();
						$name      = $component->get_name();
						echo "<option value='$shortcode'>$name</option>";
					}
				}

				?>
			</select>

			<hr>
			<div id="impressum-preview-content">
				<?php
				echo $impressum->draw();
				?>
			</div>
		</div>
	<?php
	}
}

function show_hints_box() {
	?>

	<!--div class="box primary">
		<div class="box header"><?= __( "Was zu beachten ist", SLUG ) ?></div>

		<div class="box content">
			<ul>
			  <li><?= __( 'Stellen Sie die Datenschutzerklärung auf einer separaten Seite ein. Verlinken Sie diese mit "Datenschutz" gut sichtbar von allen Seiten.', SLUG) ?></li>
			  <li>x</li>
			  <li>y</li>
			</ul>
		</div>
	</div-->

<?php
}
