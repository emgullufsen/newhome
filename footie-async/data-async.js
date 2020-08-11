 const europaURL = 'https://api-football-v1.p.rapidapi.com/v2/fixtures/league/514/';

 const initObj = {
     method: 'GET',
     headers: {
         'x-rapidapi-host': 'api-football-v1.p.rapidapi.com',
         'x-rapidapi-key': 'db96f2fc0emshaf33007386630a2p16e5fbjsn09ecad2e38ae',
         'useQueryString': 'true'
     }
 }
function getMatches() {
    let d = new Date();
    let s_d = d.toISOString().split('T')[0];
    
    let req = new Request('data-for-AJAX.php' + '?DATE=' + s_d, initObj);
    fetch(req).then(function(resp) {
        console.log(req);
    });
}

getMatches();