/**
 * Tệp: thanhvien/3120224032_dung/js/canhan.js
 * Tác giả: Nguyễn Phạm Tiến Dũng (MSSV: 3120224032 - Lớp 24CNTT2)
 * Mô tả chức năng:
 *  1. Đồng hồ đếm ngược thời gian thực đến ngày thi cuối kỳ (sử dụng setInterval).
 *  2. Sao chép địa chỉ email cá nhân vào Clipboard (navigator.clipboard) kèm thông báo tự ẩn.
 * Cách thử nghiệm:
 *  - Tương tác 1: Quan sát bộ đếm giây tự động thay đổi mỗi giây trên trang.
 *  - Tương tác 2: Bấm nút "Sao chép Email", kiểm tra dòng thông báo xuất hiện và thử dán (Ctrl+V) ra ngoài.
 */

document.addEventListener('DOMContentLoaded', () => {
  // ==========================================
  // TƯƠNG TÁC 1: ĐẾM NGƯỜC NGÀY THI CUỐI KỲ
  // ==========================================
  const targetDate = new Date('2027-01-15T07:30:00').getTime();

  const elDays = document.getElementById('cd-days');
  const elHours = document.getElementById('cd-hours');
  const elMinutes = document.getElementById('cd-minutes');
  const elSeconds = document.getElementById('cd-seconds');
  const elStatus = document.getElementById('countdown-status');

  function updateCountdown() {
    const now = new Date().getTime();
    const distance = targetDate - now;

    if (distance < 0) {
      if (elStatus) elStatus.textContent = '🎉 Đã đến giờ thi! Chúc bạn thi tốt!';
      return;
    }

    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    if (elDays) elDays.textContent = String(days).padStart(2, '0');
    if (elHours) elHours.textContent = String(hours).padStart(2, '0');
    if (elMinutes) elMinutes.textContent = String(minutes).padStart(2, '0');
    if (elSeconds) elSeconds.textContent = String(seconds).padStart(2, '0');

    if (elStatus) {
      elStatus.textContent = `🎯 Cố gắng lên! Còn ${days} ngày nữa là kết thúc học kỳ.`;
    }
  }

  if (elDays && elHours && elMinutes && elSeconds) {
    updateCountdown();
    setInterval(updateCountdown, 1000);
  }

  // ==========================================
  // TƯƠNG TÁC 2: SAO CHÉP EMAIL VÀO CLIPBOARD
  // ==========================================
  const btnCopy = document.getElementById('btn-copy-email');
  const emailText = document.getElementById('my-email');
  const statusText = document.getElementById('copy-status');

  if (btnCopy && emailText && statusText) {
    btnCopy.addEventListener('click', async () => {
      try {
        const val = emailText.textContent.trim();
        await navigator.clipboard.writeText(val);

        statusText.textContent = '✅ Đã sao chép email vào bộ nhớ tạm!';
        statusText.style.color = '#16a34a';

        setTimeout(() => {
          statusText.textContent = '';
        }, 3000);
      } catch (err) {
        statusText.textContent = '❌ Không thể sao chép!';
        statusText.style.color = '#dc2626';
        console.error('Lỗi khi thao tác với Clipboard:', err);
      }
    });
  }
});