<div class="card text-center">
    <h1 class="mb-4">PapoLive</h1>
    <form id="form">
        <div class="form-group mb-3">
            <label for="nome" class="form-label">Enter your name: </label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Your name" required minlength="5" maxlength="20">
            <span class="text-danger"></span>
        </div>
        <button type="submit" class="btn btn-primary btn-md w-100">Open</button>
    </form>
</div>

<script>
    let form = document.querySelector('#form');

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        let username = document.querySelector('#username');

        const body = {
            username: username.value
        }

        fetch('http://localhost/PapoLive/api/enter_user/', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(body)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "error") {
                throw new Error(data.error_message);
            }

            <?= $_SESSION['username'] = data.data ?> 
        })

    })
</script>