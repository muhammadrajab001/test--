const form = document.getElementById('order-form');
const status = document.getElementById('form-status');

const phoneInput = form.querySelector('input[name="phone"]');
phoneInput.addEventListener('input', (e) => {
    let v = e.target.value.replace(/\D/g, '').slice(0, 11);
    if (v.startsWith('8')) v = '7' + v.slice(1);
    if (v.startsWith('7')) {
        v = '+7 ' + v.slice(1, 4) + (v.length > 4 ? ' ' + v.slice(4, 7) : '')
          + (v.length > 7 ? '-' + v.slice(7, 9) : '')
          + (v.length > 9 ? '-' + v.slice(9, 11) : '');
    }
    e.target.value = v;
});

form.addEventListener('submit', async (e) => {
    e.preventDefault();

    status.textContent = 'Отправка...';
    status.className = 'status';

    const btn = form.querySelector('button');
    btn.disabled = true;
    btn.textContent = 'Обработка...';

    try {
        const res = await fetch('order.php', {
            method: 'POST',
            body: new FormData(form)
        });
        const data = await res.json();

        if (data.success) {
            status.textContent = '✓ Заказ принят! Мы скоро свяжемся с вами.';
            status.className = 'status success';
            form.reset();
        } else {
            status.textContent = '✕ ' + (data.error || 'Ошибка');
            status.className = 'status error';
        }
    } catch (err) {
        status.textContent = '✕ Ошибка соединения';
        status.className = 'status error';
    }

    btn.disabled = false;
    btn.textContent = 'Заказать за 14 900 ₽';
});