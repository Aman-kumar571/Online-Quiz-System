<?php
/**
 * Privacy Policy page.
 * Describes what data this application collects and how it is used.
 */

require_once __DIR__ . '/includes/helpers.php';

$pageTitle       = 'Privacy Policy';
$pageDescription = 'Privacy Policy for ' . SITE_NAME . '. Learn how we collect, use, and protect your data.';
$canonicalPath   = '/privacy.php';

require __DIR__ . '/includes/header.php';
?>

<div class="legal-page">
    <h1>Privacy Policy</h1>
    <p class="last-updated">Last updated: 1 October 2026</p>

    <p>This privacy policy explains how <?= e(SITE_NAME) ?> ("we", "our", "the application") collects, uses, stores, and protects your personal information when you use this quiz platform.</p>

    <h2>1. Information We Collect</h2>
    <p>When you create an account, we collect the following information:</p>
    <ul>
        <li><strong>Full name</strong>: used to identify you within the application and display on your profile.</li>
        <li><strong>Email address</strong>: used as your unique login credential. We do not send marketing emails.</li>
        <li><strong>Password</strong>: stored only as a cryptographic hash using the bcrypt algorithm. We never store or have access to your plain text password.</li>
    </ul>
    <p>When you use the application, we also collect:</p>
    <ul>
        <li><strong>Quiz attempt records</strong>: which quizzes you have taken, your scores, the number of questions, and the date and time of each attempt. This data is used to show you your own history and to let administrators review overall quiz performance.</li>
    </ul>

    <h2>2. Information We Do Not Collect</h2>
    <p>We do not collect your physical address, phone number, payment information, or location data. We do not use any third-party analytics, tracking pixels, advertising networks, or social media integrations. We do not embed any external scripts or resources beyond what is hosted on our own server.</p>

    <h2>3. How We Use Your Information</h2>
    <p>Your information is used solely for the following purposes:</p>
    <ul>
        <li>To create and maintain your user account.</li>
        <li>To authenticate you when you log in.</li>
        <li>To record and display your quiz scores and attempt history.</li>
        <li>To allow administrators to view aggregated results for all users.</li>
    </ul>

    <h2>4. Cookies</h2>
    <p>This application uses a single session cookie to keep you logged in while you use the site. This cookie:</p>
    <ul>
        <li>Is set with the <code>HttpOnly</code> flag, which prevents JavaScript from accessing it.</li>
        <li>Is set with the <code>SameSite=Strict</code> attribute to prevent cross-site request forgery.</li>
        <li>Is set with the <code>Secure</code> flag when the site is accessed over HTTPS.</li>
        <li>Expires when you close your browser or after the session timeout (1 hour of inactivity).</li>
    </ul>
    <p>We do not use any other cookies, including analytics cookies, advertising cookies, or persistent tracking cookies.</p>

    <h2>5. Data Storage and Security</h2>
    <p>Your data is stored in a MySQL database on the server that hosts this application. We protect your data with the following measures:</p>
    <ul>
        <li>Passwords are hashed with bcrypt before storage.</li>
        <li>All database queries use prepared statements to prevent SQL injection.</li>
        <li>All user input is escaped before display to prevent cross-site scripting (XSS).</li>
        <li>CSRF tokens are used on all forms to prevent cross-site request forgery.</li>
        <li>Session IDs are regenerated on login to prevent session fixation.</li>
    </ul>

    <h2>6. Data Retention</h2>
    <p>Your account data and quiz attempt history are retained for as long as your account exists. You can permanently delete your account and all associated quiz history at any time using the self-service account deletion page while logged in, or by contacting us at the email address below.</p>

    <h2>7. Your Rights</h2>
    <p>You have the right to:</p>
    <ul>
        <li><strong>Access your data</strong>: you can view your own quiz history at any time through the "My Attempts" page.</li>
        <li><strong>Delete your data</strong>: you can permanently delete your account and all associated quiz attempt records directly through the application at any time, or by contacting us.</li>
        <li><strong>Correct your data</strong>: if any information about you is incorrect, contact us and we will correct it.</li>
    </ul>

    <h2>8. Third-Party Sharing</h2>
    <p>We do not sell, trade, or share your personal information with any third parties. Your data is only accessible to the application administrators for the purposes described above.</p>

    <h2>9. Changes to This Policy</h2>
    <p>We may update this privacy policy from time to time. Any changes will be posted on this page with an updated "Last updated" date. Continued use of the application after changes are posted constitutes acceptance of the updated policy.</p>

    <h2>10. Contact</h2>
    <p>If you have questions about this privacy policy or wish to exercise your data rights, contact us at: <a href="mailto:<?= e(CONTACT_EMAIL) ?>"><?= e(CONTACT_EMAIL) ?></a></p>

    <div class="alert alert-warning" style="margin-top: var(--space-8);">
        <strong>Note for the site operator:</strong> This privacy policy is a template based on the actual functionality of this application. You should have it reviewed by a legal professional before launching publicly, as you are responsible for its legal accuracy in your jurisdiction.
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
