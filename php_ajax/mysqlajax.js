
function sendMail (mailTo, subj, message, from, action, phpPath = '') {
// -------------------------------------------------
    // отправляет email
    // параметр from передает заголовок "от кого", отображаемый в почтовом клиенте (ключевое слово "From: " добавляется автоматически)
    // action() - функция-обработчик результата запроса
    // [phpPath] - абсолютный путь к php файлу-обработчику ajax-запроса без слеша в конце
  let data = new FormData;
    data.append('mail_to', mailTo);
    data.append('subj', subj);
    data.append('message', message);
    data.append('from', from);

    fetch(phpPath+'sendmail.php', {
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
function ajax(query, action, phpPath=''){
  // возвращает результат fetch запроса в виде JSON
  // query - тело ajax запроса
  // action - функция-обработчик результата запроса
  // [phpPath] - абсолютный путь к php файлу-обработчику ajax-запроса без слеша в конце
// -------------------------------------------------
  let data = new FormData;
  data.append('body', query);
  fetch(phpPath, {
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
function upLoad(file, action, phpPath='', type = '', size = 0){
  // возвращает JSON вида: {filename, error, upload} при успешном - error=0, upload=true.
  // error == 1: превышен размер файла; error == 2: нарушен тип файла
  // file - объект files[0], возвращаемый input type='file'
  // action - функция-обработчик результата запроса
  // phpPath - абсолютный путь к php файлу-обработчику ajax-запроса без слеша в конце
  // type - проверка типа файла. Если не задан - не проверяется.
  // size - ограничение размера файла. Если не задан - не проверяется.
// -------------------------------------------------
  let data = new FormData;
  data.append('file', file);
  data.append('type', type);
  data.append('size', size);

  fetch(phpPath+'upload.php', {
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
function queryGet(sqlQuery, action, phpPath=''){
  // возвращает результат sql запроса в виде JSON
  // sqlQuery - текст sql запроса на выборку
  // action - функция-обработчик результата запроса
  // phpPath - абсолютный путь к php файлу-обработчику ajax-запроса без слеша в конце
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
    // fdata - список полей и их значений для конструкции set, вида: [['#поле1'], [значение1], ['поле2'], [значение2], ... ['поле n'], [значение n]]
    // поле, название которого начинается символом '#', будет считаться числовым. Нечисловые значения в этом случае будут преобразованы в NULL.
    // action - функция-обработчик результата запроса
    // phpPath - абсолютный путь к php файлу-обработчику ajax-запроса без слеша в конце
    
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
} // queryUpdate

// ----------- Пример: ------------

//   let form = document.forms.admin.elements;
//   document.querySelector('#save').addEventListener('click',()=>{
    //     queryUpdate('admin', `admin_id=1`, [
        //     ['company', form.company.value],
        //     ['address',form.address.value],
        //     ['phone',form.phone.value],
        //     ['phone',form.phone.value],
        //     ['email',form.email.value],
        //     ['about',form.about.value],
        //     ['main',form.main.value],
        //     ['footer',form.footer.value]
        // ], 
        // updateAdmin, '<?=PHP_PATH?>');
        
        // function updateAdmin(response) {
            // if (!response.sql) alert('Помилка! Інформація не збережена.'+response.query+response.error);
            // }
            // })
            // response.sql = true - в случае успешного запроса mysql
            //                 false - если where == '' или неуспешного запроса mysql
            // response.query - содержит тело sql запроса
            // response.error - содержит текст ошибки mysql
            
// -------------------------------------------------
function queryInsert(table, fdata, action, phpPath = '') {
    // вставляет новую запись и заполняет ее значениями
    // table - имя таблицы
    // fdata - список полей и их значений для конструкции set, вида: [['поле1], [значение1], [поле2], [значение2], ... [поле n], [значение n]]
    // поле, название которого начинается символом '#', будет считаться числовым. Нечисловые значения будут преобразованы в NULL.
    // action - функция-обработчик результата запроса
    // phpPath - абсолютный путь к php файлу-обработчику ajax-запроса без слеша в конце
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
    
} // queryInsert

// -------------------------------------------------
function queryDelete(table, where, action, phpPath = '') {
    // удаляет выбранную запись
    // table - имя таблицы
    // where - тело конструкции where
    // action - функция-обработчик результата запроса
    // phpPath - абсолютный путь к php файлу-обработчику ajax-запроса без слеша в конце
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

  } // queryDelete

  // -------------------------------------------------
  function queryDelFile(fileName, action, phpPath = '') {
// -------------------------------------------------
if (!fileName) return false;

      let data = new FormData;
      data.append('file', fileName);

      fetch(phpPath+'del_file.php', {
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
    function htmlEncode(str) {
// -------------------------------------------------
// убирает спецсимволы и возвращает строку в том виде, в котором она отображается браузером.
// Применяется для полей input, значение которых получено из БД.
    while (str.search('<br>') != -1) str = str.replace('<br>','~~~');
    while (str.search('<br/>') != -1) str = str.replace('<br/>','~~~');
    let el = document.querySelector('#html-encode-element');
        if (!el) {
            el = document.createElement('div');
            el.id = 'html-encode-element';
            el.style.display = 'none';
            el.innerHTML = str;
        }
    str = el.innerText;
    while (str.search('~~~') != -1) str = str.replace('~~~', '\n');
    return str;
    }