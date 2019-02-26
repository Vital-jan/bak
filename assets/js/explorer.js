// версия 1.0.3-26.02.2019
const version = '1.0.3';

// Подключение:
// <script src="http://explorer.org.ua/framework/explorer.js"></script>
// Требует подключения:
// <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
// Для функций, требующих таблицу стилей:
// <link rel="stylesheet" href="http://explorer.org.ua/framework/explorer.css">

//=================================================================================
function writeRound(s, x, y, size, radius, start, sector, userStyle) {
  //=================================================================================
  // возвращает html-код для отрисовки текста по радиусу.
  // writeRound (<текст>,<коорд x>,<коорд y>,<разм. шрифта>,<радиус>,<нач. угол>,<сектор> [,<польз. css>])
  // s - строка текста;
  // x, y - координаты центра окружности базовой линии текста в px;
  // size - размер шрифта в px;
  // radius - радиус окружности базовой линии текста в px;
  // start - начальный угол поворота текста в deg;
  // sector - угол сектора окружности, в котором размещается текст;
  //          положительное значение - по часовой стрелке, и наоборот.
  // userStyle - необязательный параметр - пользовательский css стиль;
  var t = '';
  var direction = sector / Math.abs(sector);
  var charStyle = `
        position: absolute;
        text-align:center;
        width: ${size}px;
        height: ${size}px;
        font-size:${size}px;
        top: ${y + radius * -direction - size / 2}px;
        left: ${x - size / 2}px;
        transform-origin: ${size / 2}px ${radius * direction + size / 2}px;
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        `;
  var deg = start - (180 * (direction - 1)) / -2;
  var degPlus = (start + sector - start) / (s.length - 1);
  for (var n = 0; n < s.length; n++) {
    var charStyle2 = ` transform: rotate(${deg}deg);`;
    deg += degPlus;
    t += `<div style='${charStyle} ${charStyle2} ${userStyle}'>${s[n]}</div>`;
  }
  return t;
}

//=================================================================================
function starRateWrite(
  rate = 0,
  path = 'http://explorer.org.ua/framework/img/'
) {
  //=================================================================================
  // отображает рейтинг в виде частично закрашенных звезд по 5-бальной шкале
  // rate - рейтинг (число от 0 до 5 с 1 знаком после запятой)
  // path - путь к изображениям left.png и right.png
  // возвращает html-код
  function starRate(k, path) {
    // вспомагательная функция для starRateWrite
    // отрисовка 1-й звезды;
    // k - число от 0 до 1
    starDiv = `
            overflow: hidden;
            position: relative;
            display: block;
            height: 20px;
            `;
    starSpan = `
            position: relative;
            display: inline-block;
            width: 20px;
            float:left;
            `;
    starImg = `
            width: 20px;
            height: 20px;
            display: block;
            `;
    k *= 20;
    right = k;
    left = 20 - k;
    s = `
            <div style='width:${right}px; ${starDiv}'>
                <span style='${starSpan}'> <img style='${starImg}' src="${path}left.png">
                </span>
            </div>
            <div style='width:${left}px; ${starDiv}'>
                <span style='${starSpan} left:-${right}px;'> <img style='${starImg}' src="${path}right.png">
                </span>
            </div>
            `;

    return s;
  }
  var s = '';
  for (var i = 1; i <= 5; i++) {
    var x = 0;
    if (i <= rate) x = 1;
    if ((i > rate) & (i == Math.ceil(rate))) x = rate - Math.floor(rate); 
    s += starRate(x, path);
  }
  s = `<div style='
        display: flex;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        justify-content:center;
        width: 100px;
        height: 20px;
        position: relative;
    '>${s}</div>`;
  return s;
}
//========================================================================
function toNumber(a) {
  //преобразует строку в число, отбрасывая все ненужные символы перед и после цифр.
  //======================================================================
  // если строка не содержит цифр, возвращает NaN ()
  if (a.length == 0) return NaN;
  while (isNaN(parseFloat(a)) & (a.length > 0)) a = a.substring(1, a.length);
  a = parseFloat(a);
  return a;
}
// ==========================================================================
function numberToString(n, d = 0) {
  //возвращает строку в виде числа с разделителями групп разрядов
  // ========================================================================
  // numberToString (n [,d])
  // n - исходное число (вещественное), d - к-во знаков после десятичной точки, необязательный аргумент.

  if (isNaN(n)) return NaN;
  n = Math.round(n * Math.pow(10, d)) / Math.pow(10, d);
  n = n.toFixed(d);
  let end = d > 0 ? '.' + n.substr(n.length - d) : '';
  if (d > 0) n = n.substr(0, n.length - d - 1);
  return (
    n
      .split('')
      .reverse()
      .map((i, n) => (n % 3 == 0 ? `${i} ` : `${i}`))
      .reverse()
      .join('')
      .trim() + end
  );
}

// ========================================================================
function clearToNumber (s, minus = false, float = false) {
	// ====================================================================
	// убирает из строки все лишние символы, кроме цифр и спецсимволов
    // s - исходная строка
    // minus - допускаются ли отрицательные числа
    // decimal - допускаются ли вещественные числа
    let n = 0; // текущий символ
    let r = ''; // результат
    let dotAdd = float; // надо ли добавлять точку (добавляем только первую точку в строке если float = true)

    if (s[0] == '-') {
        n++;
        if (minus) r = '-';
    }
    
    while (n < s.length) {
        if (s[n] == '.' && float == true) {
            r += '.';
            float = false;
        }
        if (s.charCodeAt(n) >= 48 && s.charCodeAt(n) <= 57) r += s[n];
        n++;
    }

    return r == '' ? '0' : r;
}

//========================================================================
function rnd(min, max) {
  //======================================================================
  // возвращает случайное целое число между <min> и <max> включительно.
  return Math.round(Math.random() * (max - min) + min);
}

// =======================================================================
function digitalForward(n, d = 0) {
  // =====================================================================
  // возвращает строку текста из числа <n>, дополненного до <d> разрядов ведущими нулями.
  if (isNaN(n)) return '';
  let s = '' + n;
  while (s.length < d) s = '0' + s;
  return s;
}

// =======================================================================
function parseIntBack(s) {
  // =====================================================================
  // отбрасывает все символы перед числом и после числа
  // 'id120' возвратит 120
  // 'id120abc21' возвратит 120
  // 'abc' возвратит NaN
  for (x = 0; x < s.length; x++) if(!isNaN(+parseInt(s[x]))) return parseInt(s.substring(x, s.length));
  return NaN;
}

// =====================================================================
function slider(
// =====================================================================
  max,
  prefix,
  delay,
  fade,
  sliderId,
  sliderWidth,
  sliderHeight,
  title = [],
  arrowsVisible = true,
  circlesVisible = true
) {
  // аргументы: ==========================================================
  // --------------------------------------------
  // max - максимальный индекс рисунка (к-во изображений)
  // prefix - префикс имени файла изображения
  // delay - время показа изображения в секундах
  // fade - время набора 100% непрозрачности в % от delay
  // sliderId - идентификатор элемента-родителя;
  // sliderWidth - ширина слайдера в px
  // sliderHeight - ширина слайдера в px
  // title - подписи к картинкам (массив). если значение не указано, не отображается.
  // arrowsVisible, circlesVisible - видимость стрелок и кружочков
  // ----------------------------------------------------
  // Подключить таблицу стилей. При необходимости, можно дополнительно стилизовать каждый слайдер по его идентификатору.

  let slider = document.querySelector(`#${sliderId}`);
  if (slider == null) return;

  const fps = 25; // частота обновления, кадров/сек
  let counter = 1; // индекс текущего рисунка
  let prevCounter = 0; // индекс предыдущего рисунка
  let opacity = 0; // текущая прозрачность рисунка
  let timeNext = 0; // время смены рисунка
  let paused = false;

  // стили для slider
  slider.classList.add("slider");
  slider.style.width = `${sliderWidth}px`;
  slider.style.height = `${sliderHeight}px`;
  slider.style.position = 'relative';

  // обработчики для slider (пауза в прокрутке)
  slider.onmouseenter = () => {
    paused = true;
  };
  slider.onmouseleave = () => {
    paused = false;
  };

  // создаем и стилизуем графический контент
  let img = document.createElement('img');
  slider.appendChild(img);
  img.style.maxWidth = `${sliderWidth}px`;
  img.style.maxHeight = `${sliderHeight}px`;

  // создаем и стилизуем контейнер для элементов-кружочков
  let circles = document.createElement('div');
  circles.classList = 'circles';
  slider.appendChild(circles);
  if (!circlesVisible) circles.style.visibility = 'hidden';
  circles.style.width = `${sliderWidth}px`;
  circles.id = `${sliderId}-circles`;
  circles.addEventListener('click', function(event) {
    if (!isNaN(parseIntBack(event.target.id))) {
      prevCounter = counter;
      counter = parseIntBack(event.target.id);
    }
  });

  // создаем подпись под слайдером
  let ttl = document.createElement('h3');
  slider.appendChild(ttl);

  // добавляем элементы стрелка влево-вправо
  let arrow = [];
  for (n = 0; n < 2; n++) {
    arrow[n] = document.createElement('div');
    arrow[n].innerHTML =
      '<i style="transform: rotate(180deg)" class="material-icons"> arrow_forward_ios </i>';
    arrow[n].classList.add('arrow');
    if (!arrowsVisible) arrow[n].style.visibility = 'hidden';
    circles.appendChild(arrow[n]);
  }
  arrow[1].style.transform = 'rotate(180deg)'; // поворачиваем правую стрелку вправо

  // обработчики клика по стрелкам
  arrow[0].addEventListener('click', function() {
    if (counter > 1) {
      prevCounter = counter;
      counter--;
    }
  });
  arrow[1].addEventListener('click', function() {
    if (counter < max) {
      prevCounter = counter;
      counter++;
    }
  });

  var s = (sliderWidth / max) * 0.4; // вычисляем размер кружочка
  s = s > (sliderHeight / max) * 0.4 ? (sliderHeight / max) * 0.4 : s;

  // создаем, добавляем и стилизуем элементы-кружочки
  for (var n = 1; n <= max; n++) {
    var el = document.createElement('div');
    circles.insertBefore(el, arrow[1]);
    el.classList = 'circle';
    el.id = `${sliderId}-circle${n}`;
    el.style.width = `${s}px`;
    el.style.height = `${s}px`;
    el.style.borderWidth = `${Math.round(s / 4)}px`;
  }

  // таймер
  let interval = setInterval(function() {
    slider = document.querySelector(`#${sliderId}`); // проверяем наличие элемента slider в DOM
    if (!slider) {clearInterval(interval); return;};

    // активируем/деактивируем стрелки влево/право
    
    if (counter == max) {
      arrow[1].classList.add('no-active');
    } else {
      arrow[1].classList.remove('no-active');
    }
    if (counter == 1) {
      arrow[0].classList.add('no-active');
    } else {
      arrow[0].classList.remove('no-active');
    }
    img.src = `${prefix}${counter}.jpg`; // отрисовка изображения
    if (title[counter - 1] !== undefined) {
      ttl.innerHTML = title[counter - 1];
    } else {
      ttl.innerHTML = title[counter - 1] = '';
    }

    if (prevCounter != 0) {
      let elem = document.querySelector(`#${sliderId}-circle${prevCounter}`); // стираем предыдущий кружочек
      elem.style.backgroundColor = 'white';
    }

    elem = document.querySelector(`#${sliderId}-circle${counter}`); // рисуем текущий кружочек
    elem.style.backgroundColor = 'black';

    if (timeNext >= fps * delay) {
      // при достижении времени смены изображения:
      timeNext = 0;
      opacity = 0;
      prevCounter = counter;
      counter = counter < max ? counter + 1 : 1;
      img.setAttribute('src',`${prefix}${counter}.jpg`);
    }

    if (!paused) timeNext++; // если не наведен курсор - инкрементируем счетчик времени

    opacity = opacity < 1 ? opacity + 1 / (fps * fade) : 1; // увеличиваем яркость, пока не будет достигнута максимальная
    img.style.opacity = opacity;
    ttl.style.opacity = opacity;
  }, 1000 / fps);
}

// ==================================================================================
function modalWindow(
  caption,
  text,
  buttons = ['Ok'],
  action,
  windowWidth = 300,
  windowHeight = 300,
  color = '#000',
  bgColor = '#fff',
  clickAction = null,
  btnDefault = 0,
  formAction = ''
  ) {
  // ==================================================================================
  // Управляет отображением модальных окон
  // caption, text - заголовок и содержимое окна. Могут содержать html.
  // buttons - массив, содержащий подписи к кнопкам. Кол-во кнопок будет соответствовать длине массива.
  // action - функция, вызываемая после нажатия одной из кнопок. в качестве аргумента функция action принимает порядковый номер нажатойкнопки.
  // необязательные аргументы:
  // windowWidth, windowHeight - размеры окна (по умолч. 300px)
  // color - цвет текста и кнопки закрытия
  // bgColor - цвет фона окна.
  // clickAction - функция обработчик события click - позволяет встраивать другие управляющие элементы в тело модального окна
  // btnDefault - номер кнопки по умолчанию
  // в качестве аргументов caption, text и buttons может выступать html
  // если первый символ текста кнопки = "+" или "-" на кнопке отображается соотв. картинка

  const closeWindow = (w) => {
    // закрытие модального окна
    w.style.display = 'none';
    document.removeEventListener('keydown', escHandler);
    document.body.style.overflow = "scroll";
  }

  // если элемент "модальное окно" не существует, создаем и отображаем его.
  let modalWindow = document.querySelector('.modal-window');
  if (modalWindow === null) {
    modalWindow = document.createElement('div');
    modalWindow.classList.add('modal-window');
    document.body.appendChild(modalWindow);
    modalWindow.innerHTML = `<div class="modal"></div>`;
  }

  document.body.style.overflow = "hidden";
  modalWindow.style.display = "block";
  wnd = document.querySelector('.modal-window div.modal');
  wnd.style.width = windowWidth + 'px';
  wnd.style.color = color;
  wnd.style.backgroundColor = bgColor;
  wnd.style.height = windowHeight + 'px';

  // наполняем окно контентом
  wnd.innerHTML = `
  <div class="header">
  ${caption}
  <img src = "http://explorer.org.ua/framework/img/close_black.png" id="close">
  </div>`;
  wnd.innerHTML += `<div id="modal-window-content"></div>`;
  let pTeg = document.querySelector('#modal-window-content');
  pTeg.style.height = windowHeight - 120 + 'px';
  pTeg.innerHTML = text;
  wnd.innerHTML += '<div class="footer"></div>';
  let footer = document.querySelector('.modal-window div.modal div.footer');

  // добавляем кнопки
  for (let n = 0; n < buttons.length; n++) {
    let btnType = 'button';
    let s = buttons[n];
    if (s[0] == '+') {
      s = '<img src = "http://explorer.org.ua/framework/img/ok.png">' + s.slice(1, s.length);
      btnType = 'submit';
    }
    if (s[0] == '-') s = '<img src = "http://explorer.org.ua/framework/img/cancel.png">' + s.slice(1, s.length);
    
    footer.innerHTML += `<button type="${btnType}" id="modal-btn${n}">${s}</button>`;
  }

let btns = document.querySelectorAll('.footer button');
if (btnDefault <0 || isNaN(btnDefault) || btnDefault == null) btnDefault = 0;
if (btnDefault > buttons.length - 1) btnDefault = buttons.length - 1;
btns[btnDefault].focus();

// назначаем обработчик событий для кнопок
function btnHandler(e) {
  closeWindow(modalWindow);
  action(parseIntBack(e.currentTarget.id));
};
btns.forEach((i, n) => {
  i.addEventListener('click', btnHandler);
});

  // обработчик нажатия Esc
  document.addEventListener('keydown', escHandler);
  function escHandler(e)
  {
    if (e.keyCode == 27) {
      closeWindow(modalWindow);
      action(null);
    }
  };

  let firstItem = btns[0];
  let lastItem = btns[btns.length - 1];
  if (document.querySelector('#modal-window-content input') != null) firstItem = document.querySelector('#modal-window-content input');
  // обработчик tab для последней кнопки
  lastItem.addEventListener('keydown', function(e){
    if (e.keyCode == 9 && !e.shiftKey) {
        firstItem.focus();
        e.preventDefault();
      }
  });
  // обработчик tab для первой кнопки
  firstItem.addEventListener('keydown', function(e){
      if (e.keyCode == 9 && e.shiftKey) {
        lastItem.focus();
        e.preventDefault();
      }
    });

  document // обработчик клика для остальных эл-тов мод.окна
    .querySelector('.modal-window div.modal')
    .addEventListener('click', function() {
      if (clickAction != null) clickAction(event);
      if (event.target.id == "close") { // обработчик кнопки закрытия
        closeWindow(modalWindow);
        action(null);
      }
    });
}
// Пример:
// modalWindow('','Бажаєте запросити мене на співбесіду?' ,['+Так', '-Ні'], function (n){console.log('action');}, 300, 200, 'black', 'white', function(){})

// ==================================================================================
function runLine(text, el, time = 0) {
// ==================================================================================
  // реализует бегущую строку.
  // text - текст для отображения
  // el - элемент, в который внедряется бегущая строка
  // time - интервал в милисекундах, через который происходит сдвиг на 3px
  // Пример:
  // runLine('Сторінка створена без використання жодних фреймворків', document.querySelector('h1'), 20)
  // высота элемента, в который внедряем строку, должна быть определена в CSS.
          if (el == undefined) return;
          let stop = false;
  
          el.addEventListener('mouseenter', ()=> {stop = true});
          el.addEventListener('mouseleave', ()=> {stop = false});
  
          let interval;
          el.style.paddingLeft = 0;
          el.style.paddingRight = 0;
          el.style.overflow = 'hidden';
          el.style.position = 'relative';
          let maxWidth = el.getBoundingClientRect().width;
  
          let line = document.createElement('div');
          el.appendChild(line);
          line.style.display = 'inline-block';
  
          let width = 0;
          for (let n = 0; n < text.length; n++) {
              line.innerHTML = text[n];
              if (text[n] != ' ') {width += line.getBoundingClientRect().width} else width += width / n;
          }
  
          line.innerHTML = text;
          line.style.width = width + 'px';
          line.style.position = 'absolute';
          line.style.left = maxWidth + 'px';
  
          if (time == 0) {clearInterval(interval)} else interval = setInterval(()=>{
              let l = parseInt(line.style.left);
              if (l < -width) l = maxWidth;
              if(!stop) line.style.left = l - 3 +'px';
          }, time);
  }

  // -------------------------------------------------------------
  function fade(el, time, start = 0, finish = 1, fps = 30){
  // -------------------------------------------------------------
        // fade (el, time [,start, finish, fps])
        // el - элемент DOM
        // time - время затухания в мс
        // start, finish - начальное и конечное значение opacity (от 0 до 1),
        // - какое из этих значений больше определяет исчезновение или возникновение элемента.
        // fps - кадров в секунду.
                let opc = start;
                let opcPlus = (finish - start) / fps; // дельта opacity
                el.style.opacity = opc;
                let interval = setInterval(function() {
    
                    if (start < finish) {
                        if (opc < finish) {opc += opcPlus}
                    else {
                        opc = finish;
                        clearInterval(interval);
                       }
                    }
    
                    if (finish < start) {
                        if (opc > finish) {opc += opcPlus}
                    else {
                        opc = finish;
                        clearInterval(interval);
                        }
                    }
                    
                el.style.opacity = opc;
                }, time / fps);
    }
