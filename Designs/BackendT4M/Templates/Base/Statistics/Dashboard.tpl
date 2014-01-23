<div class="LeftColumn">
	<div class="Menu">
		<ul>
			<li class="Selected"><a href="javascript:" onclick="$(this).parent().addClass('Selected').siblings().removeClass('Selected').parents('.Body').children('.RightColumn.Dashboard').show().siblings('.RightColumn').hide();">Dashboard</a></li>
			<li><a href="javascript:" onclick="$(this).parent().addClass('Selected').siblings().removeClass('Selected').parents('.Body').children('.RightColumn.Visits').show().siblings('.RightColumn').hide();">Besuche</a></li>
		</ul>
	</div>
</div>
<div class="RightColumn Dashboard">
	<h1>Dashboard</h1>
	<p>{$Visits.Count} Besuche</p>
	<p>{$Requests} Aufrufe</p>
	
	<h2>Top User-Agents</h2>
	
	<canvas class="Pie" width="120" height="120"></canvas>
	<script>
	
	Agents = {$Agents.Json};
	Requests = {$Requests};
	Colors = { Firefox: '#0099CC', Other: '#8AD5F0', Chrome: '#6DCAEC', IE: '#33B5E5', Safari: '#A8DFF4' };
	
	Canvas = $('.Pie').get(0);
	
	var Context = Canvas.getContext('2d');
	
	Context.clearRect(0, 0, Canvas.width, Canvas.height);
	
	var Offset = Math.PI/2*-1;
	var Radius = Math.min(Canvas.width, Canvas.height)/2-15;
	var Center = { X: Canvas.width/2, Y: Canvas.height/2 };
	
	for ( Agent in Agents )
	{
		Part = Math.PI*2/Requests*Agents[Agent]+Offset;
		
		Context.beginPath();
	//	Context.moveTo(Center.X, Center.Y);
		Context.arc(Center.X, Center.Y, Radius, Offset, Part, false);
		Context.lineWidth = 30;
	//	Context.closePath();
	//	Context.fillStyle = Colors[Agent];
	//	Context.fill();
		
		Context.strokeStyle = Colors[Agent];
		Context.stroke();
		
		Offset = Part;
	}
	
	</script>
	<ol>
		{@Agents}
		<li>{$_Key}: <script>document.write(Math.round({$_Value}/{$.Requests}*100))</script>%</li>
		{/@Agents}
	</ol>
</div>
<div class="RightColumn Visits" style="display: none;">
	<h1>Besuche</h1>
	<style>
	
	.table1
	{
		border-collapse: collapse;
	}
	
	.table1 td
	{
		border: 1px solid #eeeeee;
		padding: 10px;
		vertical-align: top;
	}
	
	</style>
	
	<table class="table1">
		<colgroup>
			<col width="50%" />
			<col width="50%" />
		</colgroup>
		{@Visits}
			<tr>
				<td>
					<b>{$IP}</b> ({$HostName})<br />
					{$CreateTime.DateTime}<br />
					<span style="font-size: 11px;">{$Session}</span>
				</td>
				<td>{$Requests.Count} Aufrufe
					<ol>
						{@Requests}
							<li>
								+{$TimeOffset}: {$RequestURI}
							</li>
						{/@Requests}
					</ol>
				</td>
			</tr>
		{/@Visits}
	</table>
	
</div>