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
            <!-- Add Contact Button -->
            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#createContactModal">
                ➕ Add New Contact
            </button>

            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $contact)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $contact->name }}</td>
                        <td>{{ $contact->phone }}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal-{{ $contact->id }}">✏️ Edit</button>

                            <form method="POST" action="{{ route('contacts.destroy', $contact) }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this contact?')">🗑 Delete</button>
                            </form>
                        </td>

                    </tr>

                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No contacts available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <!-- Pagination Links -->
<div class="d-flex justify-content-center">
    {{ $contacts->links() }}
</div>

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

<!-- Create Contact Modal -->
<div class="modal fade" id="createContactModal" tabindex="-1" aria-labelledby="createContactModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('contacts.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createContactModalLabel">Add New Contact</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Add Contact</button>
        </div>
      </div>
    </form>
  </div>
</div>

@foreach($contacts as $contact)
<!-- Edit Modal -->
<div class="modal fade" id="editModal-{{ $contact->id }}" tabindex="-1" aria-labelledby="editModalLabel-{{ $contact->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('contacts.update', $contact) }}">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Contact</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ $contact->name }}" required>
          </div>
          <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ $contact->phone }}" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endforeach


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
