let php_path = ''; // - путь к файлам php
// -------------------------------------------------

function queryGet(sqlQuery, action){
  // возвращает результат sql запроса в виде JSON
  // file - php файл-обработчик запроса
  // sqlQuery - текст sql запроса на выборку
  // action - функция-обработчик результата запроса
  let data = new FormData;
  data.append('body', sqlQuery);
  fetch(php_path+'mysqlselect.php', {
      method: "POST",
      body: data
    }) 
      .then(function(response){
          if (response.status == 200) {}// удачный ajax запрос
           else {}// неудачный ajax запрос
          return response.json();
      })
      .then(action)
      .catch(function(error) {
          alert('Error!' + error)
      });
  } // function
  
  function queryUpdate(table, where, fdata, action) {
    let data = new FormData;
    data.append('$table', table);
    data.append('$where', where);
    fdata.forEach((i)=>{
      data.append(i.key, i.value);
    })
    fetch(php_path+'mysqlupdate.php', {
      method: "POST",
      body: data
    }) 
      .then(function(response){
          if (response.status == 200) {}// удачный ajax запрос
           else {}// неудачный ajax запрос
          return response.json();
      })
      .then(action)
      .catch(function(error) {
          alert('Error!' + error)
      });

  }