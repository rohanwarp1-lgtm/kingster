<form id="edit-form">
    <input type="hidden" id="edit_id" name="id" value="{{ $shipment->id }}">
    <div class="modal-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label>Shipment Date <span class="text-danger">*</span></label>
                    <input type="date" name="shipment_date" class="form-control"
                           value="{{ $shipment->shipment_date ? $shipment->shipment_date->format('Y-m-d') : '' }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label>Shipment Time <span class="text-danger">*</span></label>
                    <input type="time" name="shipment_time" class="form-control"
                           value="{{ $shipment->shipment_time }}" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label>Product Name <span class="text-danger">*</span></label>
                    <input type="text" name="product_name" class="form-control"
                           value="{{ $shipment->product_name }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label>Quantity <span class="text-danger">*</span></label>
                    <input type="number" name="qty" class="form-control" min="1"
                           value="{{ $shipment->qty }}" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label>State <span class="text-danger">*</span></label>
                    <input type="text" name="state" class="form-control"
                           value="{{ $shipment->state }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label>Warehouse <span class="text-danger">*</span></label>
                    <input type="text" name="warehouse_name" class="form-control"
                           value="{{ $shipment->warehouse_name }}" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label>Price (₹) <span class="text-danger">*</span></label>
                    <input type="number" name="qty_price" class="form-control" step="0.01"
                           value="{{ $shipment->qty_price }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="pending" {{ $shipment->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $shipment->status === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ $shipment->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ $shipment->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="closed" {{ $shipment->status === 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="cancelled" {{ $shipment->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="returned" {{ $shipment->status === 'returned' ? 'selected' : '' }}>Returned</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>
