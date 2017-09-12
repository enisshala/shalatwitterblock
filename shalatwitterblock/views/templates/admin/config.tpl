<form action="" method="post" class="defaultForm form-horizontal">
	<div class="panel">
		<div class="panel-heading">{l s='Settings' mod='moduledemo1'}</div>

		<div class="form-group">
			<label class="control-label col-lg-3">{l s='My Field' mod='moduledemo1'}</label>
			<div class="col-lg-6">
				<textarea name="ourtext">{$ourtext}</textarea>
			</div>
		</div>
		<input type="submit" class="btn btn-default" name="submitUpdate" value="{l s='Save' mod='moduledemo1'}"/>
	</div>
</form>