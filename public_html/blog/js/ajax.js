document.addEventListener('DOMContentLoaded', function() {
    // Отправка комментария
    const submitBtn = document.getElementById('submit-comment');
    if (submitBtn) {
        submitBtn.addEventListener('click', function() {
            const content = document.getElementById('comment-text').value;
            if (!content.trim()) {
                showMessage('Введите текст комментария', 'error');
                return;
            }
            fetch('add_comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ post_id: postId, content: content })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const commentHtml = `
                        <div class="comment" data-id="${data.comment.id}">
                            <strong>${escapeHtml(data.comment.username)}</strong> <small>${data.comment.created_at}</small>
                            <p>${escapeHtml(data.comment.content)}</p>
                            ${isAdmin ? '<button class="delete-comment" data-id="'+data.comment.id+'">Удалить</button>' : ''}
                        </div>
                    `;
                    document.getElementById('comments-list').insertAdjacentHTML('beforeend', commentHtml);
                    document.getElementById('comment-text').value = '';
                    showMessage('Комментарий добавлен', 'success');
                } else {
                    showMessage(data.error, 'error');
                }
            });
        });
    }

    // Удаление комментария (для админа)
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-comment')) {
            if (!confirm('Удалить комментарий?')) return;
            const commentId = e.target.dataset.id;
            fetch('admin/delete_comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: commentId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    e.target.closest('.comment').remove();
                } else {
                    alert('Ошибка удаления');
                }
            });
        }
    });
});

function showMessage(msg, type) {
    const div = document.getElementById('comment-message');
    if (div) {
        div.textContent = msg;
        div.className = type;
        setTimeout(() => div.textContent = '', 3000);
    }
}

function escapeHtml(str) {
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}