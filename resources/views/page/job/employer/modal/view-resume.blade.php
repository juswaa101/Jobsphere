<!-- View Resume Modal -->
<div class="modal fade" id="viewResumeModal" tabindex="-1" aria-labelledby="viewResumeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewResumeModalLabel">Applicant Resume</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Name: <span class="fw-bold" id="applicantName"></span></p>
                <p>Email: <span class="fw-bold" id="applicantEmailDisplay"></span></p>
                <p>Job Title Applied For: <span class="fw-bold" id="jobTitle"></span></p> <!-- Job title -->
                <p>Company: <span class="fw-bold" id="companyTitle"></span></p> <!-- Company name -->
                <hr>
                <h6>Resume Preview:</h6>
                <iframe id="resumeContent" src="" style="width: 100%; height: 400px; display: none;"
                    frameborder="0"></iframe>
                <p id="resumeMessage" style="display: none; color: red;"></p> <!-- Message for unsupported formats -->
                <a href="#" id="applicantResumeLink" class="btn btn-primary mt-3" target="_blank">Download
                    Resume</a>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
