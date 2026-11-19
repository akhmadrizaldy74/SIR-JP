@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show alert-custom d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i>
        <div>
            <strong>Berhasil!</strong> {{ session('success') }}
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show alert-custom d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5 text-danger"></i>
        <div>
            <strong>Gagal!</strong> {{ session('error') }}
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show alert-custom d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-info-circle-fill me-2 fs-5 text-primary"></i>
        <div>
            <strong>Informasi:</strong> {{ session('info') }}
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show alert-custom mb-4" role="alert">
        <div class="d-flex align-items-center mb-2">
            <i class="bi bi-exclamation-octagon-fill me-2 fs-5 text-danger"></i>
            <strong>Terdapat kesalahan pada input:</strong>
        </div>
        <ul class="mb-0 ps-4 small">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
