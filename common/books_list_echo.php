<? // відображення списку книг
            // echo "<div class='book-right'>";
            if (isset($books)) {
                $root = ROOTFOLDER;
                $photo_folder = BOOK_PHOTO_FOLDER;
                foreach($books as $key=>$value){
                    $btns = ''; // кнопки edit та del
                    if ($login & isset($_GET['folder'])) // формуємо кнопки редагування та видалення якщо юзер залогінений та поточною сторінкою є "Книги" ($_GET['folder'] свідчить, що обрано поточний розділ)
                    $btns = 
                    "<span class='btns'>".
                    "<img id='book-edit' data-id='{$value['book_id']}' class='edit-button' src='../assets/img/edit-button.png' title='Редагувати'>".
                        "<img id='book-del' data-id='{$value['book_id']}' class='edit-button' src='../assets/img/close.png' title='Видалити'>".
                        "<img id='copy-link' data-id='{$value['book_id']}' class='edit-button' src='../assets/img/copylink.png' title='Скопіювати посилання'>".
                    "</span>";
                    $goto_folder = "";
                    if (isset($_GET['author'])) $goto_folder = "
                    <span class='book-folder'>
                        <a href='{$root}/books/?folder={$value['folder_id']}&book={$value['book_id']}' title='Перейти до розділу {$value['folder']}'>
                        <img src='../assets/img/books2.png'>{$value['folder']}
                        </a>
                    </span>";
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
                    {$btns}
                        <span class='book-name'>&laquo;{$value['book']}&raquo; </span>
                        <span class='book-author'> {$writer}</span>
                        
                        <span class='book-item__pages'>{$pages}</span>
                        {$goto_folder}
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