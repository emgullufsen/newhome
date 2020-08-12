const initObj = {
    method: 'GET',
    headers: {
        'x-rapidapi-host': 'api-football-v1.p.rapidapi.com',
        'x-rapidapi-key': 'db96f2fc0emshaf33007386630a2p16e5fbjsn09ecad2e38ae',
        'useQueryString': 'true'
    }
}

const base_fixtures_url = 'https://api-football-v1.p.rapidapi.com/v2/fixtures/date/'

async function getMatchesFromSource(d = new Date()) {
    let s_d = d.toISOString().split('T')[0];

    let req = new Request(base_fixtures_url + s_d, initObj);
    let resp = await fetch(req);
    let data = resp.json();
    return data;
}
