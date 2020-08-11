 const initObj = {
     method: 'GET'
 }
async function getMatches() {
    let d = new Date();
    let s_d = d.toISOString().split('T')[0];
    
    let req = new Request('data-for-AJAX.php' + '?DATE=' + s_d, initObj);
    const resp = await fetch(req);
    return resp.json();
}

