<?
$query = getQuery('admin','');
$content = mysql_fetch_assoc($query);

$query = mysql_query("SELECT news.date, news.content FROM news WHERE deleted = 0 ORDER BY date DESC");
$news = array();

while ($cRecord = mysql_fetch_assoc($query)) {
    $cRecord['content'] = strip_tags($cRecord['content']);
    $cRecord['content'] = htmlspecialchars($cRecord['content']);
    if ($cRecord['content'] != '') $news[] = $cRecord;

}

$query = mysql_query("SELECT books.picture FROM books WHERE deleted = 0 ORDER BY RAND()");
$books = array();
while ($cRecord = mysql_fetch_assoc($query)) {
    if ($cRecord['picture'] != '') $books[] = $cRecord;
    }

$login = getLogin();
?>

<div class='main-content'>
    <div>
        <p>
            <?if ($login):?>
                <img class='edit-button' id='edit' src='assets/img/edit-button.png'>
            <?endif;?>
            <?=$content['main']?>
        </p>
    </div>

<div id="pages">

<div class="page" id='page-news'>
    <h4>Останні новини</h4>
    <div class="page-in"><h5><?=strip_tags($news[3]['date'])?></h5><span><?=strip_tags($news[3]['content'])?></span></div>
    <div class="page-in"><h5><?=strip_tags($news[2]['date'])?></h5><span><?=strip_tags($news[2]['content'])?></span></div>
    <div class="page-in"><h5><?=strip_tags($news[1]['date'])?></h5><span><?=strip_tags($news[1]['content'])?></span></div>
    <div class="page-in"><h5><?=strip_tags($news[0]['date'])?></h5><span><?=strip_tags($news[0]['content'])?></span></div>
</div>

<div class="page" id='page-books'>
    <h4>Наші книги</h4>
    <div class="page-in-right"><img src='assets/img/books/<?=$books[3]['picture']?>'></div>
    <div class="page-in-right"><img src='assets/img/books/<?=$books[2]['picture']?>'></div>
    <div class="page-in-right"><img src='assets/img/books/<?=$books[1]['picture']?>'></div>
    <div class="page-in-right"><img src='assets/img/books/<?=$books[0]['picture']?>'></div>
</div>

</div>

<div class="img">
    <img src='assets/img/book_bl.jpg'>
    <h4>Видавництво &laquo;БаК&raquo;</h4>
    
</div>

</div>
<script src='assets/js/explorer.js'></script>
<script>
    document.addEventListener("DOMContentLoaded", ()=>{

    fade(document.querySelector('.main-content'), 300);
    const iteration = 25; // к-во ітерацій до мінімальної ширини
    const booksArray = [<?=json_encode($books)?>];
    const newsArray = [<?=json_encode($news)?>];

    class PageBrowser {
        constructor (selector, pos){
            this.position = pos;
            this.page = [];
            this.pageEl = [];
            let el = document.querySelectorAll(selector).forEach( (i)=>{this.pageEl.push(i)})
        }
        init (number){
            this.page = [
                {wStart: 100, wMin: 100, aStart: 0, aMax: 0},
                {wStart: 100, wMin: 90, aStart: 0, aMax: 5},
                {wStart: 90, wMin: 80, aStart: 5, aMax: 10},
                {wStart: 80, wMin: 0, aStart: 10, aMax: 25}
                ];

            for (let n = 0; n < 4; n++) {
                this.pageEl[n].style.width = `${this.page[n].wStart}%`;
                this.pageEl[n].style.zIndex = n;
                let skewSign = this.position == 'right' ? '' : '-';
                this.pageEl[n].style.transform = `skewy(${skewSign}${this.page[n].aStart}deg)`;
                this.pageEl[n].style.top = `${getShift(parseInt(getComputedStyle(this.pageEl[n]).width), this.page[n].aStart)}px`;
                if (this.position == 'right') {this.pageEl[n].style.left = 0} else this.pageEl[n].style.right = 0;
                this.page[n].w = this.page[n].wStart;
                this.page[n].a = this.page[n].aStart;
                this.page[n].da = (this.page[n].aMax - this.page[n].aStart) / iteration;
                this.page[n].dw = (this.page[n].wStart - this.page[n].wMin) / iteration;
                number++;
                if (number > 3) number = 0;
                }
            };

        browse (n){
            this.page[n].w -= this.page[n].dw;
            if (this.page[n].w < this.page[n].wMin) this.page[n].w = this.page[n].wMin;
            this.page[n].a += this.page[n].da;
            if (this.page[n].a > this.page[n].aMax) this.page[n].a = this.page[n].aMax;
            this.pageEl[n].style.width = `${this.page[n].w}%`;
            let skewSign = this.position == 'right' ? '' : '-';
            this.pageEl[n].style.transform = `skewy(${skewSign}${this.page[n].a}deg)`;
            this.pageEl[n].style.top = `${getShift(parseInt(getComputedStyle(this.pageEl[n]).width), this.page[n].a)}px`;
        }

        rotate (){
            let a = this.pageEl[3];
            this.pageEl.pop();
            this.pageEl.splice(0,0,a);
        }
    }

    function getShift(width, angle) { return (width / 2) * Math.sin(angle * (Math.PI / 180));}

    function rnd() {
        let exclusive = false;
        let n;
        while (exclusive == false) {
            n = Math.random() * booksArray[0].length;
            n = Math.round(n);
            exclusive = true;
            pageBooks.pageEl.forEach((i)=>{
                let s = i.firstElementChild.getAttribute('src');
                let x = s.lastIndexOf("/");
                if (s.substring(x+1, s.length) == booksArray[0][n].picture) exclusive = false;
            })
        }
        return n;
    }

    let pageBooks = new PageBrowser('#page-books .page-in-right', 'right');
    let pageNews = new PageBrowser('#page-news .page-in', 'left');

    let browseIndex = 0;
    let newsIndex = 4;
    let booksIndex = 4;
    pageBooks.init(browseIndex);
    pageNews.init(browseIndex);

    document.querySelector('#page-books').addEventListener('click', (event)=>{
        let i = 1;
        let interval = setInterval(()=>{
            pageBooks.browse(0);
            pageBooks.browse(3);
            pageBooks.browse(2);
            pageBooks.browse(1);
            i++;
            if (i > iteration) {
                clearInterval(interval);
                pageBooks.rotate();
                pageBooks.pageEl[0].innerHTML = `
                <img src='assets/img/books/${booksArray[0][rnd()].picture}'>`;
                booksIndex ++;
                browseIndex++;
                if(browseIndex > 3) browseIndex = 0;
                pageBooks.init(browseIndex);
            }
        }, 15)
    })

    document.querySelector('#page-news').addEventListener('click', (event)=>{
        if (newsIndex > newsArray[0].length - 1) return;
        let i = 1;
        let interval = setInterval(()=>{
            pageNews.browse(0);
            pageNews.browse(3);
            pageNews.browse(2);
            pageNews.browse(1);
            i++;
            if (i > iteration) {
                clearInterval(interval);
                pageNews.rotate();
                pageNews.pageEl[0].innerHTML =
                `<h5>${newsArray[0][newsIndex].date}</h5><span>${newsArray[0][newsIndex].content}</span>`;
                newsIndex++;
                browseIndex++;
                if(browseIndex > 3) browseIndex = 0;
                pageNews.init(browseIndex);
                rnd(pageNews.page, newsArray);
            }
        }, 15)
    })

}) // onload
</script>