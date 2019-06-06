<? // відображення списку книг
            // echo "<div class='book-right'>";
            if (isset($books)) {
                $photo_folder = BOOK_PHOTO_FOLDER;
                foreach($books as $key=>$value){
                    $btns = ''; // кнопки edit та del
                    if ($login) $btns = "<img id='book-edit' data-id='{$value['book_id']}' class='edit-button' src='../assets/img/edit-button.png' title='Редагувати'> ".
                    "<img id='book-del' data-id='{$value['book_id']}' class='edit-button' src='../assets/img/close.png' title='Видалити'>".
                    "<img id='copy-link' data-id='{$value['book_id']}' class='edit-button' src='../assets/img/copylink.png' title='Скопіювати посилання'>"
                    ;

                    $pages = $value['pages'] ? " ({$value['pages']} стор.)" : '';
                    $price = $value['price'] ? "Ціна: {$value['price']} грн" : '';
                    $available = $value['available'] ? "Наявність: Так" : 'Наявність: Ні';
                    $writer = $value['assemble'] ? "<img class='picture writer' src='../assets/img/pero.png'> {$value['assemble']}" : '';

                    $book_picture = $value['picture'] != '' ? "<img class='picture' src='{$photo_folder}{$value['picture']}'>" : '';
                    echo "<div class='book-item' data-book='{$value['book_id']}'>
                    <div>
                    <span class='book-item__create'>
                        <img src='../assets/img/calendar2.jpg'>
                        {$value['created']}
                    </span>
                    <span class='btns'> {$btns}</span>
                        <span class='book-name'>&laquo;{$value['book']}&raquo; </span>
                        <span class='book-author'> {$writer}</span>
                        <span class='book-item__pages'>{$pages}</span>
                        <span class='book-describe'>{$value['describe']}</span>
                    </div>
                    <span class='img'> {$book_picture} </span>
                    <span class='book-describe'>{$price} </span>
                    <span class='book-describe'>{$available}</span>
                    </div>";
                }
            }
            // echo '</div>';
            ?>