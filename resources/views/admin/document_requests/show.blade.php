<x-layout>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>{{ __('Document Request Details') }}</span>
                        <a href="{{ route('admin.document-requests.index') }}" class="btn btn-sm btn-secondary">
                            {{ __('Back to List') }}
                        </a>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>{{ __('Request Information') }}</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>{{ __('ID') }}</th>
                                        <td>{{ $documentRequest->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Document Type') }}</th>
                                        <td>{{ $documentRequest->document_type }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Status') }}</th>
                                        <td>
                                            <span class="badge bg-{{ $documentRequest->status == 'pending' ? 'warning' : ($documentRequest->status == 'approved' ? 'success' : 'danger') }}">
                                                {{ ucfirst($documentRequest->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Requested At') }}</th>
                                        <td>{{ $documentRequest->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Last Updated') }}</th>
                                        <td>{{ $documentRequest->updated_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>{{ __('Employee Information') }}</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <td>{{ $documentRequest->employee->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Email') }}</th>
                                        <td>{{ $documentRequest->employee->email ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Department') }}</th>
                                        <td>{{ $documentRequest->employee->department ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Position') }}</th>
                                        <td>{{ $documentRequest->employee->position ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($documentRequest->notes)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5>{{ __('Notes') }}</h5>
                                <div class="p-3 bg-light rounded">
                                    {{ $documentRequest->notes }}
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($documentRequest->document_path)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5>{{ __('Uploaded Document') }}</h5>
                                <div class="p-3 bg-light rounded">
                                    <a href="{{ route('admin.document-requests.download', $documentRequest->id) }}" class="btn btn-primary">
                                        <i class="fas fa-download"></i> {{ __('Download Document') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($documentRequest->status == 'pending')
                        <div class="row">
                            <div class="col-md-12">
                                <h5>{{ __('Actions') }}</h5>
                                <div class="d-flex gap-2">
                                    <form action="{{ route('admin.document-requests.approve', $documentRequest->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success">{{ __('Approve Request') }}</button>
                                    </form>
                                    
                                    <form action="{{ route('admin.document-requests.reject', $documentRequest->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to reject this request?')">
                                            {{ __('Reject Request') }}
                                        </button>
                                    </form>
                                    
                                    <a href="{{ route('admin.document-requests.edit', $documentRequest->id) }}" class="btn btn-primary">
                                        {{ __('Edit Request') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>