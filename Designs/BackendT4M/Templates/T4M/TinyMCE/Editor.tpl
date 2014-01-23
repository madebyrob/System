<div class="Editor">
	<a class="Button Right" href="javascript:" onclick="Editor.Return()">Übernehmen</a>
	<a class="Button Right Light" href="javascript:" onclick="Editor.Close()">Abbrechen</a>
	<h1>Editor <span style="color: #dddddd;">&bull; <span class="Title"></span></span></h1>
	<div class="Content">
		<textarea></textarea>
	</div>
</div>
<script type="text/javascript" src="{$ScriptUrl}jquery.tinymce.min.js"></script>
<script type="text/javascript" src="{$ScriptUrl}tinymce.min.js"></script>
<script type="text/javascript">

$(function()
{
	Editor.Init();
	
	$(window).resize(function()
	{
		Editor.SetHeight();
	});
});

Editor =
{
	Container: $('.Editor'),
	ReturnTo: null,
	
	Init: function()
	{
		Settings =
		{
			plugins: 'advlist anchor autolink charmap code contextmenu directionality fullscreen hr image insertdatetime legacyoutput link lists media nonbreaking noneditable pagebreak paste preview print save searchreplace spellchecker tabfocus table template textcolor visualblocks visualchars wordcount',
			toolbar1: 'undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | blockquote',
			toolbar2: 'formatselect styleselect | link unlink image media | table | code | fullscreen',
			menubar: 'edit insert view format table tools',
			resize: false,
			content_css: '/Designs/Default/CSS/Base.css',
			language: 'de',
			width: '100%',
			height: 'auto',
			doctype : '<!DOCTYPE html>',
			file_browser_callback: function( ReturnTo, Value, Type, Window ) { Browser.Open(ReturnTo, Value, Type, Window); },
			document_base_url: '{$_Url}',
			entity_encoding: 'raw'
		};
		
		this.Container.find('textarea').tinymce(Settings);
	},
	
	Open: function( Title, Element )
	{
		this.ReturnTo = $(Element);
		this.Container.show();
		this.Container.find('h1 .Title').html(Title);
		this.Container.find('textarea').val(this.ReturnTo.val()).tinymce().execCommand('mceRepaint');
		
		this.SetHeight();
	},
	
	Return: function()
	{
		this.ReturnTo.val(this.Container.find('textarea').val());
		
		this.Close();
	},
	
	Close: function()
	{
		this.Container.hide();
	},
	
	SetHeight: function()
	{
		this.Container.find('.mce-edit-area iframe').css('height', this.Container.find('.Content').height()-150+'px');
	}
}

</script>