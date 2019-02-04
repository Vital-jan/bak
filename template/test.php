<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src='mysqlget.js'></script>
</head>
<body>
<script>
function getsql(response) {
    response.forEach((i)=>{
        document.write(`
        <ul>
        <li>${i.author}</li>
        </ul>
        `);
    })
}
queryGet('select * from authors', getsql);
function upd(response){
    console.log(response);
}
queryUpdate('test', 'id=2', [
    {key:'name', value:'test'}
], upd);
</script>
</body>
</html>