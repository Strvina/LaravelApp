document.addEventListener('DOMContentLoaded', () => {
    const lanes = [...document.querySelectorAll('[data-task-lane]')];
    const cards = [...document.querySelectorAll('[data-task-card]')];
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');

    if (!lanes.length || !cards.length || !csrfToken) {
        return;
    }

    let draggedTaskId = null;
    let draggedElement = null;
    let activeLane = null;
    let draggedPreview = null;
    let previewOffsetX = 0;
    let previewOffsetY = 0;

    const clearActiveLane = () => {
        if (!activeLane) {
            return;
        }

        activeLane.classList.remove('task-lane-active');
        activeLane = null;
    };

    const setActiveLane = lane => {
        if (activeLane === lane) {
            return;
        }

        clearActiveLane();

        if (!lane) {
            return;
        }

        lane.classList.add('task-lane-active');
        activeLane = lane;
    };

    const resetDraggedState = () => {
        if (draggedElement) {
            draggedElement.classList.remove('task-card-dragging');
        }

        if (draggedPreview) {
            draggedPreview.remove();
            draggedPreview = null;
        }

        clearActiveLane();
        draggedElement = null;
        draggedTaskId = null;
    };

    const startDragging = card => {
        draggedElement = card;
        draggedTaskId = card.dataset.id;
        card.classList.add('task-card-dragging');
    };

    const updateTaskStatus = status => {
        if (!draggedTaskId || !status) {
            resetDraggedState();
            return;
        }

        fetch(`/pages/todo/update-status/${draggedTaskId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ status })
        })
            .then(response => {
                if (response.ok) {
                    location.reload();
                }
            })
            .finally(() => {
                resetDraggedState();
            });
    };

    const findLaneAtPoint = (x, y) => {
        return document.elementFromPoint(x, y)?.closest('[data-task-lane]') ?? null;
    };

    const createDraggedPreview = (card, pointerEvent) => {
        const rect = card.getBoundingClientRect();
        previewOffsetX = pointerEvent.clientX - rect.left;
        previewOffsetY = pointerEvent.clientY - rect.top;

        draggedPreview = card.cloneNode(true);
        draggedPreview.classList.add('task-card-preview');
        draggedPreview.style.width = `${rect.width}px`;
        draggedPreview.style.left = `${rect.left}px`;
        draggedPreview.style.top = `${rect.top}px`;
        document.body.appendChild(draggedPreview);
    };

    const moveDraggedPreview = pointerEvent => {
        if (!draggedPreview) {
            return;
        }

        draggedPreview.style.left = `${pointerEvent.clientX - previewOffsetX}px`;
        draggedPreview.style.top = `${pointerEvent.clientY - previewOffsetY}px`;
    };

    lanes.forEach(lane => {
        lane.addEventListener('dragover', event => {
            event.preventDefault();
            setActiveLane(lane);
        });

        lane.addEventListener('dragleave', () => {
            if (activeLane === lane) {
                clearActiveLane();
            }
        });

        lane.addEventListener('drop', event => {
            event.preventDefault();
            const droppedTaskId = event.dataTransfer?.getData('text/plain');

            if (droppedTaskId) {
                draggedTaskId = droppedTaskId;
            }

            updateTaskStatus(lane.dataset.status);
        });
    });

    cards.forEach(card => {
        card.addEventListener('dragstart', event => {
            startDragging(card);

            if (event.dataTransfer) {
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData('text/plain', card.dataset.id ?? '');
            }
        });

        card.addEventListener('dragend', () => {
            resetDraggedState();
        });

        card.addEventListener('pointerdown', event => {
            if (event.pointerType === 'mouse') {
                return;
            }

            startDragging(card);
            createDraggedPreview(card, event);
            moveDraggedPreview(event);
            setActiveLane(findLaneAtPoint(event.clientX, event.clientY));
        });
    });

    document.addEventListener(
        'pointermove',
        event => {
            if (!draggedPreview || !draggedElement) {
                return;
            }

            event.preventDefault();
            moveDraggedPreview(event);
            setActiveLane(findLaneAtPoint(event.clientX, event.clientY));
        },
        { passive: false }
    );

    const finishPointerDrag = event => {
        if (!draggedPreview || !draggedElement) {
            return;
        }

        event.preventDefault();
        const lane = findLaneAtPoint(event.clientX, event.clientY);

        if (!lane) {
            resetDraggedState();
            return;
        }

        updateTaskStatus(lane.dataset.status);
    };

    document.addEventListener('pointerup', finishPointerDrag);
    document.addEventListener('pointercancel', resetDraggedState);
});
