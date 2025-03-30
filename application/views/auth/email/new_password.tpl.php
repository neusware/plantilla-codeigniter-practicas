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
			<p>Buenas,</p>
			<p><?php echo sprintf(lang('email_new_password_heading'), $identity);?></p>
			<p>Este correo es generado automáticamente, por favor no conteste a este correo.</p>
		</div>
	</div>
</body>
</html>