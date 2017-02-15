<label for="%name%">%label%</label><input class="%class%" placeholder="%defaultvalue%" type="text" name="%name%" value="%value%" %attributes%>%error%

<script language="javascript">
$('input[name=%name%]').ColorPicker({
	onSubmit: function(hsb, hex, rgb, el) {
		$(el).val(hex);
		$(el).ColorPickerHide();
	},
	onChange: function(hsb, hex, rgb){
		$('input[name=%name%]').val(hex);
	},
	onBeforeShow: function () {
		$(this).ColorPickerSetColor(this.value);
	}
})
.bind('keyup', function(){
	$(this).ColorPickerSetColor(this.value);
});
</script>

