$(function()
{
	Messages.Init();
});

Load = function( Container, Url, Data )
{
	if ( !Browser.Opened ) Editor.Close();
	
	$('body').addClass('Loading');
	
	Context = $(Container).last().empty().get(0);
	
	Settings =
	{
		type: 'GET',
		url: Url,
		dataType: 'json',
		context: Context,
		success: function( Data )
		{
			for ( i in Data.Instances )
			{
				$(this).append(Data.Instances[i].Content);
				
				if ( Data.Messages.length ) Messages.Add(Data.Messages);
			}
			
			$('body').removeClass('Loading');
		},
		error: function( Data )
		{
			document.write(Data.responseText);
		}
	}
	
	if ( typeof Data == 'string' )
	{
		Settings.type = 'POST';
		Settings.data = Data;
	}
	
	$.ajax(Settings);
}

Messages =
{
	Container: null,
	
	Init: function()
	{
		this.Container = $('.Messages');
		this.Container.on('mouseenter', function() { $(this).addClass('Over'); });
		this.Container.on('mouseleave', function() { $(this).removeClass('Over'); });
		this.Container.on('click', function() { Messages.Show(); });
		
		$(document).on('mouseup', function() { if ( !Messages.Container.hasClass('Over') ) Messages.Hide(); });
		
		var MessageItems = this.Container.find('li:not(.All)');
		
		if ( MessageItems.length ) this.Show();
		else this.Hide();
		
		this.Container.find('.All').on('mousedown', function() { $(this).hide().siblings().show(); });
	},
	
	Add: function( Items )
	{
		var MessageList = this.Container.find('ul');
		
		MessageList.children().removeClass('New');
		
		for ( var i in Items )
		{
			var Data = Items[i];
			
			MessageList.prepend('<li class="'+Data.Type+' New">'+Data.Message+'</li>');
		}
		
		this.Show();
	},
	
	Show: function()
	{
		var MessageList = this.Container.find('ul');
		var OldMessages = MessageList.children(':not(.New, .All)');
		
		if ( OldMessages.length )
		{
			OldMessages.hide();
			MessageList.find('.All').show();
		}
		else MessageList.find('.All').hide();
		
		MessageList.show();
	},
	
	Hide: function()
	{
		this.Container.find('ul').hide();
	}
}

SubmitForm = function( Form, Container )
{
	if ( Form.action ) Load(Container, Form.action, $(Form).serialize());
	
	return false;
}

Browser =
{
	BaseUrl: '',
	
	Types: null,
	
	Opened: false,
	
	ReturnTo: null,
	
	Open: function( ReturnTo, Value, Type, Window )
	{
		if ( !this.Types || typeof this.Types['default'] == 'undefined' ) return false;
		
		this.Opened = true;
		this.ReturnTo = $(Window.document.getElementById(ReturnTo));
		
		if ( typeof this.Types[Type] == 'undefined' ) Type = 'default';
		
		Load('.Browser .Content', this.Types[Type]);
		
		$('.Browser').show();
	},
	
	Select: function( Value )
	{
		var Replace = new RegExp('^'+this.BaseUrl);
		
		this.ReturnTo.val(Value.replace(Replace, ''));
		
		this.Close();
	},
	
	Close: function()
	{
		this.Opened = false;
		
		$('.Browser').hide().children('.Content').empty();
	}
}