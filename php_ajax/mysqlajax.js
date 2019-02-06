// -------------------------------------------------
function queryGet(sqlQuery, action, phpPath=''){
  // возвращает результат sql запроса в виде JSON
  // sqlQuery - текст sql запроса на выборку
  // action - функция-обработчик результата запроса
  // phpPath - абсолютный путь к php файлу-обработчику ajax-запроса
// -------------------------------------------------
  let data = new FormData;
  data.append('body', sqlQuery);
  fetch(phpPath+'mysqlselect.php', {
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
  
// -------------------------------------------------
  function queryUpdate(table, where, fdata, action, phpPath = '') {
      // обновляет значения выбранного поля бд mysql
      // table - имя таблицы
      // where - выражение вида <field> = <value> для конструкции where sql запроса update
      // fdata - список полей и их значений для конструкции set, вида: [['полей], [значение1], [поле2], [значение2], ... [поле n], [значение n]]
      // поле, название которого начинается с #, будет считаться числовым. Нечисловые значения будут преобразованы в NULL.
// -------------------------------------------------
    let data = new FormData;
    data.append('$table', table);
    data.append('$where', where);
    fdata.forEach((i)=>{
      data.append(i[0], i[1]);
    })
    fetch(phpPath+'mysqlupdate.php', {
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
// -------------------------------------------------
  function queryInsert(table, fdata, action, phpPath = '') {
      // вставляет новую запись и заполняет ее значениями
      // table - имя таблицы
      // fdata - список полей и их значений для конструкции set, вида: [['поле1], [значение1], [поле2], [значение2], ... [поле n], [значение n]]
      // поле, название которого начинается символом '#', будет считаться числовым. Нечисловые значения будут преобразованы в NULL.
// -------------------------------------------------
    let data = new FormData;
    data.append('$table', table);
    fdata.forEach((i)=>{
      data.append(i[0], i[1]);
    })
    fetch(phpPath+'mysqlinsert.php', {
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

// -------------------------------------------------
  function queryDelete(table, where, action, phpPath = '') {
      // удаляет выбранную запись
      // table - имя таблицы
      // where - тело конструкции where
      // поле, название которого начинается символом '#', будет считаться числовым. Нечисловые значения будут преобразованы в NULL.
// -------------------------------------------------
    let data = new FormData;
    data.append('$table', table);
    data.append('$where', where);
    fetch(phpPath+'mysqldelete.php', {
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