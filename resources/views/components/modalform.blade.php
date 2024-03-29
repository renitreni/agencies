<div>
    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="{{ $id }}Label" aria-hidden="true">
        <div class="modal-dialog {{ $size ?? '' }}" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="{{ $id }}Label">{{ $modalTitle }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ $slot }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    {{ $button }}
                </div>
            </div>
        </div>
    </div>
</div>
