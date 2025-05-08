<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Manager</title>
  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
       
        .error-modal {
            background-color: #f8d7da; 
            color: #721c24; 
        }
    </style>
</head>
<body>
<div class="container py-5">
    <h2 class="mb-4 text-center">📇 Contact Manager</h2>

    <!-- Toast message (success/error) -->
    @if(session('success') || session('error'))
        <div id="toast-message" class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show" role="alert">
            {{ session('success') ?? session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Import Contacts Form -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Import Contacts from XML</div>
        <div class="card-body">
            <form id="importForm" method="POST" action="{{ route('contacts.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-2">
                    <div class="col-md-10">
                        <input type="file" name="xml_file" id="xml_file" class="form-control" accept=".xml">
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-primary">📥 Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Contact List (for displaying added contacts) -->
    <div class="card">
        <div class="card-header bg-success text-white">Contact List</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Phone</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $contact)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $contact->name }}</td>
                            <td>{{ $contact->phone }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No contacts available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for Validation Error -->
<div class="modal fade" id="validationModal" tabindex="-1" aria-labelledby="validationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content error-modal">
            <div class="modal-body" id="modal-message">
                <!-- Error message will be inserted here dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS & Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(function () {
    // Show toast message if present
    $('#toast-message').fadeIn(300).delay(4000).fadeOut(300);

    // Custom function to show modal with the validation error
    function showModal(message) {
        $('#modal-message').text(message); 
        $('#validationModal').modal('show'); 

        // Hide the modal after 1.5 seconds
        setTimeout(function () {
            $('#validationModal').modal('hide');
        }, 900);
    }

    // Validate Import XML Form
    $('#importForm').on('submit', function (e) {
        let file = $('#xml_file').val();
        if (!file) {
            e.preventDefault();
            showModal('Please select an XML file to import.');
        }
    });
});
</script>

</body>
</html>
