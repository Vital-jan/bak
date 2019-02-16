<?
$query = getQuery('admin','');
$content = mysql_fetch_assoc($query);
echo "<p class='main-content'>
    <span>
        {$content['main']}
    </span>
    </p>";
?>
<div id="page">
    <div class="page-in">Lorem ipsum dolor sit amet consectetur adipisicing elit. Nulla odit, repudiandae consequuntur aperiam quia aspernatur unde beatae perspiciatis corporis quidem, explicabo doloribus, magni tempora libero optio eius nihil minima labore!</div>
    <div class="page-in">Lorem ipsum dolor sit amet consectetur adipisicing elit. Fugit voluptate omnis eaque excepturi, fuga molestias dolor ut atque dolorum eligendi facilis dolores iure quam cumque ab expedita deserunt accusantium beatae.</div>
    <div class="page-in">Lorem ipsum dolor sit amet consectetur adipisicing elit. Sint corrupti, eligendi sequi architecto eius veniam fugit, molestiae quis soluta illo reiciendis aspernatur et cum non ea nisi at adipisci exercitationem!</div>
</div>
