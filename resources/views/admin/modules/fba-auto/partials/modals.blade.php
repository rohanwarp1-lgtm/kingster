<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New FBA Shipment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="create-form">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>FBA Shipment ID</label>
                        <input type="text" class="form-control" value="Auto generated after save" disabled>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>FBA Shipment Date <span class="text-danger">*</span></label>
                                <input type="date" name="shipment_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Shipment Time <span class="text-danger">*</span></label>
                                <input type="time" name="shipment_time" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Product <span class="text-danger">*</span></label>
                                <select name="product_name" class="form-select fba-select2" data-placeholder="Select or type product" data-tags="1" required>
                                    <option value=""></option>
                                    @foreach(($productNames ?? []) as $productName)
                                        <option value="{{ $productName }}">{{ $productName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Qty <span class="text-danger">*</span></label>
                                <input type="number" name="qty" class="form-control" min="1" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>State <span class="text-danger">*</span></label>
                                <select name="state" class="form-select fba-select2" data-placeholder="Select state" required>
                                    <option value=""></option>
                                    @foreach(($states ?? []) as $state)
                                        <option value="{{ $state }}">{{ $state }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Warehouse Name <span class="text-danger">*</span></label>
                                <select name="warehouse_name" class="form-select fba-select2" data-placeholder="Select or type warehouse name" data-tags="1" required>
                                    <option value=""></option>
                                    @foreach(($warehouses ?? []) as $warehouse)
                                        <option value="{{ $warehouse }}">{{ $warehouse }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>QTY Price (₹) <span class="text-danger">*</span></label>
                        <input type="number" name="qty_price" class="form-control" step="0.01" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Shipment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="edit-content"></div>
        </div>
    </div>
</div>
