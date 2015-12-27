/*globals jQuery, $, jsapi, google */

( function( $, plugin ) {
	"use strict";

	// Look at your Dev Tools console
	console.log( plugin );

	google.load( "visualization", "1", {
		packages : [ "corechart" ]
	} );
	google.setOnLoadCallback( function() {
		var container = document.getElementById( 'piechart' ),
			data = google
				.visualization
				.arrayToDataTable( plugin.exampleData ),
			chart = new google
				.visualization
				.PieChart( container );

		chart.draw( data, {
			title : 'My Daily Activities',
			is3D: true,
		} );
	} );
} )( jQuery, jsapi || {} );
