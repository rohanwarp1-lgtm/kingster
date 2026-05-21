<div class="modal fade" id="createModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New FBA Shipment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="create-form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="shipment_date">Shipment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="shipment_date" name="shipment_date" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="product_name">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="product_name" name="product_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="qty">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="qty" name="qty" min="1" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="state">State <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="state" name="state" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="warehouse_name">Warehouse Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="warehouse_name" name="warehouse_name" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="qty_price">Price (₹) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="qty_price" name="qty_price" step="0.01" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm d-none" id="create-spinner"></span>
                        Save Shipment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
