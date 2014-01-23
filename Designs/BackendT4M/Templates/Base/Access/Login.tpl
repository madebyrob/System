{?_UserStatus>1}
<div class="User LoggedIn">
	<b>{LabelUser}</b> {$_UserName}<canvas class="Timer" width="16" height="16"></canvas>
	<a class="Button Logout" href="{$ActionLogout}" title="{Logout}"></a>
</div>
{/?_UserStatus>1}

{?_UserStatus=1}
<form id="Login" method="post" action="{$ActionLogin}" onkeypress="if ( event.keyCode == 13 ) Login.Submit()">
	<input name="RedirectTo" value="{$RedirectTo}" type="hidden" />
	<ul class="Form">
		<li>
			<label id="UserKeyLabel" for="UserKey">{LabelKey}</label>
			{?!Users}
			<input id="UserKey" name="UserKey" type="text" />
			{/?!Users}
			{?Users}
			<select id="UserKey" name="UserKey">
				<option value="">{PleaseSelect}</option>
				{@Users}
				<option value="{$Key}">{$Key}</option>
				{/@Users}
			</select>
			{/?Users}
		</li>
		<li>
			<label id="UserPasswordLabel" for="UserPassword">{LabelPassword}</label>
			<input id="UserPassword" name="UserPassword" type="password" /> <a href="#">{ForgotPassword}</a>
		</li>
	</ul>
	<button class="Button Accept" type="submit" title="{ButtonLogin}"></button>
</form>
{/?_UserStatus=1}