<div class="modal fade" id="postJobModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Large modal -->
        <div class="modal-content">
            <div class="modal-header card-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Post Job</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="jobPostForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label">Job Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                            <div class="invalid-feedback" id="titleError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="company" class="form-label">Company</label>
                            <input type="text" class="form-control" id="company" name="company" required>
                            <div class="invalid-feedback" id="companyError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="salary_from" class="form-label">Salary From</label>
                            <input type="number" class="form-control" id="salary_from" name="salary_from" required>
                            <div class="invalid-feedback" id="salary_fromError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="salary_to" class="form-label">Salary To</label>
                            <input type="number" class="form-control" id="salary_to" name="salary_to" required>
                            <div class="invalid-feedback" id="salary_toError"></div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" required></textarea>
                            <div class="invalid-feedback" id="descriptionError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="company_logo" class="form-label">Company Logo (max 8MB)</label>
                            <input type="file" class="form-control" id="company_logo" name="company_logo">
                            <div class="invalid-feedback" id="company_logoError"></div>
                        </div>
                        <div class="col-md-6 mb-3 form-check">
                            <input type="checkbox" id="is_active" name="is_active" class="form-check-input">
                            <label for="is_active" class="form-check-label">Is Active?</label>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="expiry_date" class="form-label">Expiry Date</label>
                            <input type="date" class="form-control" id="expiry_date" name="expiry_date">
                            <div class="invalid-feedback" id="expiry_dateError"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveJobButton">
                    <span id="buttonText">Save</span>
                    <i id="loadingSpinner" class="fas fa-spinner fa-spin d-none"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Setup ajax csrf token per request
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
</script>
