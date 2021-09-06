<div>

  <!-- Nav tabs -->
  <ul class="nav nav-tabs">
    <li class="nav-item " >
        <a class="nav-link active"  href="#home" aria-controls="home" role="tab" data-toggle="tab"> Fontawesome</a></li>
    <li class="nav-item"><a class="nav-link"  href="#profile" aria-controls="profile" role="tab" data-toggle="tab"> Themify</a></li>
    <li class="nav-item"><a class="nav-link"  href="#messages" aria-controls="messages" role="tab" data-toggle="tab">Simply Line</a></li>

  </ul>

  <!-- Tab panes -->
  <div class="tab-content pt-3">
    <div role="tabpanel" class="tab-pane active" id="home">
       @include('sximo.menu.fontawesome')
    </div>
    <div role="tabpanel" class="tab-pane" id="profile">
        @include('sximo.menu.temify')
    </div>
    <div role="tabpanel" class="tab-pane" id="messages">
        @include('sximo.menu.simple')
    </div>
  </div>

</div>
<style type="text/css">
    .icon-list-demo { font-size: 10px; }
    .icon-list-demo i{ font-size: 16px; }

</style>
<script type="text/javascript">
    $(document).ready(function(){

        $('.icon-list-demo  i , .material-icon-list-demo i').on('click',function(){
            val = $(this).attr('class');
           // alert(val);
            $('input[name=menu_icons]').val(val);
            $('#sximo-modal').modal('hide');

        })
    })
</script>