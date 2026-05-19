<form id="edit-form">
    <input type="hidden" id="edit_id" name="id" value="{{ $shipment->id }}">
    <div class="modal-body">
        @php
            $productOptions = collect($productNames ?? [])->push($shipment->product_name)->filter()->unique()->values();
            $stateOptions = collect($states ?? [])->push($shipment->state)->filter()->unique()->values();
            $warehouseOptions = collect($warehouses ?? [])->push($shipment->warehouse_name)->filter()->unique()->values();
        @endphp

        <div class="form-group mb-3">
            <label>FBA Shipment ID</label>
            <input type="text" class="form-control" value="{{ $shipment->shipment_id }}" disabled>
        </div>

        <div class="form-group mb-3">
            <label>FBA Shipment Date <span class="text-danger">*</span></label>
            <input type="date" name="shipment_date" class="form-control"
                   value="{{ $shipment->shipment_date ? $shipment->shipment_date->format('Y-m-d') : '' }}" required>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label>Product <span class="text-danger">*</span></label>
                    <select name="product_name" class="form-select fba-select2" data-placeholder="Select or type product" data-tags="1" required>
                        <option value=""></option>
                        @foreach($productOptions as $productName)
                            <option value="{{ $productName }}" {{ $shipment->product_name === $productName ? 'selected' : '' }}>{{ $productName }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label>Qty <span class="text-danger">*</span></label>
                    <input type="number" name="qty" class="form-control" min="1"
                           value="{{ $shipment->qty }}" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label>State <span class="text-danger">*</span></label>
                    <select name="state" class="form-select fba-select2" data-placeholder="Select state" required>
                        <option value=""></option>
                        @foreach($stateOptions as $state)
                            <option value="{{ $state }}" {{ $shipment->state === $state ? 'selected' : '' }}>{{ $state }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label>Warehouse Name <span class="text-danger">*</span></label>
                    <select name="warehouse_name" class="form-select fba-select2" data-placeholder="Select or type warehouse name" data-tags="1" required>
                        <option value=""></option>
                        @foreach($warehouseOptions as $warehouse)
                            <option value="{{ $warehouse }}" {{ $shipment->warehouse_name === $warehouse ? 'selected' : '' }}>{{ $warehouse }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label>QTY Price (₹) <span class="text-danger">*</span></label>
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

        <div class="row border-top pt-3 mt-2">
            <div class="col-md-6 mb-2">
                <div class="fw-bold">Generated By</div>
                <div>{{ $shipment->generator->username ?? $shipment->generator->name ?? 'System' }}</div>
                <small class="text-muted">{{ $shipment->created_at ? $shipment->created_at->format('d-M-Y H:i') : '-' }}</small>
            </div>
            <div class="col-md-6 mb-2">
                <div class="fw-bold">Last Updated By</div>
                <div>{{ $shipment->updater->username ?? $shipment->updater->name ?? '-' }}</div>
                <small class="text-muted">{{ $shipment->updater && $shipment->updated_at ? $shipment->updated_at->format('d-M-Y H:i') : '-' }}</small>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>
