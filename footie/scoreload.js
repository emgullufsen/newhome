let t = document.getElementById("scoreboard_table");

let today = new Date();

let tomorrow = new Date();
tomorrow.setDate(today.getDate() + 1);

let yesterday = new Date();
yesterday.setDate(today.getDate() - 1);

getMatchesSetTableBody(today, t);

let button_div = document.getElementById("day_buttons");
let button_tomorrow = document.createElement('button');
let button_yesterday = document.createElement('button');

button_tomorrow.setAttribute('title', 'Next Day');
button_tomorrow.onclick = () => {
    [today, tomorrow, yesterday].map(d => d.setDate(d.getDate() + 1))
    getMatchesSetTableBody(today, t);
};
button_tomorrow.appendChild(document.createTextNode("Next Day \u27a1"));

button_yesterday.setAttribute('title', 'Previous Day');
button_yesterday.onclick = () => {
    [today, tomorrow, yesterday].map(d => d.setDate(d.getDate() - 1))
    getMatchesSetTableBody(today, t);
};
button_yesterday.appendChild(document.createTextNode("\u2b05 Previous Day"));

button_div.appendChild(button_yesterday);
button_div.appendChild(button_tomorrow);