/**
 * QuizPoint main JavaScript
 * Handles: mobile nav toggle, quiz form validation
 */
(function () {
    'use strict';

    // Mobile navigation toggle
    var toggle = document.querySelector('.nav-toggle');
    var nav = document.querySelector('.main-nav');

    if (toggle && nav) {
        toggle.addEventListener('click', function () {
            var expanded = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', String(!expanded));
            nav.classList.toggle('open');
        });

        // Close nav when clicking outside
        document.addEventListener('click', function (e) {
            if (!nav.contains(e.target) && !toggle.contains(e.target) && nav.classList.contains('open')) {
                nav.classList.remove('open');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // Quiz form: warn if submitting without answering all questions
    var quizForm = document.getElementById('quiz-form');
    if (quizForm) {
        quizForm.addEventListener('submit', function (e) {
            var questions = quizForm.querySelectorAll('.quiz-question');
            var unanswered = 0;

            questions.forEach(function (q) {
                var radios = q.querySelectorAll('input[type="radio"]');
                var checked = false;
                radios.forEach(function (r) {
                    if (r.checked) checked = true;
                });
                if (!checked) unanswered++;
            });

            if (unanswered > 0) {
                var proceed = confirm(
                    'You have ' + unanswered + ' unanswered question' +
                    (unanswered > 1 ? 's' : '') +
                    '. Submit anyway?'
                );
                if (!proceed) {
                    e.preventDefault();
                }
            }
        });
    }

    // Auto-dismiss flash alerts after 5 seconds
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 200ms ease';
            alert.style.opacity = '0';
            setTimeout(function () {
                alert.remove();
            }, 200);
        }, 5000);
    });
})();
