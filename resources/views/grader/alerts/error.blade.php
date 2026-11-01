<html>
    @include('layouts.includes.css')
    <head><title>Error Alert | BNU</title></head>
    <body>
        <div style="margin:50px 20px">
        <div class="alert alert-danger">
            <strong>Alert!</strong> {{$message}} 
          </div>
        </div>
    </body>
    @include('layouts.includes.js')
</html>   
<script type="text/javascript">
</script>



