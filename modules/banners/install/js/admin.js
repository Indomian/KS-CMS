function RecheckTable()
{
	if($('table.calendar').length>0)
	{
		obRows=$('table.calendar tr');
		for(i=2;i<obRows.length;i++)
		{
			obRow=obRows.eq(i);
			count=obRow.children('td.checked').length;
			if(count==7)
			{
				if(obRow.children('td.allH').hasClass('checked'))
				{
					obRow.children('td.allH').removeClass('checked');
				}
				else
				{
					obRow.children('td.allH').addClass('checked');
				}
			}
			else if(count<8)
			{
				obRow.children('td.allH').removeClass('checked');
			}
			else
			{
				obRow.children('td.allH').addClass('checked');
			}
		}
		iChecked=0;
		for(i=1;i<8;i++)
		{
			iCount=0;
			for(j=2;j<28;j++)
			{
				if($('table.calendar tr').eq(j).children('td').eq(i).hasClass('checked')) iCount++;
			}
			if(iCount==24)
			{
				$('table.calendar tr td.allD:eq('+(i-1)+')').addClass('checked');
				iChecked++;
			}
			else
			{
				$('table.calendar tr td.allD:eq('+(i-1)+')').removeClass('checked');
			}
		}
		if(iChecked==7)
			$('table.calendar tr td.allY').addClass('checked');
		else
			$('table.calendar tr td.allY').removeClass('checked');
	}
}

$(document).ready(function(){
	if($('table.calendar').length>0)
	{
		$('table.calendar td input').hide();
		$('table.calendar td').click(function(e){
			if(!$(this).hasClass('allH') && !$(this).hasClass('allD') && !$(this).hasClass('allY'))
			{
				if($(this).hasClass('checked') )
				{
					$(this).removeClass('checked').addClass('unchecked').children('input').attr('checked',false);
				}
				else if($(this).hasClass('unchecked'))
				{
					$(this).removeClass('unchecked').addClass('checked').children('input').attr('checked',true);
				}
				RecheckTable();
			}
		});
		$('table.calendar td.allD').click(function(e){
			if($(this).hasClass('checked'))
			{
				index=$(this).index()-1;
				ob=$(this).parent().next()
				while(ob.length>0)
				{
					ob.children('td:eq('+index+')').removeClass('checked').addClass('unchecked').children('input').attr('checked',false);
					ob=ob.next();
				}
				$(this).removeClass('checked');
			}
			else
			{
				index=$(this).index()-1;
				ob=$(this).parent().next()
				while(ob.length>0)
				{
					ob.children('td:eq('+index+')').removeClass('unchecked').addClass('checked').children('input').attr('checked',true);
					ob=ob.next();
				}
				$(this).addClass('checked');
			}
			RecheckTable();
		});
		$('table.calendar td.allH').click(function(e){
			if($(this).hasClass('checked'))
			{
				ob=$(this).next()
				while(ob.length>0)
				{
					ob.removeClass('checked').addClass('unchecked').children('input').attr('checked',false);
					ob=ob.next();
				}
				$(this).removeClass('checked');
			}
			else
			{
				ob=$(this).next()
				while(ob.length>0)
				{
					ob.removeClass('unchecked').addClass('checked').children('input').attr('checked',true);
					ob=ob.next();
				}
				$(this).addClass('checked');
			}
			RecheckTable();
		});
		$('table.calendar td.allY').click(function(e){
			if($(this).hasClass('checked'))
			{
				for(i=1;i<8;i++)
				{
					for(j=2;j<28;j++)
					{
						$('table.calendar tr').eq(j).children('td').eq(i).removeClass('checked').addClass('unchecked').children('input').attr('checked',false);
					}
				}
				$(this).removeClass('checked');
			}
			else
			{
				for(i=1;i<8;i++)
				{
					for(j=2;j<28;j++)
					{
						$('table.calendar tr').eq(j).children('td').eq(i).removeClass('unchecked').addClass('checked').children('input').attr('checked',true);
					}
				}
				$(this).addClass('checked');
			}
			RecheckTable();
		});
		RecheckTable();
	}
	$('#filterStatistics').click(function(){
		var dateFrom=$('#statisticsFrom').datetimepicker('getDate');
		var dateTo=$('#statisticsTo').datetimepicker('getDate');
		iDate=dateFrom.getTime()/1000;
		iDateTo=dateTo.getTime()/1000;
		params={
			'dateFrom':iDate,
			'dateTo':iDateTo,
			'module':'banners',
			'page':'banners',
			'action':'getStatistics',
			'id':{/literal}{$data.id}{literal}
		};
		$.getJSON('/admin.php',params,function(data){
			if(data.error)
			{
				alert(data.error)
			}
			else
			{
				$('#statResult tr:gt(0)').remove();
				mytable=$('#statResult');
				for(ii in data.list)
				{
					mytable.append('<tr><td rowspan="2">'+ii+'</td><td>Показов</td></tr>');
					var row=$('#statResult tr:last');
					for(i=0;i<24;i++)
					{
						if(data.list[ii][i])
						{
							row.append('<td>'+data.list[ii][i].views+'</td>');
						}
						else
						{
							row.append('<td>-</td>');
						}
					}
					mytable.append('<tr><td>Хитов</td></tr>');
					var row=$('#statResult tr:last');
					for(i=0;i<24;i++)
					{
						if(data.list[ii][i])
						{
							row.append('<td>'+data.list[ii][i].hits+'</td>');
						}
						else
						{
							row.append('<td>-</td>');
						}
					}
				}
			}
		});
	});
});