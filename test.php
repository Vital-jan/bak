<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <!-- <form action="send.php" method="POST">

    <textarea name="text" id="" cols="30" rows="10"></textarea>
    <input type="text" name='input'>
    <button>Send</button>
    </form> -->
<script>
    call=(a)=>{alert('callback'+a)};
    
    func=(action)=>{
        action();
    };

    func(call('asd'));
</script>
</body>
</html>