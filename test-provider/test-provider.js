const deepstream = require( 'deepstream.io-client-js' );
const ds = deepstream( 'localhost:6020' );
var receivedEvents = [];

ds.login({}, (success, data ) => {
	if( success ) {
		console.log( 'test provider ready' )
	} else {
		console.log( data );
	}
});

ds.rpc.provide( 'times-two', ( data, response ) => {
	response.send( data * 2 );
});

ds.rpc.provide( 'reset-test-provider', ( data, response ) => {
	receivedEvents = [];
	response.send( 'OK' );
});

ds.rpc.provide( 'get-event-info', ( data, response ) => {
	response.send( receivedEvents );
});

ds.event.subscribe( 'test-event', ( data ) => {
	receivedEvents.push({name: 'test-event', data: data });
});