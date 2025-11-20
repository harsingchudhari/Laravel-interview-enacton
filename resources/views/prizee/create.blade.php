<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  </head>
  <body>
        <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mt-5">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4><i class="fas fa-gift"></i> Create Prize</h4>
                        <a href="{{ route('prizee.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                     <div class="alert alert-info mt-2">
                        

                            <strong>Total probability Used:</strong> {{ isset($existingTotal) ? $existingTotal : '' }}% <strong>Remaining probability:</strong> {{ isset($remaining) ? $remaining : ''}}%
                    </div>

                    <div class="card-body">
                         @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li><i class="fas fa-exclamation-circle"></i> {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form id="createForm" action="{{ route('prizee.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="title" class="form-label"><i class="fas fa-user-tag"></i> Title</label>
                                <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="text-danger mt-1"><i class="fas fa-exclamation-triangle"></i> {{ isset($message) ? $message : '' }} div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="probability" class="form-label"><i class="fas fa-percent"></i> Prize probability</label>
                                <input type="number" class="form-control" id="probability" name="probability" placeholder="0-100" min="0.01" max="100" step="0.01" value="{{ old('probability') }}" required>
                                <div class="form-text">Enter a value between 0.01% and 100%</div>
                                @error('probability')
                                    <div class="text-danger mt-1"><i class="fas fa-exclamation-triangle"></i> {{ isset($message) ? $message : '' }}</div>
                                @enderror
                                <div class="invalid-feedback d-none" id="probabilityError">probability must be between 0.01% and 100%.</div>
                            </div>
                            <button type="submit" id="submitCreateBtn" class="btn btn-primary">
                                <i class="fas fa-save"></i> Submit
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
        (function(){
            var createForm = document.getElementById('createForm');
            var submitCreateBtn = document.getElementById('submitCreateBtn');
            var probabilityInput = document.getElementById('probability');
            var probabilityError = document.getElementById('probabilityError');
            
            if (createForm && submitCreateBtn && probabilityInput) {
                probabilityInput.addEventListener('input', function() {
                    var value = parseFloat(probabilityInput.value);
                    
                    if (isNaN(value) || value < 0.01 || value > 100) {
                        probabilityInput.classList.add('is-invalid');
                        probabilityError.classList.remove('d-none');
                        submitCreateBtn.disabled = true;
                    } else {
                        probabilityInput.classList.remove('is-invalid');
                        probabilityError.classList.add('d-none');
                        submitCreateBtn.disabled = false;
                    }
                });
                
                createForm.addEventListener('submit', function (e) {
                    var value = parseFloat(probabilityInput.value);
                    
                    if (isNaN(value) || value < 0.01 || value > 100) {
                        e.preventDefault();
                        probabilityInput.classList.add('is-invalid');
                        probabilityError.classList.remove('d-none');
                        return false;
                    }
                    
                    if (submitCreateBtn.disabled) return;
                    submitCreateBtn.disabled = true;
                    submitCreateBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Creating...';
                });
            }
        })();
    </script>
  </body>
</html>