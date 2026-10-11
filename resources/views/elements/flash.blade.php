@if($errors->any())
    <div class="flash-alert alert alert-danger alert-dismissible" role="alert">
        <i class="menu-icon tf-icons fa-solid fa-triangle-exclamation"></i>{{$errors->first()}}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@elseif($message = Session::get('success'))
    <div class="flash-alert alert alert-success alert-dismissible" role="alert">
        <i class="menu-icon tf-icons fa-solid fa-check fa-sm me-1"></i>{{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<script>
    setTimeout(function() {
        $('.flash-alert').slideUp('slow');
    }, 3000)
</script>