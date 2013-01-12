<?php
	$sDate = date("m/d/Y", strtotime("-7 days"));
	$eDate =  date("m/d/Y");
?>

<style>

#_ga_authButton {
	margin-top: 10px;
}

#_ga_panel {
	display: none;
	width: 100%;
	height: 100%;
	margin: 0;
	padding: 0px;
	border: 1px solid #6087FF;
	font: 12px arial,helvetica,verdana;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
}

#_ga_header {
	margin: 0;
	padding: 5px 5px 15px 5px;
	color: #7F7F7F;
}

#_ga_header span { font-weight: bold; color: #0F0F0F;}

#_ga_ctrl {
	float: left;
	width: 300px;
	height: 38px;
	margin: 6px 0 0 0;
}

#_ga_ctrl ul {
	list-style: none;
}

#_ga_ctrl li {
	float: left;
	display: block;
	margin: 0 0 0 8px;
	padding: 5px;
	background: #EFEFEF;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
	cursor: pointer;
}

#_ga_ctrl li:hover{ background: #DFEEFF }


#_ga_date {
	float: right;
	margin: 0;
	padding: 5px;
}

#_ga_date input {
	width: 120px;
	font-size: 17px;
	border: 1px solid #DFDFDF;
}

#_ga_date input:hover {
	border-color: #6FA2FF;
}

#_ga_outputDiv {
	clear: both;
	min-height: 300px;
	margin: 0;
	padding: 15px 5px 5px 10px;
}

#_ga_outoutDiv table {
	width: 100%;
	
}

#_ga_outputDiv table, #_ga_outputDiv th, #_ga_outputDiv td {
	border:1px solid;
	padding: 5px;
}


#_ga_footer {
	margin: 0;
	padding: 5px;
	background: #6FA2FF;
	font-size: 11px;
}
	
</style>

<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript" src="/modup/module/GoogleAnalytics/gettingStarted.js"></script>

<script type="text/javascript">


	//  google.load("visualization", "1", {packages:["corechart"]});
    //  google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Year');
        data.addColumn('number', 'Sales');
        data.addColumn('number', 'Expenses');
        data.addRows([
          ['2004', 1000, 400],
          ['2005', 1170, 460],
          ['2006', 660, 1120],
          ['2007', 1030, 540]
        ]);

        var chart = new google.visualization.AreaChart(document.getElementById('_ga_outputDiv'));
        chart.draw(data, {width: 400, height: 240, title: 'Company Performance', 
                          hAxis: {title: 'Year', titleColor:'#FF0000'}
          });
      }


$(function() {
	var dates = $('#from, #to').datepicker({
		defaultDate: "+1w",
		changeMonth: true,
		numberOfMonths: 1,
		onSelect: function(selectedDate) {
			var option = this.id == "from" ? "minDate" : "maxDate";
			var instance = $(this).data("datepicker");
			var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
			dates.not(this).datepicker("option", option, date);
		}
	});
	
	 
	$('li', "#_ga_ctrl").each(function(index) {
		
		if (index == selectedBtn) {
			$(this).css('background', '#6FA2FF');
		};
		
		$(this).bind('click', function() {
			if (index != selectedBtn) {
				_dimensions = feedData[index][0];
				_metrics = feedData[index][1];
				_segment = feedData[index][2];
				_filters = feedData[index][3];
				_sort = feedData[index][4];
				_style = feedData[index][5];
				
				$('li:nth-child('+ (selectedBtn+1) +')', "#_ga_ctrl").css('background', '#DFEEFF');
				$(this).css('background', '#6FA2FF');
				selectedBtn = index;
				getDataFeed();
			}
		});
	
	});

});





</script>
    <h1>Google Analytics (alpha)</h1>
    <div id="_ga_panel">
    	<div id="_ga_header"></div>
    	
    	<div id="_ga_ctrl">
    		<ul>
    			<li>Visited</li>
    			<li>Most viewed</li>
    		</ul>
    	</div>
    	<div id="_ga_date">
    	   	<label for="from">From</label>
			<input type="text" id="from" name="from" value="<? echo $sDate ?>"/>
			<label for="to">to</label>
			<input type="text" id="to" name="to" value="<? echo $eDate ?>"/>
			<button id="_ga_apply" onClick="getDataFeed()">Apply</button>    
    	</div>
    	
    	
		<div id="_ga_outputDiv"></div>
		<div id="_ga_footer">Profile: <span id="profile"></span> | Table ID: <span id="tableId"></span></div>
    </div>

	<button id="_ga_authButton">Loading...</button>    
    <img src="dummy.gif" style="display:none" alt="required for Google Data"/>
<?php

if (isset($_GET['acc_nm'])) {
		$arr = array('acc_nm' => $_GET['acc_nm'], 'acc_tt' => $_GET['acc_tt'], 'acc_id' => $_GET['acc_id']);
		Data::update('GoogleAnalytics', 'data', $arr);
		check_ga_acc();
}
else{
check_ga_acc();
}





function check_ga_acc() {
	$data = Data::query('GoogleAnalytics', 'data');
	
	echo '<script type="text/javascript">';
	
	if ($data['acc_id'] == null) {
		echo '$("#_ga_header").html("We could not locate your Google analytics account. Click <a href=# onClick=getAccountFeed()>here</a> to choose.");
		$("#profile").text("n/a");
		$("#tableId").text("n/a")';
	}
	else {
		echo '$("#_ga_header").html("<span>Hello '.$data['acc_nm'].'.</span><br>If you are not '.$data['acc_nm'].', Click <a href=# onClick=getAccountFeed()>here</a> to choose another account.");
		$("#profile").text("'.$data['acc_tt'].'");
		$("#tableId").text("'.$data['acc_id'].'")'; 
	}
	
	echo '</script>';
	
};


    /*$a = array( 'id' => null, 'pw' => null );
	Data::update('GoogleAnalytics', 'data', $a);*/

	
	//if (isset($_GET[
	

 
    //$some_variable = 'Glenn';
    
    //echo '<script type="text/javascript">$(function() { hello("'.$some_variable.'") });</script>';
  	
/*	
    $a = array(
        'id' => null, 'pw' => null
    );*/
    
    
	
	
	/*Data::update('HelloWorld', 'data', $a);
	
	$vv = Data::query('HelloWorld', 'data');
	var_dump($vv);
	echo $vv['id'];
	echo URI_PART_2;*/
	//echo $_GET['value'];
?>
