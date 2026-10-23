<div>
    @push('breadcrumbs')
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Agencies</li>
        </ol>
        <h6 class="font-weight-bolder mb-0">Agencies</h6>
    @endpush
    <livewire:toaster/>
    <div class="card mt-5">
        <div class="card-body">
            <div class="card-title mb-4 d-flex flex-row">
                <h3>Agencies</h3>
                <x-a-button class="btn btn-success ms-3">
                    <x-slot name="others">
                        data-bs-toggle="modal" data-bs-target="#agencyModal" wire:click="$set('details', [])"
                    </x-slot>
                    <i class="fas fa-plus"></i> Add Agency
                </x-a-button>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="row mb-4 mx-2">
                    </div>
                </div>
                <div class="col-12">
                    <livewire:agency-table/>
                </div>
            </div>
        </div>
    </div>
    {{--Add--}}
    <x-modalform id="agencyModal" modalTitle="New Agency" size="modal-lg">
        <div class="row">
            <div class="col-md-6 mb-2">
                <label>Name</label>
                <x-input type="text" model="details.name"/>
            </div>
            <div class="col-md-6 mb-2">
                <label>POEA</label>
                <x-input type="text" model="details.poea"/>
            </div>
            <div class="col-md-6 mb-2">
                <label>CR No.</label>
                <x-input type="text" model="details.cr_no"/>
            </div>
            <div class="col-md-6 mb-2">
                <label>Status</label>
                <x-select model="details.status">
                    <option value="active">Active</option>
                    <option value="blocked">Blocked</option>
                </x-select>
            </div>
            <div class="col-md-6 mb-2">
                <label>Owner Name</label>
                <x-input type="text" model="details.owner_name"/>
            </div>
            <div class="col-md-6 mb-2">
                <label>Contact Number</label>
                <x-input type="text" model="details.contact_number"/>
            </div>
            <div class="col-md-12 mb-2">
                <label>Address</label>
                <x-textarea model="details.address"/>
            </div>
            <div class="col-md-6 mb-2">
                <label>Logo Upload</label>
                <x-input type="file" model="details.photo"/>
            </div>
            <div class="col-md-12 mb-2">
                <x-errors/>
            </div>
        </div>
        <x-slot name="button">
            <button type="button" class="btn btn-primary" wire:click="store">Save changes</button>
        </x-slot>
    </x-modalform>
    {{--Edit--}}
    <x-modalform id="agencyEditModal" modalTitle="New Agency" size="modal-lg">
        <div class="row">
            <div class="col-md-6 mb-2">
                <label>Name</label>
                <x-input type="text" model="details.name"/>
            </div>
            <div class="col-md-6 mb-2">
                <label>POEA</label>
                <x-input type="text" model="details.poea"/>
            </div>
            <div class="col-md-6 mb-2">
                <label>CR No.</label>
                <x-input type="text" model="details.cr_no"/>
            </div>
            <div class="col-md-6 mb-2">
                <label>Status</label>
                <x-select model="details.status">
                    <option value="active">Active</option>
                    <option value="blocked">Blocked</option>
                </x-select>
            </div>
            <div class="col-md-6 mb-2">
                <label>Owner Name</label>
                <x-input type="text" model="details.owner_name"/>
            </div>
            <div class="col-md-6 mb-2">
                <label>Contact Number</label>
                <x-input type="text" model="details.contact_number"/>
            </div>
            <div class="col-md-12 mb-2">
                <label>Address</label>
                <x-textarea model="details.address"/>
            </div>
            <div class="col-md-6 mb-2">
                <label>Logo Upload</label>
                <x-input type="file" model="details.photo"/>
            </div>
            <div class="col-md-6 mb-2">
                <label>Preview</label>
                <img src="{{ isset($details['logo_path']) ? \App\Models\Agency::make(['logo_path' => $details['logo_path']])->logo_url : '' }}"
                     class="img-fluid" style="width: 150px">
            </div>
            <div class="col-md-12 mb-2">
                <x-errors/>
            </div>
        </div>
        <x-slot name="button">
            <button type="button" class="btn btn-info" wire:click="edit" wire:loading.attr="disabled">Update</button>
            <button type="button" class="btn btn-danger" wire:click="delete">Delete</button>
        </x-slot>
    </x-modalform>
</div>
