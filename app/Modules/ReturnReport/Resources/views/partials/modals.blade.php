<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Return Report</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="create-form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Order ID <span class="text-danger">*</span></label>
                                <input type="text" name="order_id" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Marketplace <span class="text-danger">*</span></label>
                                <select name="marketplace" class="form-control" required>
                                    <option value="">Select</option>
                                    <option value="amazon">Amazon</option>
                                    <option value="flipkart">Flipkart</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Product Name <span class="text-danger">*</span></label>
                                <input type="text" name="product_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Model Name <span class="text-danger">*</span></label>
                                <input type="text" name="model_name" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Return Reason <span class="text-danger">*</span></label>
                                <input type="text" name="return_reason" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Refund Status <span class="text-danger">*</span></label>
                                <select name="refund_status" class="form-control" required>
                                    <option value="">Select</option>
                                    <option value="pending">Pending</option>
                                    <option value="processed">Processed</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="partial">Partial</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Return Cost (₹) <span class="text-danger">*</span></label>
                                <input type="number" name="return_cost" class="form-control" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Loss Amount (₹) <span class="text-danger">*</span></label>
                                <input type="number" name="loss_amount" class="form-control" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Warehouse <span class="text-danger">*</span></label>
                        <input type="text" name="warehouse" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
