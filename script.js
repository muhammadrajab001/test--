const API_URL = 'api.php';
const form = document.getElementById('task-form');
const input = document.getElementById('task-input');
const list = document.getElementById('task-list');
const status = document.getElementById('status');

// Загрузка задач при старте
document.addEventListener('DOMContentLoaded', loadTasks);

// Обработка формы
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const text = input.value.trim();
    if (!text) return;

    await fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'add', text })
    });

    input.value = '';
    showStatus('Задача добавлена ✓');
    loadTasks();
});

// Загрузка списка
async function loadTasks() {
    try {
        const res = await fetch(API_URL + '?action=list');
        const tasks = await res.json();
        renderTasks(tasks);
    } catch (err) {
        showStatus('Ошибка загрузки: ' + err.message);
    }
}

// Рендер задач
function renderTasks(tasks) {
    list.innerHTML = '';
    tasks.forEach(task => {
        const li = document.createElement('li');
        if (task.done) li.classList.add('done');

        const span = document.createElement('span');
        span.textContent = task.text;
        span.onclick = () => toggleTask(task.id);

        const delBtn = document.createElement('button');
        delBtn.textContent = '✕';
        delBtn.className = 'delete-btn';
        delBtn.onclick = () => deleteTask(task.id);

        li.appendChild(span);
        li.appendChild(delBtn);
        list.appendChild(li);
    });
}

// Переключить статус
async function toggleTask(id) {
    await fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'toggle', id })
    });
    loadTasks();
}

// Удалить
async function deleteTask(id) {
    await fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'delete', id })
    });
    showStatus('Задача удалена');
    loadTasks();
}

// Показать сообщение
function showStatus(msg) {
    status.textContent = msg;
    setTimeout(() => { status.textContent = ''; }, 2000);
}