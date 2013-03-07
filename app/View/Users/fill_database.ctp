<?php
if(isset($errorMsg)){
    echo "処理中断 : ".$errorMsg;
    echo "<br/>";
}
?>

<script>
$(function(){


setTimeout(function(){
location.reload();
},900000
);
});
</script>
