const initObj = {
    method: 'GET',
    headers: {
        'x-rapidapi-host': 'api-football-v1.p.rapidapi.com',
        'x-rapidapi-key': 'db96f2fc0emshaf33007386630a2p16e5fbjsn09ecad2e38ae',
        'useQueryString': 'true'
    }
}

const base_fixtures_url = 'https://api-football-v1.p.rapidapi.com/v2/fixtures/date/'

async function getMatchesFromSource(d) {
    let s_d = d.getFullYear().toString() + '-' + (d.getMonth() + 1).toString() + '-' + d.getDate();
    let req = new Request(base_fixtures_url + s_d + '?timezone=America%2FAnchorage', initObj);
    let resp = await fetch(req);
    let data = resp.json();
    return data;
}

function fixture_func(item) {
    const cell_datas = [
        {  
            ih : item.league.country,
            s : item.league.flag,
        },
        {
            ih : item.league.name,
            s : item.league.logo,
        },
        {
            ih : item.homeTeam.team_name,
            s : item.homeTeam.logo,
        },
        {
            ih : item.awayTeam.team_name,
            s : item.awayTeam.logo
        }
    ];

    let tably = document.getElementById("scoreboard_table");
    let tbod = tably.tBodies[0];
    let new_row = tbod.insertRow(-1);

    cell_datas.forEach((cd, i) => {
        let nc = new_row.insertCell(i);
        let ci = document.createElement('img');
        let cf = document.createElement('figure');
        let cfig = document.createElement('figcaption');
        cfig.innerHTML = cd.ih;
        ci.src = cd.s;
        cf.appendChild(ci);
        cf.appendChild(cfig);
        nc.appendChild(cf);
    });

    let nc_4 = new_row.insertCell(4);
    nc_4.innerHTML = item.score.fulltime;
}

function getMatchesSetTableBody(the_day, t) {
    t.caption.innerHTML = "Games for " + the_day.toDateString();
    let new_tb = document.createElement("tbody");
    new_tb.id = "scoreboard_table_body";
    if (t.tBodies.length > 0) {
        let tb = t.tBodies[0];
        t.removeChild(tb);
    }
    
    t.appendChild(new_tb);
    getMatchesFromSource(the_day).then(
        function (data) {
            let fixs = data.api.fixtures;
            fixs.sort((a, b) => (a.league_id < b.league_id) ? -1 : 1);
            let fixs_cut = fixs;//fixs.filter(f => f.league_id == 530);
            fixs_cut.forEach(fixture_func);
            console.log(data);
        }
    );
}
