document.addEventListener('DOMContentLoaded', function () {
    let draggedTaskId = null;

    window.drag = function (event) {
        draggedTaskId = event.target.dataset.id;
    };

    window.allowDrop = function (event) {
        event.preventDefault();
    };

    window.drop = function (event) {
        event.preventDefault();
        const status = event.currentTarget.dataset.status;
        if (!draggedTaskId) return;

        fetch(`/pages/todo/update-status/${draggedTaskId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content')
            },
            body: JSON.stringify({ status })
        }).then(res => {
            if (res.ok) location.reload();
        });

        draggedTaskId = null;
    };
});
