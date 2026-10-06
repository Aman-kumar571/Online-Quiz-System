<?php
/**
 * Terms and Conditions page.
 */

require_once __DIR__ . '/includes/helpers.php';

$pageTitle       = 'Terms and Conditions';
$pageDescription = 'Terms and Conditions for using ' . SITE_NAME . '.';
$canonicalPath   = '/terms.php';

require __DIR__ . '/includes/header.php';
?>

<div class="legal-page">
    <h1>Terms and Conditions</h1>
    <p class="last-updated">Last updated: 1 October 2026</p>

    <p>These terms and conditions ("Terms") govern your use of <?= e(SITE_NAME) ?> ("the application", "the service"), a web-based quiz platform. By creating an account or using the application, you agree to be bound by these Terms.</p>

    <h2>1. Description of Service</h2>
    <p><?= e(SITE_NAME) ?> is an online quiz platform that allows registered users to take multiple-choice quizzes on various computer science topics. The application records quiz scores and provides users with a history of their attempts. Administrators can create, edit, and manage quizzes, categories, questions, and view results.</p>

    <h2>2. User Accounts</h2>
    <p>To take quizzes and have your scores recorded, you must create an account by providing your name, email address, and a password. You are responsible for:</p>
    <ul>
        <li>Providing accurate information during registration.</li>
        <li>Keeping your password secure and not sharing it with others.</li>
        <li>All activity that occurs under your account.</li>
    </ul>
    <p>You may only create one account per email address. We reserve the right to suspend or delete accounts that violate these Terms.</p>

    <h2>3. Acceptable Use</h2>
    <p>When using this application, you agree not to:</p>
    <ul>
        <li>Attempt to gain unauthorized access to administrator features or other users' accounts.</li>
        <li>Submit automated or scripted quiz attempts.</li>
        <li>Attempt to exploit security vulnerabilities, including SQL injection, cross-site scripting, or session hijacking.</li>
        <li>Use the application to distribute harmful, offensive, or illegal content.</li>
        <li>Interfere with the normal operation of the application or its infrastructure.</li>
        <li>Scrape, copy, or redistribute quiz content without permission.</li>
    </ul>

    <h2>4. Quiz Content and Accuracy</h2>
    <p>Quiz questions and answers are created by the application administrators. While we make reasonable efforts to ensure that all questions and their marked correct answers are factually accurate, we do not guarantee the accuracy, completeness, or educational value of any quiz content. The quizzes are provided for practice and self-assessment purposes only.</p>
    <p>Quiz content should not be treated as a substitute for formal academic instruction, official study materials, or professional certification.</p>

    <h2>5. Scores and Results</h2>
    <p>Your quiz scores are calculated automatically by the server at the time of submission. Scores are determined by comparing your submitted answers with the stored correct answers. The application does not accept client-side scoring. Scores are final and reflect the answers submitted at the time of the attempt.</p>

    <h2>6. Intellectual Property</h2>
    <p>All content within this application, including quiz questions, interface design, and source code, is the property of the application operator or its contributors. You may not reproduce, distribute, or create derivative works from any part of the application without explicit written permission.</p>

    <h2>7. Privacy</h2>
    <p>Your use of the application is also governed by our <a href="<?= e(BASE_URL) ?>/privacy.php">Privacy Policy</a>, which describes how we collect, use, and protect your personal information.</p>

    <h2>8. Limitation of Liability</h2>
    <p>The application is provided "as is" and "as available" without warranties of any kind, either express or implied. To the fullest extent permitted by law:</p>
    <ul>
        <li>We do not warrant that the application will be uninterrupted, error-free, or secure.</li>
        <li>We are not liable for any direct, indirect, incidental, or consequential damages arising from your use of the application.</li>
        <li>We are not responsible for any academic decisions, grades, or outcomes based on quiz results obtained through this application.</li>
        <li>We are not liable for any loss of data, including quiz scores or account information, due to technical failures or circumstances beyond our control.</li>
    </ul>

    <h2>9. Account Termination</h2>
    <p>You may stop using the application at any time. You can delete your account and associated data directly through the self-service account deletion page in your account, or by contacting us at the email address listed below. We reserve the right to suspend or terminate your account if you violate these Terms, without prior notice.</p>

    <h2>10. Changes to These Terms</h2>
    <p>We may modify these Terms at any time by posting the updated version on this page with a new "Last updated" date. Your continued use of the application after changes are posted constitutes your acceptance of the revised Terms. If you do not agree with the changes, you should stop using the application and request account deletion.</p>

    <h2>11. Governing Law</h2>
    <p>These Terms are governed by the laws of the jurisdiction in which the application operator is located. Any disputes arising from these Terms or your use of the application shall be resolved in the courts of that jurisdiction.</p>

    <h2>12. Contact</h2>
    <p>For questions about these Terms, contact us at: <a href="mailto:<?= e(CONTACT_EMAIL) ?>"><?= e(CONTACT_EMAIL) ?></a></p>

    <div class="alert alert-warning" style="margin-top: var(--space-8);">
        <strong>Note for the site operator:</strong> These terms and conditions are a template based on the actual functionality of this application. You should have them reviewed by a legal professional before launching publicly, as you are responsible for their legal accuracy in your jurisdiction.
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
