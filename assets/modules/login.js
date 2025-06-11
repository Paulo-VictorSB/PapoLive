export function setupLogin(form, username, error_message) {
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const body = {
            username: username.value
        }

        fetch('https://pbarbosaprojetos.byethost31.com/api/enter_user/', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin',
            body: JSON.stringify(body)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "error") {
                throw new Error(data.error_message);
            }

            window.location.href = "?route=chat";
        })
        .catch(error => {
            error_message.classList.remove('d-none');
            error_message.innerHTML = error.message;
        })
    })
}