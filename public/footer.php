<svg style="display:none;">
  <symbol id="icon-facebook" viewBox="0 0 16 16">
    <path d="M12.5 2H10a3 3 0 0 0-3 3v2H5a1 1 0 0 0-1 1v2h2v5h3v-5h2l.5-2H9V5a1 1 0 0 1 1-1h2.5z"/>
  </symbol>
  <symbol id="icon-twitter" viewBox="0 0 16 16">
    <path d="M16 3a6.5 6.5 0 0 1-1.89.52A3.3 3.3 0 0 0 15.56 2a6.56 6.56 0 0 1-2.08.8A3.28 3.28 0 0 0 7.88 5.03a9.32 9.32 0 0 1-6.77-3.43a3.28 3.28 0 0 0 1.01 4.37A3.23 3.23 0 0 1 .64 5.1v.04a3.28 3.28 0 0 0 2.63 3.22a3.3 3.3 0 0 1-.86.11c-.21 0-.42-.02-.62-.06a3.28 3.28 0 0 0 3.06 2.28A6.58 6.58 0 0 1 0 13.54a9.29 9.29 0 0 0 5.03 1.47c6.04 0 9.35-5 9.35-9.34c0-.14 0-.28-.01-.42A6.72 6.72 0 0 0 16 3z"/>
  </symbol>
  <symbol id="icon-youtube" viewBox="0 0 16 16">
    <path d="M15.634 4.504a1.999 1.999 0 0 0-1.406-1.414C13.02 2.667 8 2.667 8 2.667s-5.02 0-6.228.423A1.999 1.999 0 0 0 .366 4.504C0 5.713 0 8 0 8s0 2.287.366 3.496a1.999 1.999 0 0 0 1.406 1.414C2.98 13.333 8 13.333 8 13.333s5.02 0 6.228-.423a1.999 1.999 0 0 0 1.406-1.414C16 10.287 16 8 16 8s0-2.287-.366-3.496zM6.4 10.667V5.333L10.667 8 6.4 10.667z"/>
  </symbol>
</svg>
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3 mb-md-0">
                <strong>Contact</strong><br>
                Election Commission of Sri Lanka<br>
                204, Bauddhaloka Mawatha,<br>
                Colombo 07, Sri Lanka<br>
                <a href="mailto:info@elections.gov.lk">info@elections.gov.lk</a>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <strong>Quick Links</strong><br>
                <a href="index.php">Home</a> |
                <a href="how-election-works.php">How Election Works</a> |
                <a href="guidance.php">Guidance</a>
            </div>
            <div class="col-md-4">
                <strong>Follow Us</strong><br>
                <a href="#" title="Facebook" style="color:#ffd700;">
                  <svg width="18" height="18" fill="currentColor"><use xlink:href="#icon-facebook"/></svg> Facebook
                </a> |
                <a href="#" title="Twitter" style="color:#ffd700;">
                  <svg width="18" height="18" fill="currentColor"><use xlink:href="#icon-twitter"/></svg> Twitter
                </a> |
                <a href="#" title="YouTube" style="color:#ffd700;">
                  <svg width="18" height="18" fill="currentColor"><use xlink:href="#icon-youtube"/></svg> YouTube
                </a>
            </div>
        </div>
        <hr style="border-color:rgba(255,255,255,0.2);">
        <div class="text-center small mt-2">
            &copy; <?php echo date('Y'); ?> Presidential Election of Sri Lanka. All Rights Reserved.<br>
            <span style="font-size:0.95em;">Inspired by the Sri Lankan Parliament and European Parliament web design.</span>
        </div>
    </div>
</footer>

<!-- Bootstrap JS for alert dismiss functionality -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom alert management -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 10 seconds (except for critical security messages)
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        if (alert.classList.contains('alert-danger')) {
            // Security alerts stay visible longer - 30 seconds
            setTimeout(function() {
                if (alert.parentNode) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 30000);
        } else {
            // Other alerts auto-hide after 10 seconds
            setTimeout(function() {
                if (alert.parentNode) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 10000);
        }
    });
    
    // Add click to dismiss functionality for security info section
    const securitySection = document.querySelector('.main-card[style*="border: 3px solid #dc3545"]');
    if (securitySection) {
        securitySection.style.cursor = 'pointer';
        securitySection.title = 'Click to minimize this security notice';
        securitySection.addEventListener('click', function() {
            this.style.display = 'none';
            // Store preference in localStorage
            localStorage.setItem('securityNoticeMinimized', 'true');
        });
        
        // Check if user previously minimized it
        if (localStorage.getItem('securityNoticeMinimized') === 'true') {
            securitySection.style.display = 'none';
        }
    }
});
</script> 