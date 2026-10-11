{{-- resources/views/Academics/streams/_empty.blade.php --}}
<div class="text-center py-5 text-muted bg-white h-100
            d-flex flex-column justify-content-center align-items-center">
    <div class="bg-light rounded-circle p-3 mb-2">
        <i class="feather-inbox fs-20 opacity-50"></i>
    </div>
    <p class="mb-0 fs-13 fw-medium">{{ $label ?? 'Nothing here yet.' }}</p>
</div>