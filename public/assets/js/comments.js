document.addEventListener('DOMContentLoaded', function () {
    // Reply button functionality
    const parentIdInput = document.getElementById('parent_id');
    const replyFormTitle = document.getElementById('reply-form-title');
    const replyContent = document.getElementById('reply_content');
    const cancelReplyBtn = document.getElementById('cancel-reply-btn');
    const cancelReplyFormBtn = document.getElementById('cancel-reply-form-btn');

    // Handle reply button clicks
    document.querySelectorAll('.reply-to-comment-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const commentId = this.dataset.commentId;
            const replyFormContainer = document.getElementById(`reply-form-container-${commentId}`);
            const authorName = this.dataset.commentAuthor;

            // Set form for replying
            parentIdInput.value = commentId;
            replyFormTitle.textContent = `Reply to ${authorName}`;

            // Show reply form
            replyFormContainer.style.display = 'block';

            // Scroll to reply form
            setTimeout(() => {
                replyFormContainer.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 100);

            // Focus on textarea
            setTimeout(() => {
                replyContent.focus();
                replyContent.setAttribute('placeholder', `Replying to ${authorName}...`);
            }, 200);
        });
    });

    document.querySelectorAll('.cancel-reply-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const commentId = button.dataset.commentId;
            resetReplyForm(commentId);
        });
    });

    document.querySelectorAll('.cancel-reply-form-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const commentId = button.dataset.commentId;
            const replyFormContainer = document.getElementById(`reply-form-container-${commentId}`);
            replyFormContainer.style.display = 'none';
        });
    });

    // Form submission via AJAX
    const replyForm = document.getElementById('reply-form');
    if (replyForm) {
        replyForm.addEventListener('submit', function (e) {
            e.preventDefault();
            submitReplyForm(this);
        });
    }

    // Show replies button
    document.querySelectorAll('.show-replies-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const commentId = this.dataset.commentId;
            const container = document.getElementById(`replies-container-${commentId}`);
            const isLoaded = this.dataset.loaded === 'true';

            if (!isLoaded) {
                loadReplies(commentId, 1, this);
                this.dataset.loaded = 'true';
            }

            // Toggle visibility
            if (container.style.display === 'none') {
                container.style.display = 'block';
                this.innerHTML = `<i class="fas fa-times me-1"></i>Hide Replies`;
            } else {
                container.style.display = 'none';
                const count = this.textContent.match(/\d+/)[0];
                const text = count == 1 ? 'reply' : 'replies';
                this.innerHTML = `<i class="fas fa-comments me-1"></i>${count} ${text}`;
            }
        });
    });

    // AJAX form submission
    function submitReplyForm(form) {
        const submitBtn = form.querySelector('#submit-reply-btn');
        const btnText = submitBtn.querySelector('.btn-text');
        const spinner = submitBtn.querySelector('.spinner-border');

        // Show loading state
        btnText.textContent = 'Submitting...';
        spinner.classList.remove('d-none');
        submitBtn.disabled = true;

        const formData = new FormData(form);

        fetch(`${window.APP_CONFIG.baseUrl}comment/add-reply`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');

                    // Reset form
                    form.reset();
                    resetReplyForm();

                    // Hide reply form
                    replyFormContainer.style.display = 'none';

                    // If replies were shown for this comment, reload them
                    const commentId = parentIdInput.value;
                    const repliesContainer = document.getElementById(`replies-container-${commentId}`);
                    if (repliesContainer && repliesContainer.style.display !== 'none') {
                        const showRepliesBtn = document.querySelector(`.show-replies-btn[data-comment-id="${commentId}"]`);
                        if (showRepliesBtn) {
                            loadReplies(commentId, 1, showRepliesBtn);
                        }
                    }

                    // Update reply count on button if it exists
                    updateReplyCount(commentId);

                } else {
                    showNotification(data.errors || data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Network error. Please try again.', 'error');
            })
            .finally(() => {
                // Reset button state
                btnText.textContent = 'Submit Reply';
                spinner.classList.add('d-none');
                submitBtn.disabled = false;
            });
    }

    // Load replies via AJAX
    function loadReplies(commentId, page, button) {
        const container = document.getElementById(`replies-container-${commentId}`);

        // Show loading indicator
        container.innerHTML = `
            <div class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span class="ms-2">Loading replies...</span>
            </div>
            `;

        fetch(`${window.APP_CONFIG.baseUrl}comment/load-replies/${commentId}?page=${page}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    container.innerHTML = data.html;

                    // Update button text
                    if (button) {
                        const count = data.total_replies || 0;
                        button.innerHTML = `<i class="fas fa-times me-1"></i>Hide Replies (${count})`;
                    }

                    // Attach event listeners to reply buttons inside replies
                    attachReplyButtonsToReplies();
                } else {
                    container.innerHTML = `
                    <div class="alert alert-danger">
                        Failed to load replies. Please try again.
                    </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                container.innerHTML = `
                <div class="alert alert-danger">
                    Error loading replies. Please try again.
                </div>
            `;
            });
    }

    // Attach reply buttons to replies (for nested replies if needed)
    function attachReplyButtonsToReplies() {
        // You can implement nested replies here if needed
    }

    // Update reply count on button
    function updateReplyCount(commentId) {
        const showRepliesBtn = document.querySelector(`.show-replies-btn[data-comment-id="${commentId}"]`);
        if (showRepliesBtn) {
            const currentMatch = showRepliesBtn.textContent.match(/\d+/);
            if (currentMatch) {
                const currentCount = parseInt(currentMatch[0]);
                const newCount = currentCount + 1;
                const text = newCount == 1 ? 'reply' : 'replies';
                showRepliesBtn.innerHTML = `<i class="fas fa-comments me-1"></i>${newCount} ${text}`;
            }
        }
    }

    // Show notification
    function showNotification(message, type) {
        // Remove existing notifications
        const existing = document.querySelector('.custom-notification');
        if (existing) existing.remove();

        // Create notification
        const notification = document.createElement('div');
        notification.className = `custom-notification alert alert-${type} alert-dismissible fade show`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;

        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    // Reset reply form
    function resetReplyForm(commentId) {
        const replyFormContainer = document.getElementById(`reply-form-container-${commentId}`);
        parentIdInput.value = '';
        replyFormTitle.textContent = 'Reply to Comment';
        replyContent.setAttribute('placeholder', 'Write your reply here...');
        replyFormContainer.style.display = 'none';

        // Also reset the form fields
        if (replyForm) {
            replyForm.reset();
        }
    }
});