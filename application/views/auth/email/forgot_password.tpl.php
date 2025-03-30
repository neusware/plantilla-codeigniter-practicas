<html>
<style>
.overlay {
  background-color: #FFFFFF;
  border: 1px solid #000000;
  color: #000000;
  padding: 0.5em;
}
.shadow {
  background-color: #CCCCCC;
}
.shadow .overlay {
  bottom: 4px;
  position: relative;
  right: 4px;
}

</style>
<body>
	<div class="shadow">
		<div class="overlay">
			<h4><?php echo sprintf(lang('email_forgot_password_heading'), $identity);?></h4>
		<p> <?php echo sprintf(lang('email_forgot_password_subheading'),
      // '<a href="'.base_url('resetPassword/'. $forgotten_password_code).'">'.lang('email_forgot_password_link').'</a>'
      '<a href="http://localhost:9528/resetPassword/'. base64_encode($forgotten_password_code).'">'.lang('email_forgot_password_link').'</a>'
		) ?>
    <p>Este correo es generado automáticamente, por favor no conteste a este correo.</p>
		</div>
	</div>
</body>
</html>