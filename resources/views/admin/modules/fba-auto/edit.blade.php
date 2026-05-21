<form id="edit-form">
    <input type="hidden" id="edit_id" value="{{ $shipment->id }}">
    <div class="modal-body">

        {{-- Shipment header --}}
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <label class="form-label">Shipment ID</label>
                <input type="text" class="form-control" value="{{ $shipment->shipment_id }}" disabled>
            </div>
            <div class="col-md-3">
                <label class="form-label">Shipment Date <span class="text-danger">*</span></label>
                <input type="date" name="shipment_date" class="form-control"
                       value="{{ $shipment->shipment_date ? $shipment->shipment_date->format('Y-m-d') : '' }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">State <span class="text-danger">*</span></label>
                @php
                    $stateOptions = collect($states ?? [])->push($shipment->state)->filter()->unique()->values();
                @endphp
                <select name="state" class="form-select fba-select2" data-placeholder="Select or type state" data-tags="1" required>
                    <option value=""></option>
                    @foreach($stateOptions as $s)
                        <option value="{{ $s }}" {{ $shipment->state === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Warehouse Name <span class="text-danger">*</span></label>
                @php
                    $warehouseOptions = collect($warehouses ?? [])->push($shipment->warehouse_name)->filter()->unique()->values();
                @endphp
                <select name="warehouse_name" class="form-select fba-select2" data-placeholder="Select or type warehouse name" data-tags="1" required>
                    <option value=""></option>
                    @foreach($warehouseOptions as $w)
                        <option value="{{ $w }}" {{ $shipment->warehouse_name === $w ? 'selected' : '' }}>{{ $w }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Status selector --}}
        @php
            $statusConfig = [
                'pending'    => ['label' => 'Pending',    'color' => 'warning',   'icon' => 'fe-clock'],
                'processing' => ['label' => 'Processing', 'color' => 'info',      'icon' => 'fe-refresh-cw'],
                'shipped'    => ['label' => 'Shipped',    'color' => 'primary',   'icon' => 'fe-truck'],
                'delivered'  => ['label' => 'Delivered',  'color' => 'success',   'icon' => 'fe-check-circle'],
                'closed'     => ['label' => 'Closed',     'color' => 'secondary', 'icon' => 'fe-lock'],
                'cancelled'  => ['label' => 'Cancelled',  'color' => 'danger',    'icon' => 'fe-x-circle'],
                'returned'   => ['label' => 'Returned',   'color' => 'dark',      'icon' => 'fe-corner-down-left'],
            ];
        @endphp
        <div class="mb-4">
            <label class="form-label fw-semibold">Shipment Status</label>
            <input type="hidden" name="status" id="edit-status-input" value="{{ $shipment->status }}">
            <div class="d-flex flex-wrap gap-2 mt-1">
                @foreach($statusConfig as $value => $cfg)
                <button type="button"
                    class="btn btn-sm status-badge-btn {{ $shipment->status === $value ? 'btn-'.$cfg['color'] : 'btn-outline-'.$cfg['color'] }}"
                    data-status="{{ $value }}">
                    <i class="fe {{ $cfg['icon'] }} me-1"></i>{{ $cfg['label'] }}
                    @if($shipment->status === $value)
                        <i class="fe fe-check ms-1"></i>
                    @endif
                </button>
                @endforeach
            </div>
        </div>

        {{-- Product rows --}}
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0 fw-semibold">Products</h6>
            <button type="button" class="btn btn-sm btn-outline-primary" id="add-edit-product-row">
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
                <tbody id="edit-product-rows">
                    @foreach($items as $idx => $item)
                    <tr>
                        <td class="text-center row-num">{{ $idx + 1 }}</td>
                        <td>
                            <input type="hidden" name="items[{{ $idx }}][id]" value="{{ $item->id }}">
                            <select name="items[{{ $idx }}][product_name]" class="form-select edit-product-select2 w-100" style="width:100%" required>
                                <option value="{{ $item->product_name }}" selected>{{ $item->product_name }}</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" name="items[{{ $idx }}][qty]" class="form-control form-control-sm"
                                   min="1" value="{{ $item->qty }}" required>
                        </td>
                        <td>
                            <input type="number" name="items[{{ $idx }}][qty_price]" class="form-control form-control-sm"
                                   step="0.01" min="0" max="1000000000" value="{{ $item->qty_price }}" required>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-edit-row-btn">
                                <i class="fe fe-trash-2"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update Shipment</button>
    </div>
</form>
