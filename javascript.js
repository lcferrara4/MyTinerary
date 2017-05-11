/**
 * javascript.js
 *
 * GETs data from getdetails.php (aka a list of place id's formatted as json)
 * Makes getDetails call to Google Place Details API
 * Sends results of API call to loaddetails.php, which updates data in our database
 *
 */


var PlaceIDRequest = new XMLHttpRequest();
PlaceIDRequest.onload = getGoogleData;
PlaceIDRequest.open('GET', 'http://dsg1.crc.nd.edu/cse30246/dat_base/getdetails.php');
PlaceIDRequest.setRequestHeader('Content-type', 'application/json');

PlaceIDRequest.send();

function getGoogleData(){
    var place_ids = JSON.parse(this.response.slice(this.response.indexOf('[')));
    console.log('GETTING GOOGLE DATA:', place_ids);
    place_ids.forEach(get_place_details);
}

function get_place_details(place_id){
    var service = new google.maps.places.PlacesService(document.getElementById('list'));
    var req_body = {placeId: place_id};
    service.getDetails(req_body, onGetDetails);
}

function onGetDetails(results, status){
    if(status == google.maps.places.PlacesServiceStatus.OK) {
        console.log("STATUS: ", status);
        console.log("RESULTS: ", results);
        var sendDetails = new XMLHttpRequest();
        sendDetails.addEventListener('load', function() { console.log(this.responseText) });
        sendDetails.open('POST', 'http://dsg1.crc.nd.edu/cse30246/dat_base/loaddetails.php', true);
        sendDetails.setRequestHeader('Content-type', 'application/json');
        sendDetails.send(JSON.stringify(results));
    }
}
