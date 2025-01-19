<?php if (isset($captcha_error)) { ?>
<div>
	<p style="color:red"><?php echo $captcha_error; ?></p>
</div>
<?php } ?>
<div>
	<input type="image" style="cursor:crosshair;" src="<?php echo Uri::base().'captcha/circlecaptcha'; ?>" alt="Circle Captcha" name="button" />
</div>
