
// Line Chart
//google.load('visualization', '1', {packages: ['corechart', 'line']});
//google.setOnLoadCallback(drawChart);
// Bar Chart
//google.charts.load('current', {'packages':['bar']});
//google.charts.load("current", {packages:['corechart']});
google.charts.load('current', {packages: ['corechart', 'bar']}); // Material (doesn't support imgUri function for printing > PDF)

google.charts.setOnLoadCallback(drawSummaryChart);
google.charts.setOnLoadCallback(drawChart);

function drawSummaryChart() {   
	
	var series_type_plants_target = 'bar';
	var series_type_yield_target = 'bar';
	var isInterActivityEnabled = true;

	// Draw legend & target lines
	var gs_id = Number($("#growth_stage_id").val())
	if (gs_id != 5 ) {
		series_type_plants_target = 'line';	
		isInterActivityEnabled = false;
		colColor = '#000';	
	} else {
		series_type_yield_target = 'line';
		colColor = '#f00';
	}
	
	var data = google.visualization.arrayToDataTable(JSON.parse($('#chartAverages').val()));
	/*
	var zones = data.getNumberOfRows();
	var hTicks = "[";
	var zoneName = '';
	for (var x = 0 ; x < zones ; x++){
		zoneName = data.getValue(x, 0);
		hTicks += "{v:"+x+", f:'"+zoneName+"'},";
	}
	hTicks = hTicks.substr(0, hTicks.length-1);
	hTicks += "]";
	*/
	var options = {	
		// chart border
		backgroundColor: {
			stroke: '#777777',
			strokeWidth: 4 
		},
		hAxis:  {
			title: JSON.parse($('#hAxisTitle').val()),
			gridlines: { count: JSON.parse($('#gridLines').val()) },
			//ticks: hTicks,
		},
		
		vAxis:  { 
				textStyle: {
				color: '#000000',
				fontSize: 18,
				fontName: 'Helvetica',
				bold: true,
				italic: false				
			},
			baseline: 0,	
			chxs: '0N*f0*', 
			format: '0',
			gridlines: { count: -1 },
		},
		series: { 1 : { type: series_type_plants_target,
						lineWidth: 3,
						color: colColor,
						enableInteractivity: isInterActivityEnabled },
				  4 : { type: series_type_yield_target,
						lineWidth: 3,
						color: '#000',
						enableInteractivity: false },
				},		
		//width: '100%',
		//height: 300,		
		legend: { position: 'top' },
		trendlines: { 
				0: {
					type: 'linear'
				} 
			}, // draw a trendline for data series 0
			/*
		chartArea: {
			top: 40,
			left: 65,
			width: '90%',			
			height: '70%'
		} 
*/            
	};
	
	var chartSummary = new google.visualization.ColumnChart(document.getElementById('chart_summary'));
	// Wait for the chart to finish drawing before calling the getImageURI() method.
	google.visualization.events.addListener(chartSummary, 'ready', function () {
		var imgSummaryUri = chartSummary.getImageURI();
		// do something with the image URI:            
		$('#chartSummaryURI').val(imgSummaryUri);            
	});
	google.visualization.events.addListener(window, 'resize', function () {
		chartSummary.draw(data, options);
	});	
	chartSummary.draw(data, options);
}

function drawChart() {    
	//var data = google.visualization.arrayToDataTable(JSON.parse($('#chartData').val())); 
	var data = google.visualization.arrayToDataTable(JSON.parse($('#chartData').val())); 	
	
	var options = {
		
		// chart border
		backgroundColor: {
			stroke: '#777777',
			strokeWidth: 4 
		},
		//curveType: 'function', // add smooth lines
		//legend: { top: 30, right: 20 },
		hAxis:  { 
			
			title: JSON.parse($('#hAxisTitle').val()),
			/*
			titleTextStyle: {
				color: '#333333',
				fontSize: 16,
				fontName: 'Helvetica',
				bold: true,
				italic: false
			},
			 */
			gridlines: { count: JSON.parse($('#gridLines').val()) },
			//gridlines: { count: JSON.parse($('#plotCount').val()) },
		},
		vAxis:  { 
			/*
			title: JSON.parse($('#vAxisTitle').val()),
			*/
			textStyle: {
				color: '#000000',
				fontSize: 18,
				fontName: 'Helvetica',
				bold: true,
				italic: false
			},
			/*
			titleTextStyle: {
				color: '#333333',
				fontSize: 16,
				fontName: 'Helvetica',
				bold: true,
				italic: false
			},
			*/
			//viewWindowMode:'explicit', // deprecated
			//viewWindowMode:'maximised',			
			//viewWindowMode:'pretty',
			baseline: 0,
			/*
			viewWindow:{
				max: JSON.parse($('#vAxisMin').val()),
				min: JSON.parse($('#vAxisMax').val())
			},
			*/
			chxs: '0N*f0*', 
			format: '0',
			gridlines: { count: -1 },
		}, 
		// Line chart series    
/*		series: {
			0: { lineWidth: 2, pointShape: 'circle', pointSize: 2 },
			1: { lineWidth: 2, pointShape: 'circle', pointSize: 2 },						
			2: { lineWidth: 2, pointShape: 'circle', pointSize: 2 },
			3: { lineWidth: 2, pointShape: 'circle', pointSize: 2 },
			4: { lineWidth: 2, pointShape: 'circle', pointSize: 2 },
			5: { lineWidth: 2, pointShape: 'circle', pointSize: 2 },                    
		},*/ 
		//colors: ['#01579B', '#64DD17', '#D50000', '#FFD600', '#F57F17', '#00BFA5'],
		//width: '100%',
		/*
		backgroundColor: {
			fill: '#ffffff',
			stroke: '#666',
			strokeWidth: '3px'
		},
		 */
		//height: 300,
		legend: { position: 'none' },
		/*
		title: JSON.parse($('#vAxisTitle').val()),
		titleTextStyle: {
				color: '#000000',
				fontSize: 18,
				fontName: 'Helvetica',
				bold: true,
				italic: false
		},
		*/
		/*
		chartArea: {
			top: 40,
			left: 65,
			width: '90%',			
			height: '70%'
		} 
        */ 
	};


	
	//var chart = new google.charts.Bar(document.getElementById('chart'));
	var chart = new google.visualization.ColumnChart(document.getElementById('chart'));
	// line chart	
	/*	var chart_div = document.getElementById('chart')
		var chart = new google.visualization.LineChart(chart_div); 
	*/
	
	// Wait for the chart to finish drawing before calling the getImageURI() method.
	google.visualization.events.addListener(chart, 'ready', function () {
		var imgUri = chart.getImageURI();
		// do something with the image URI:            
		$('#chartURI').val(imgUri);            
	});
	google.visualization.events.addListener(window, 'resize', function () {
		chart.draw(data, options);
	});	
	chart.draw(data, options);
}

$(window).resize(function(){
	drawSummaryChart();
	drawChart();
  
});