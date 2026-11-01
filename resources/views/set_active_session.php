<?php
if(isset($active_session_id)){
    session_start();
    //$session_array = (explode("-",$_POST['sel_active_session']));
    $_SESSION['active_session_id']=$active_session_id;
    $_SESSION['active_session_name']=$active_session_name;
    echo 1;
}else{
    echo 0;
}
?>
