<?
    $link = mysqli_connect('localhost', 'root', '', db1);
    if (!$link) {
    echo mysqli_connect_errno;
    echo mysqli_connect_error;
    }
    mysqli_query($link, "SET names 'utf8'");
    $query = mysqli_query($link, "select * from test") or die();
    $res = mysql_fetch_all();
    $res = array();
    while ($cRec = mysqli_fetch_assoc($query))
    $res[] = $cRec;
    var_dump($res);
?>