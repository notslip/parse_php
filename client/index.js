async function checkData() {
    const url = "../server/controller.php";
    let response = await fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/json;charset=utf-8'},
        body: JSON.stringify({check:1})
    });
    if (response.ok) {
        let json = await response.json();
        if(json.check === "true"){
            let event = new Event("check", {bubbles: true});
            document.dispatchEvent(event);
        }
        else {document.querySelector('table').innerHTML = `<h1>идет парсинг таблицы, обновите страницу через минуту</h1>`;}
    } else {alert("Ошибка HTTP: " + response.status);}
}
async function getData(d) {
    const url = "../server/controller.php";
    let response = await fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/json;charset=utf-8'},
        body: JSON.stringify({request: d})
    });
    if (response.ok) {
        let json = await response.json();
        if(typeof json.data !== 'undefined'){return  json;}
            else {document.querySelector('table').innerHTML = `<h1>ошибка в получении данных</h1>`;}
        }else {alert("Ошибка HTTP: " + response.status);}
}
function createResult(result){
    const div = document.getElementById("result");
    div.innerHTML="";
    let s =document.createElement('span');
    s.textContent="Количесво товаров на складе: "+result.sum+" ";
    div.append(s)
    let c =document.createElement('span');
    c.textContent="Средняя стоимость розничной цены товара: "+result.medium_cost+" ";
    div.append(c)
    let w =document.createElement('span');
    w.textContent="Средняя стоимость оптовой цены товара: "+result.medium_cost_w+" ";
    div.append(w)
}
function createTable(json){
    const table = document.querySelector('table');
    table.innerHTML="";
    const tr = document.createElement('tr');
    for (const title in json.title) {
        const th = document.createElement('th');
        th.textContent = json.title[title];
        tr.appendChild(th);
    }
    const th = document.createElement('th');
    th.textContent = "Примечание";
    tr.appendChild(th);
    table.appendChild(tr);
    for ( let i in json.data) {
        const tr = document.createElement('tr');
        for(let j in json.data[i] ){
            const td = document.createElement('td');
            td.textContent = json.data[i][j];
            tr.appendChild(td);
        }
        const td = document.createElement('td');
        if(json.result.attention.includes(json.data[i].id)){
            td.textContent = "Осталось мало!! Срочно докупите!!!";
        }
        else {
            td.textContent = "";
        }
        tr.appendChild(td);
        if(json.data[i].id === json.result.id_max_cost){
            tr.style.backgroundColor="red"
        }
        else if(json.data[i].id === json.result.id_min_cost){
            tr.style.backgroundColor="green"
        }
        table.appendChild(tr);
    }
}
async function handlerFilter(e){
    e.preventDefault();
    let form=document.getElementById("filter");
    let filter_data={
        "price": form.price.value,
        "min_price": form.min_price.value,
        "max_price": form.max_price.value,
        "quantity": form.quantity.value,
        "number_quantity": form.number_quantity.value,
    };
    let json = await getData(filter_data);
    createTable(json);
    createResult(json.result);
}
document.addEventListener("DOMContentLoaded", checkData);
document.addEventListener("check", handlerFilter);
document.addEventListener("submit", handlerFilter);
