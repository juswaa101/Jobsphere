<div class="modal fade" id="updateJobModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Large modal -->
        <div class="modal-content">
            <div class="modal-header card-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Update Job</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="jobUpdateForm">
                    <div class="row">
                        <input type="hidden" class="form-control" id="jobId" name="jobId" required>
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label">Job Title</label>
                            <input type="text" class="form-control" id="jobTitle" name="jobTitle" required>
                            <div class="invalid-feedback" id="jobTitleError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="company" class="form-label">Company</label>
                            <input type="text" class="form-control" id="jobCompany" name="jobCompany" required>
                            <div class="invalid-feedback" id="jobCompanyError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="salary_from" class="form-label">Salary From</label>
                            <input type="number" class="form-control" id="jobSalary_from" name="jobSalary_from" required>
                            <div class="invalid-feedback" id="jobSalary_fromError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="salary_to" class="form-label">Salary To</label>
                            <input type="number" class="form-control" id="jobSalary_to" name="jobSalary_to" required>
                            <div class="invalid-feedback" id="jobSalary_toError"></div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="jobDescription" name="jobDescription" required></textarea>
                            <div class="invalid-feedback" id="jobDescriptionError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="company_logo" class="form-label">Company Logo (max 8MB)</label>
                            <input type="file" class="form-control" id="jobCompany_logo" name="jobCompany_logo">
                            <div class="invalid-feedback" id="jobCompany_logoError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <input type="checkbox" id="jobIs_active" name="jobIs_active" class="form-check-input">
                            <label for="is_active" class="form-check-label">Is Active?</label>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="expiry_date" class="form-label">Expiry Date</label>
                            <input type="date" class="form-control" id="jobExpiry_date" name="jobExpiry_date">
                            <div class="invalid-feedback" id="jobExpiry_dateError"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="updateJobButton">
                    <span id="buttonText">Update</span>
                    <i id="UpdateloadingSpinner" class="fas fa-spinner fa-spin d-none"></i>
                </button>
            </div>
        </div>
    </div>
</div>
