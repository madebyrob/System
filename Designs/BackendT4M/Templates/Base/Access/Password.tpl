<div class="PasswordRecovery">
	<form method="post" action="{$Action}" autocomplete="off">
		{?_UserStatus=1}
			<p>{TextLoggedOut}</p>
			<ul class="Form">
				<li>
					<label for="EmailAddress">{LabelEmailAddress}</label>
					<input id="EmailAddress" name="EmailAddress" type="text" value="{$EmailAddress}" />
				</li>
			</ul>
			<div class="Buttons">
				<button class="Button" type="submit">{ButtonSendPassword}</button>
			</div>
		{/?_UserStatus=1}
		
		{?_UserStatus=2}
			<p>{TextLoggedIn}</p>
			{?_InstancePassword}<input name="PasswordOld" type="hidden" value="{$_InstancePassword}" />{/?_InstancePassword}
			<ul class="Form">
				{?!_InstancePassword}
				<li>
					<label for="PasswordOld">{LabelPasswordOld}</label>
					<input class="Input" name="PasswordOld" id="PasswordOld" type="password" />
				</li>
				{/?!_InstancePassword}
				<li>
					<label for="PasswordNew">{LabelPasswordNew}</label>
					<input class="Input" name="PasswordNew" id="PasswordNew" type="password" />
				</li>
				<li>
					<label for="PasswordConfirm">{LabelPasswordConfirm}</label>
					<input class="Input" name="PasswordConfirm" id="PasswordConfirm" type="password" />
				</li>
			</ul>
			<div class="Buttons">
				<button class="Button" type="submit">{ButtonSavePassword}</button>
			</div>
		{/?_UserStatus=2}
	</form>
</div>