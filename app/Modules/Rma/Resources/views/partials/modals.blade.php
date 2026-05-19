<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create RMA Ticket</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="create-form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Customer Name <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Mobile <span class="text-danger">*</span></label>
                                <input type="text" name="mobile" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Order ID <span class="text-danger">*</span></label>
                                <input type="text" name="order_id" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Order Date <span class="text-danger">*</span></label>
                                <input type="date" name="order_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Platform <span class="text-danger">*</span></label>
                                <select name="platform" class="form-control" required>
                                    <option value="">Select Platform</option>
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
                                <label>Model <span class="text-danger">*</span></label>
                                <input type="text" name="model" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Issue Type <span class="text-danger">*</span></label>
                                <select name="issue_type" class="form-control" required>
                                    <option value="">Select Issue</option>
                                    <option value="hardware_defect">Hardware Defect</option>
                                    <option value="software_issue">Software Issue</option>
                                    <option value="missing_parts">Missing Parts</option>
                                    <option value="wrong_item">Wrong Item</option>
                                    <option value="damaged">Damaged</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Replacement Type <span class="text-danger">*</span></label>
                                <select name="replacement_type" class="form-control" required>
                                    <option value="">Select Type</option>
                                    <option value="full">Full Replacement</option>
                                    <option value="partial">Partial Replacement</option>
                                    <option value="refund">Refund</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Issue Description <span class="text-danger">*</span></label>
                        <textarea name="issue_description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Address <span class="text-danger">*</span></label>
                        <textarea name="address" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Bill File</label>
                        <input type="file" name="bill_file" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="status-form">
                <div class="modal-body">
                    <input type="hidden" id="ticket_id" name="id">
                    <input type="hidden" id="current_status" name="current_status">
                    <div class="form-group">
                        <label>New Status <span class="text-danger">*</span></label>
                        <select name="status" id="new_status" class="form-control" required>
                            <option value="">Select Status</option>
                            <option value="open">Open</option>
                            <option value="under_review">Under Review</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="pickup_pending">Pickup Pending</option>
                            <option value="pickup_completed">Pickup Completed</option>
                            <option value="replacement_shipped">Replacement Shipped</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" id="status_notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
