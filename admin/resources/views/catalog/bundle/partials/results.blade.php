@foreach ($bundles as $bundle)
    <tr>
        <td>
            <div class="form-check">
                <input class="form-check-input bundle-checkbox" type="checkbox" value="{{ $bundle->id }}">
            </div>
        </td>
        <td>
            @if ($bundle->firstImage)
                <img src="{{ asset('storage/' . $bundle->firstImage->path) }}" class="img-thumbnail" width="60" alt="Bundle Image">
            @else
                <span class="text-muted">No image</span>
            @endif
        </td>
        <td><b>{{ $bundle->name }}</b></td>
        <td>{{ Str::limit($bundle->short_desc, 50) }}</td>
        <td class="text-center">{{ $bundle->variants_count }}</td>
        <td class="text-center">{{ number_format($bundle->purchase_price, 2) }}</td>
        <td class="text-center">{{ number_format($bundle->additional_price, 2) }}</td>
        <td class="text-center">
            <span class="badge rounded-pill alert-{{ $bundle->status ? 'success' : 'danger' }}">
                {{ $bundle->status ? 'Active' : 'Inactive' }}
            </span>
        </td>
        <td class="text-end">
            <div class="btn-group" role="group">
                <a href="{{ route('bundles.show', $bundle->id) }}" class="btn btn-sm font-sm btn-light rounded me-1" title="View Details">
                    <i class="material-icons md-view_carousel"></i> Detail
                </a>


            </div>
        </td>
    </tr>
@endforeach

@if($bundles->isEmpty())
    <tr>
        <td colspan="9" class="text-center py-4 text-muted">
            <i class="fas fa-inbox fa-2x mb-2 d-block text-secondary"></i>
            No bundles found matching your criteria
        </td>
    </tr>
@endif
