<div>
    <livewire:toaster/>
    <div class="card mt-5">
        <div class="card-body">
            <div class="card-title mb-4 d-flex flex-row">
                <h3>Users</h3>
                <a href="#" class="btn btn-success ml-3" data-toggle="modal" data-target="#userModal">
                    <i class="fas fa-plus"></i> Add User
                </a>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="row mb-4 mx-2">
                    </div>
                </div>
                <div class="col-12">
                    <livewire:user-table/>
                </div>
            </div>
        </div>
    </div>

    <x-modalform id="userModal" modalTitle="New User" size="modal-lg">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Agency</label>
                <x-select model="details.agency_id">
                    @foreach($agencies as $agency)
                        <option value="{{ $agency['id'] }}">{{ $agency['name'] }}</option>
                    @endforeach
                </x-select>
            </div>
            <div class="col-md-4 mb-3">
                <label>Roles</label>
                <x-select model="details.role">
                    <option value="1">Admin</option>
                    <option value="2">Agency</option>
                    <option value="4">Gov</option>
                </x-select>
            </div>
            <div class="col-md-4 mb-3">
                <label>Name</label>
                <x-input model="details.name"/>
            </div>
            <div class="col-md-4 mb-3">
                <label>E-mail</label>
                <x-input model="details.email"/>
            </div>
            <div class="col-md-4 mb-3">
                <label>Password</label>
                <x-input type="password" model="details.password"/>
            </div>
            <div class="col-md-4 mb-3">
                <label>Confirm Password</label>
                <x-input type="password" model="details.password_confirmation"/>
            </div>
            <div class="col-md-12">
                <x-errors/>
            </div>
        </div>
        <x-slot name="button">
            <button type="button" class="btn btn-primary" wire:click="store">Save changes</button>
        </x-slot>
    </x-modalform>

    <x-modalform id="userEditModal" modalTitle="Edit User">
        <div class="row">
            <div>

            </div>
        </div>
        <x-slot name="button">
            <button type="button" class="btn btn-info">Update</button>
        </x-slot>
    </x-modalform>
</div>
