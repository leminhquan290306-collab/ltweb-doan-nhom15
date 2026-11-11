document.querySelectorAll('.form-them-gio').forEach(form => {
    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        const csrfToken = document.querySelector(
            'meta[name="csrf-token"]'
        )?.content || '';

        try {
            const response = await fetch('gio-hang.php', {
                method: 'POST',
                headers: {
                    'X-CSRF-Token': csrfToken
                },
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.success) {
                const badge = document.getElementById('so-luong-bong-bong');

                if (badge) {
                    badge.textContent = result.tongSoLuong;
                }

                const bubble = document.querySelector(
                    '#bong-bong-gio-hang a'
                );

                if (bubble) {
                    bubble.animate(
                        [
                            { transform: 'scale(1)' },
                            { transform: 'scale(1.2)' },
                            { transform: 'scale(1)' }
                        ],
                        {
                            duration: 200
                        }
                    );
                }
            } else {
                alert(
                    'Có lỗi xảy ra: ' +
                    (result.message || 'Không thể thêm vào giỏ')
                );
            }
        } catch (error) {
            console.error('Lỗi:', error);
            this.submit();
        }
    });
});