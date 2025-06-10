export function openPasswordModal() {
    const modal = new bootstrap.Modal(document.getElementById('roomPasswordModal'));
    modal.show();
}

export function closePasswordModal() {
    const modalElement = document.getElementById('roomPasswordModal');
    const modalInstance = bootstrap.Modal.getInstance(modalElement);
    if (modalInstance) {
        modalInstance.hide();
    }
    modalElement.querySelectorAll('input').forEach(input => {
        input.value = '';
    })
}

export function closeRoomModal() {
    const modalInstance = bootstrap.Modal.getInstance(modalCreateRoom);
    if (modalInstance) {
        modalInstance.hide();
    }
    modalCreateRoom.querySelectorAll('input').forEach(input => {
        if (input.type === 'checkbox') {
            input.checked = null;
            password_field.classList.add('d-none');
        } else {
            input.value = '';
        }
    })
}