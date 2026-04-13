@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    Offence Record Details
                </h2>
                <a href="{{ route('reports.employee-offences') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Offences
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <span class="badge {{ $offence->getSeverityBadgeClassAttribute() }}">{{ ucfirst($offence->severity) }}</span>
                        <span class="badge {{ $offence->getStatusBadgeClassAttribute() }}">{{ ucfirst($offence->status) }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Employee Information -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2">Employee Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Employee Name</p>
                                <p class="mb-3"><strong>{{ $offence->employee->full_name }}</strong></p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Employee ID</p>
                                <p class="mb-3"><strong>{{ $offence->employee->employee_id }}</strong></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Department</p>
                                <p class="mb-3"><strong>{{ $offence->employee->department->name ?? 'N/A' }}</strong></p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Position</p>
                                <p class="mb-3"><strong>{{ $offence->employee->position->name ?? 'N/A' }}</strong></p>
                            </div>
                        </div>
                    </div>

                    <!-- Offence Details -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2">Offence Details</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Offence Type</p>
                                <p class="mb-3"><strong>{{ $offence->offence_type }}</strong></p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Offence Date</p>
                                <p class="mb-3"><strong>{{ $offence->offence_date->format('F d, Y') }}</strong></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <p class="text-muted mb-1">Description</p>
                                <p class="mb-3"><strong>{{ $offence->description }}</strong></p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Information -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2">Action Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Reported By</p>
                                <p class="mb-3"><strong>{{ $offence->reported_by ?? 'System' }}</strong></p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Action Date</p>
                                <p class="mb-3"><strong>{{ $offence->action_date ? $offence->action_date->format('F d, Y') : 'Not Set' }}</strong></p>
                            </div>
                        </div>
                        @if($offence->action_taken)
                        <div class="row">
                            <div class="col-12">
                                <p class="text-muted mb-1">Action Taken</p>
                                <p class="mb-3"><strong>{{ $offence->action_taken }}</strong></p>
                            </div>
                        </div>
                        @endif
                        @if($offence->notes)
                        <div class="row">
                            <div class="col-12">
                                <p class="text-muted mb-1">Notes</p>
                                <p class="mb-3"><strong>{{ $offence->notes }}</strong></p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Timestamps -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2">Records</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Created</p>
                                <p class="mb-3"><small>{{ $offence->created_at->format('F d, Y H:i') }}</small></p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Last Updated</p>
                                <p class="mb-3"><small>{{ $offence->updated_at->format('F d, Y H:i') }}</small></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <!-- Edit Button -->
                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editOffenceModal" onclick="editOffence('{{ $offence->id }}')">
                            <i class="fas fa-edit"></i> Edit Record
                        </button>

                        <!-- Delete Button -->
                        <button type="button" class="btn btn-danger" onclick="deleteOffence('{{ $offence->id }}')">
                            <i class="fas fa-trash"></i> Delete Record
                        </button>
                    </div>

                    <hr class="my-3">

                    <!-- Status Badge -->
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Current Status</p>
                        <span class="badge {{ $offence->getStatusBadgeClassAttribute() }} p-2">
                            {{ ucfirst($offence->status) }}
                        </span>
                    </div>

                    <!-- Severity Badge -->
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Severity Level</p>
                        <span class="badge {{ $offence->getSeverityBadgeClassAttribute() }} p-2">
                            {{ ucfirst($offence->severity) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal (included for inline editing) -->
<div class="modal fade" id="editOffenceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Offence Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editOffenceForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editEmployeeId">Employee *</label>
                        <select class="form-control" id="editEmployeeId" name="employee_id" required>
                            <option value="">-- Select Employee --</option>
                            <!-- Populated by JavaScript -->
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="editOffenceType">Offence Type *</label>
                        <input type="text" class="form-control" id="editOffenceType" name="offence_type" required>
                    </div>

                    <div class="form-group">
                        <label for="editDescription">Description *</label>
                        <textarea class="form-control" id="editDescription" name="description" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="editOffenceDate">Offence Date *</label>
                        <input type="date" class="form-control" id="editOffenceDate" name="offence_date" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="editSeverity">Severity *</label>
                            <select class="form-control" id="editSeverity" name="severity" required>
                                <option value="minor">Minor</option>
                                <option value="major">Major</option>
                                <option value="serious">Serious</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="editStatus">Status *</label>
                            <select class="form-control" id="editStatus" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="verified">Verified</option>
                                <option value="dismissed">Dismissed</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="editReportedBy">Reported By</label>
                        <input type="text" class="form-control" id="editReportedBy" name="reported_by">
                    </div>

                    <div class="form-group">
                        <label for="editActionTaken">Action Taken</label>
                        <textarea class="form-control" id="editActionTaken" name="action_taken" rows="2"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="editNotes">Notes</label>
                        <textarea class="form-control" id="editNotes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function editOffence(offenceId) {
    $.ajax({
        type: 'GET',
        url: '/reports/employee-offences/' + offenceId,
        dataType: 'json',
        success: function(data) {
            $('#editEmployeeId').val(data.employee_id);
            $('#editOffenceType').val(data.offence_type);
            $('#editDescription').val(data.description);
            $('#editOffenceDate').val(data.offence_date);
            $('#editSeverity').val(data.severity);
            $('#editStatus').val(data.status);
            $('#editReportedBy').val(data.reported_by);
            $('#editActionTaken').val(data.action_taken);
            $('#editNotes').val(data.notes);
            $('#editOffenceForm').attr('action', '/reports/employee-offences/' + offenceId);
        },
        error: function(xhr) {
            alert('Error loading offence for editing');
        }
    });
}

function deleteOffence(offenceId) {
    if (confirm('Are you sure you want to delete this offence record? This action cannot be undone.')) {
        $.ajax({
            type: 'DELETE',
            url: '/reports/employee-offences/' + offenceId,
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert('Offence record deleted successfully!');
                window.location.href = '{{ route("reports.employee-offences") }}';
            },
            error: function(xhr) {
                alert('Error deleting record');
            }
        });
    }
}

$(document).ready(function() {
    $('#editOffenceForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const url = form.attr('action');
        const formData = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            _method: 'PUT',
            employee_id: $('#editEmployeeId').val(),
            offence_type: $('#editOffenceType').val(),
            description: $('#editDescription').val(),
            offence_date: $('#editOffenceDate').val(),
            severity: $('#editSeverity').val(),
            status: $('#editStatus').val(),
            reported_by: $('#editReportedBy').val(),
            action_taken: $('#editActionTaken').val(),
            notes: $('#editNotes').val()
        };
        
        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            success: function(response) {
                alert('Offence record updated successfully!');
                location.reload();
            },
            error: function(xhr) {
                alert('Error updating record');
            }
        });
    });
});
</script>
@endpush

@endsection
