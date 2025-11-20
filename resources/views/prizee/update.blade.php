<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Update Prize</title>

    <style>
        #percentError {
            color: red;
        }
    </style>
</head>

<body>
<div class="container mt-4" data-existing="{{ isset($existingTotal) ? $existingTotal : '' }}" data-current="{{ isset($prizeedata->probability) ? $prizeedata->probability : '' }}">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><i class="fas fa-edit"></i> Update Prize</h4>
                    <a href="{{ route('prizee.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

                <div class="alert alert-info m-3 d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Total probability Used:</strong> <span id="existingTotal">{{ isset($existingTotal) ? $existingTotal : ''}}</span>%  
                        &nbsp;&nbsp;
                        <strong>Remaining probability:</strong> <span id="remaining">{{ isset($remaining) ? $remaining : '' }}</span>%
                    </div>
                </div>

                <div class="card-body">

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if (session('info'))
                        <div class="alert alert-info">{{ session('info') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="updateForm" action="{{ route('prizee.update', isset($prizeedata->id) ? $prizeedata->id : '') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-user-tag"></i> Title
                            </label>
                            <input type="text" class="form-control" name="title"
                                   value="{{ old('title', isset($prizeedata->title) ? $prizeedata->title : '') }}" required>
                        </div>

                        
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-info-circle"></i> Current Prize Percentage</label>
                            <input type="number" class="form-control" value="{{ isset($prizeedata->probability) ? $prizeedata->probability : '' }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-percent"></i> Prize probability</label>
                            <input type="number" class="form-control" id="probability" name="probability"
                                   value="{{ old('probability', isset($prizeedata->probability) ? $prizeedata->probability : '') }}"
                                   step="0.01" min="0.01" max="100" required>

                            <div class="form-text mt-1">
                                New total: <span id="newTotalPreview">{{ isset($existingTotal) ? $existingTotal : '' }}</span>%
                            </div>

                            <div class="invalid-feedback d-none" id="percentError">
                                Total percentage cannot exceed 100%.
                            </div>

                            <div class="form-text">Current value: {{ isset($prizeedata->probability) ? $prizeedata->probability : '' }}%</div>
                        </div>
                        <button type="submit" id="submitBtn" class="btn btn-primary">
                            <i class="fas fa-sync-alt"></i> Update
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    (function () {
        function toNum(v) {
            var n = parseFloat(v);
            return isNaN(n) ? 0 : n;
        }

        var container = document.querySelector('.container[data-existing]');
        if (!container) return;

        var existing = toNum(container.dataset.existing);
        var current = toNum(container.dataset.current);

        var percentageInput = document.getElementById('probability');
        var newTotalPreview = document.getElementById('newTotalPreview');
        var percentError = document.getElementById('percentError');
        var submitBtn = document.getElementById('submitBtn');

        function updatePreview() {
            var entered = toNum(percentageInput.value);

            var newTotal = existing - current + entered;
            newTotal = Math.round(newTotal * 100) / 100;

            newTotalPreview.textContent = newTotal;

            if (newTotal > 100) {
                percentError.classList.remove("d-none");
                percentageInput.classList.add("is-invalid");
                submitBtn.disabled = true;
            } else {
                percentError.classList.add("d-none");
                percentageInput.classList.remove("is-invalid");
                submitBtn.disabled = false;
            }
        }

        percentageInput.addEventListener('input', updatePreview);
        updatePreview(); 

        var updateForm = document.getElementById('updateForm');
        if (updateForm) {
            updateForm.addEventListener('submit', function () {
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm" role="status"></span> Updating...';
            });
        }
    })();

});

</script>

</body>
</html>
