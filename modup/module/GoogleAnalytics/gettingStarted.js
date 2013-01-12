// Load the Google data JavaScript client library.
google.load('gdata', '2.x', {packages: ['analytics']});
google.load("visualization", "1", {packages:["corechart"]});
 
var selectedBtn = 0;

var feedData = new Array();
feedData.push([['ga:date'],['ga:visits'],[],[],[],'chart']);
feedData.push([['ga:pageTitle'],['ga:visits'],[],[],['-ga:visits'],'table']);


var _dimensions = feedData[0][0];
var _metrics = feedData[0][1];
var _segment = feedData[0][2];
var _filters = feedData[0][3];
var _sort = feedData[0][4];
var _style = feedData[0][5];


// Set the callback function when the library is ready.
google.setOnLoadCallback(init);

/**
 * This is called once the Google Data JavaScript library has been loaded.
 * It creates a new AnalyticsService object, adds a click handler to the
 * authentication button and updates the button text depending on the status.
 */
function init() {
  myService = new google.gdata.analytics.AnalyticsService('gaExportAPI_acctSample_v2.0');
  scope = 'https://www.google.com/analytics/feeds';
  var button = document.getElementById('_ga_authButton');

  // Add a click handler to the Authentication button.
  button.onclick = function() {
    // Test if the user is not authenticated.
    if (!google.accounts.user.checkLogin(scope)) {
      // Authenticate the user.
      google.accounts.user.login(scope);
    } else {
      // Log the user out.
      google.accounts.user.logout();
      getStatus();
    }
  }
  getStatus();
}

/**
 * Utility method to display the user controls if the user is 
 * logged in. If user is logged in, get Account data and
 * get Report Data buttons are displayed.
 */
function getStatus() {
 // var getAccountButton = document.getElementById('getAccount');
 // getAccountButton.onclick = getAccountFeed;
  
  //var getDataButton = document.getElementById('getData');
  //getDataButton.onclick = getDataFeed;

  var gaPanel = document.getElementById('_ga_panel');
  var loginButton = document.getElementById('_ga_authButton');
  
  if (!google.accounts.user.checkLogin(scope)) {
    gaPanel.style.display = 'none';   // hide control div
    loginButton.innerHTML = 'Access Google Analytics';
  } else {
    gaPanel.style.display = 'block';   // hide control div
    loginButton.innerHTML = 'Logout from Google Analytics account';
    
    if (document.getElementById('tableId').innerHTML) {
    	getDataFeed();
    };
    
  }
}

/**
 * Main method to get account data from the API.
 */
function getAccountFeed() {

  document.getElementById('_ga_outputDiv').innerHTML = 'Loading...';

  var myFeedUri =
      'https://www.google.com/analytics/feeds/accounts/default?max-results=100';
  myService.getAccountFeed(myFeedUri, handleAccountFeed, handleError);
}

/**
 * Handle the account data returned by the Export API by constructing the inner parts
 * of an HTML table and inserting into the HTML file.
 * @param {object} result Parameter passed back from the feed handler.
 */
function handleAccountFeed(result) {
  // An array of analytics feed entries.
  var entries = result.feed.getEntries();

  // Create an HTML Table using an array of elements.
  var outputTable = ['<table><tr>',
                     '<th>Account Name</th>',
                     '<th>Profile Name</th>',
                     '<th>Profile ID</th>',
                     '<th>Table Id</th></tr>'];

  // Iterate through the feed entries and add the data as table rows.
  for (var i = 0, entry; entry = entries[i]; ++i) {

    // Add a row in the HTML Table array for each value.
    var acc_nm = entry.getPropertyValue('ga:AccountName');
    var acc_tt = entry.getTitle().getText();
    var acc_id =  entry.getTableId().getValue();
    
    var row = [
      acc_nm, acc_tt, acc_id,
      '<a href=?acc_nm=' + acc_nm +'&acc_tt='+ acc_tt +'&acc_id=' + acc_id + '>Select</a>'
    ].join('</td><td>');
    
    
    outputTable.push('<tr><td >', row, '</td></tr>');
  }
  outputTable.push('</table>');

  // Insert the table into the UI.
  document.getElementById('_ga_outputDiv').innerHTML =
      outputTable.join('');
}

/**
 * Main method to get report data from the Export API.
 */
function getDataFeed() {

 document.getElementById('_ga_outputDiv').innerHTML = 'Loading...';

var sd = document.getElementById('from').value.split('/');
var ed = document.getElementById('to').value.split('/');
var sdate = sd[2] + '-' + sd[0] + '-' + sd[1];
var edate = ed[2] + '-' + ed[0] + '-' + ed[1];

var myFeedUri = 'https://www.google.com/analytics/feeds/data' +
    '?start-date='+ sdate +
    '&end-date=' + edate +
    '&dimensions=' + _dimensions +
    '&metrics=' + _metrics +
    '&segment=' + _segment +
    '&filters=' + _filters +
    '&sort=' + _sort +
    '&max-results=365' +
    '&ids=' +  document.getElementById('tableId').innerHTML;

  myService.getDataFeed(myFeedUri, handleDataFeed, handleError);
}

/**
 * Handle the data returned by the Export API by constructing the 
 * inner parts of an HTML table and inserting into the HTML File.
 * @param {object} result Parameter passed back from the feed handler.
 */
function handleDataFeed(result) {
 
 // An array of Analytics feed entries.
 var entries = result.feed.getEntries();


	if(_style == 'table') { 
 		// Create an HTML Table using an array of elements.
 		var outputTable = ['<table><tr>',
 	                   '<th>Table 1</th>',
 	                   '<th>Table 2</th></tr>'];

 		 for (var i = 0, entry; entry = entries[i]; ++i) {
		     var row = [
		       entry.getValueOf(_dimensions[0]),
 		      entry.getValueOf(_metrics[0])
 	    	].join('</td><td>');
 	    	outputTable.push('<tr><td>', row, '</td></tr>');
 	  	}
 	  	outputTable.push('</table>');
	 	 document.getElementById('_ga_outputDiv').innerHTML = outputTable.join('');
	}
	else {

 	var data = new google.visualization.DataTable();
        data.addColumn('string', 'Date');
        data.addColumn('number', 'Visited');
        
         for (var i = 0, entry; entry = entries[i]; ++i) {
			var _date = entry.getValueOf(_dimensions[0]);
			_date
         	data.addRows([[_date.substr(4,2) + '/' + _date.substr(6,2), entry.getValueOf(_metrics[0])]]);
         };
        var chart = new google.visualization.AreaChart(document.getElementById('_ga_outputDiv'));
        chart.draw(data, {width: 750, height: 300, title: 'VISITED', legend:'none'
          });
 
	}
} //handleDataFeed end


/**
 * Alert any errors that come from the API request.
 * @param {object} e The error object returned by the Analytics API.
 */
function handleError(e) {
  var error = 'There was an error!\n';
  if (e.cause) {
    error += e.cause.status;
  } else {
    error.message;
  }
  alert(error);
}
