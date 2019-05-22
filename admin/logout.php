<?
    session_start();
    require "../php_ajax/connect.php";
    session_destroy();
    echo 'Вы вийшли. Повернення на головну...';
    echo "<img src = '../assets/img/book.gif' style='width: 30px'>";
    echo "<script>
        let time = setTimeout(()=>{
            document.location.href = '..';
        },2500);
    </script>";
?>