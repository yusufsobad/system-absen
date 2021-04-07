// -------------------- Chart JS
			function load_chart_dash(data,id){
				$('#chart_loading_'+id).hide();
				$('#chart_content_'+id).show();

				var dtsheet = [];var fl;var brd;
				for(var i = 0;i<data['data'].length;i++){
					fl = true;brd = 1;
					if(data['data'][i]['type']=='line'){
						fl = false;brd = 2;
					}

					dtsheet[i] = { 
						type : data['data'][i]['type'],
						label : data['data'][i]['label'],
						backgroundColor : data['data'][i]['bgColor'],
						borderColor : data['data'][i]['brdColor'],
						borderWidth : brd,
						data : data['data'][i]['data'],
						fill : fl,
						stack : typeof data['data'][i]['stack']=='object'?data['data'][i]['stack']:i,
						pointBackgroundColor : typeof data['data'][i]['pBgColor']=='object'?data['data'][i]['pBgColor']:data['data'][i]['bgColor'],
						pointBorderColor : typeof data['data'][i]['pBgColor']=='object'?data['data'][i]['pBgColor']:data['data'][i]['brdColor'],
						pointRadius : typeof data['data'][i]['pRadius']=='object'?data['data'][i]['pRadius']:3
					}
				};
				
				$('#'+id).remove(); // this is my <canvas> element
  				$('#chart_content_'+id).append('<canvas id="'+id+'" style="height: 228px;"></canvas>');

				var ctx = document.getElementById(id).getContext('2d');
				var opts = typeof(window[data['option']])=='function'?window[data['option']]():data['option'];

				window[id] = new Chart(ctx, {
					type: data['type'],
					data: {
						labels:data['label'],
						datasets:dtsheet
					},
					options: opts
				});	
			}