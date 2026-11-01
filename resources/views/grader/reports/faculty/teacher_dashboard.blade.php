<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();

?>
@extends('layouts.master')
@section('title')
    BNU CMS
@stop
@section('mainBody')
<div style="width:100%; border:solid 2px #000000;min-height:600px;">
<?php
$a=1;
while($a<10){
?>
<div style="float:left; margin:10px; width:300px; height:300px; border:solid 4px #000000;">
	<?php echo $a; ?>
</div>
<?php
$a++;
}
?>
</div>
<div style="clear:both;"></div>



@stop

