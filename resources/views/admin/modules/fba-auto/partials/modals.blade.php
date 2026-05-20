<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New FBA Shipment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="create-form">
                <div class="modal-body">

                    {{-- Shipment header --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-2">
                            <label class="form-label">Shipment ID <span class="text-danger">*</span></label>
                            <input type="text" name="shipment_id" class="form-control" placeholder="e.g. FBA-123456" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Shipment Date <span class="text-danger">*</span></label>
                            <input type="date" name="shipment_date" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Time <span class="text-danger">*</span></label>
                            <input type="time" name="shipment_time" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">State <span class="text-danger">*</span></label>
                            <select name="state" class="form-select fba-select2" data-placeholder="Select or type state" data-tags="1" required>
                                <option value=""></option>
                                @foreach(($states ?? []) as $state)
                                    <option value="{{ $state }}">{{ $state }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Warehouse Name <span class="text-danger">*</span></label>
                            <select name="warehouse_name" class="form-select fba-select2" data-placeholder="Select or type warehouse name" data-tags="1" required>
                                <option value=""></option>
                                @foreach(($warehouses ?? []) as $warehouse)
                                    <option value="{{ $warehouse }}">{{ $warehouse }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Product rows --}}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 fw-semibold">Products</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="add-product-row">
                            <i class="fe fe-plus"></i> Add Row
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:40px">#</th>
                                    <th>Product</th>
                                    <th style="width:110px">Qty</th>
                                    <th style="width:160px">Total Amount (₹)</th>
                                    <th style="width:46px"></th>
                                </tr>
                            </thead>
                            <tbody id="product-rows"></tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Shipment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Shipment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="edit-content"></div>
        </div>
    </div>
</div>
