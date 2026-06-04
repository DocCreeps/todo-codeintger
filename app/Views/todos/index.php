<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Todo List</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
</head>

<body class="bg-gray-100 min-h-screen flex justify-center py-10">

    <div
        x-data="todoApp()"
        x-init="init()"
        class="w-full max-w-2xl bg-white shadow-xl rounded-2xl p-6">

        <!-- HEADER -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Todo List</h1>
        </div>

        <!-- INPUT -->
        <form class="flex gap-2 mb-6" @submit.prevent="addTodo">
            <input
                x-model="newTodo"
                name="title"
                class="flex-1 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                placeholder="Nouvelle tâche...">

            <button class="bg-blue-600 text-white px-4 rounded-lg hover:bg-blue-700">
                Ajouter
            </button>
        </form>

        <!-- LIST -->
        <div id="todo-list" class="space-y-2">

            <?php foreach ($todos as $todo): ?>

                <div
                    class="flex items-center justify-between bg-gray-50 border rounded-lg px-4 py-3 group hover:bg-gray-100 transition"
                    data-id="<?= $todo['id'] ?>">

                    <!-- LEFT -->
                    <div class="flex items-center gap-3">

                        <!-- DRAG HANDLE -->
                        <div class="cursor-move text-gray-400 hover:text-gray-600 select-none">
                            ☰
                        </div>

                        <!-- STATUS DOT -->
                        <div
                            class="w-2.5 h-2.5 rounded-full"
                            :class="completedIds.includes(<?= $todo['id'] ?>)
                            ? 'bg-green-500'
                            : 'bg-gray-300'"></div>

                        <!-- TITLE -->
                        <span
                            class="cursor-pointer select-none transition"
                            :class="completedIds.includes(<?= $todo['id'] ?>)
                            ? 'line-through text-gray-400'
                            : 'text-gray-800'"
                            @click="toggle(<?= $todo['id'] ?>)">
                            <?= esc($todo['title']) ?>
                        </span>

                    </div>

                    <!-- ACTIONS -->
                    <div class="flex items-center gap-3 opacity-0 group-hover:opacity-100 transition">

                        <button
                            @click="toggle(<?= $todo['id'] ?>)"
                            class="text-xs px-2 py-1 rounded bg-gray-200 hover:bg-gray-300">
                            <?= $todo['completed'] ? '↩' : '✓' ?>
                        </button>

                        <button
                            @click="openModal(<?= $todo['id'] ?>)"
                            class="text-xs px-2 py-1 rounded bg-red-100 text-red-600 hover:bg-red-200">
                            🗑
                        </button>

                    </div>

                </div>

            <?php endforeach; ?>

            <?php if (empty($todos)): ?>
                <div class="text-center text-gray-400 py-10">
                    Aucune tâche
                </div>
            <?php endif; ?>

        </div>

        <!-- MODAL DELETE -->
        <div
            x-show="modalOpen"
            x-transition
            class="fixed inset-0 bg-black/40 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg w-80 shadow-xl">

                <p class="mb-4 text-gray-700">
                    Supprimer cette tâche ?
                </p>

                <div class="flex justify-end gap-2">
                    <button
                        @click="modalOpen=false"
                        class="px-3 py-1 rounded bg-gray-200">
                        Annuler
                    </button>

                    <button
                        @click="deleteTodo()"
                        class="px-3 py-1 rounded bg-red-600 text-white">
                        Supprimer
                    </button>
                </div>

            </div>
        </div>

        <!-- TOAST -->
        <div
            x-show="toast.show"
            x-transition
            class="fixed bottom-5 right-5 bg-gray-900 text-white px-4 py-2 rounded-lg shadow-lg">
            <div class="flex items-center gap-2">
                <span class="text-green-400">✔</span>
                <span x-text="toast.message"></span>
            </div>
        </div>

    </div>

    <!-- SCRIPT -->
    <script>
        function todoApp() {
            return {
                newTodo: '',
                modalOpen: false,
                deleteId: null,

                completedIds: [],
                toast: {
                    show: false,
                    message: ''
                },

                init() {
                    new Sortable(document.getElementById('todo-list'), {
                        animation: 200,
                        handle: '.cursor-move',

                        onStart: (evt) => {
                            evt.item.classList.add('opacity-50');
                        },

                        onEnd: async (evt) => {
                            evt.item.classList.remove('opacity-50');

                            const ids = [...document.querySelectorAll('#todo-list [data-id]')]
                                .map(el => el.dataset.id);

                            await fetch('/todo/reorder', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    items: ids
                                })
                            });

                            this.notify('Ordre mis à jour ✔');
                        }
                    });
                },

                async toggle(id) {
                    const el = document.querySelector(`[data-id="${id}"]`);

                    el.classList.add('scale-[1.01]', 'transition');

                    setTimeout(() => el.classList.remove('scale-[1.01]'), 150);

                    if (this.completedIds.includes(id)) {
                        this.completedIds = this.completedIds.filter(i => i !== id);
                    } else {
                        this.completedIds.push(id);
                    }

                    await fetch('/todo/toggle/' + id, {
                        method: 'POST'
                    });

                    this.notify('Statut mis à jour ✔');
                },

                openModal(id) {
                    this.deleteId = id;
                    this.modalOpen = true;
                },

                async deleteTodo() {
                    await fetch('/todo/delete/' + this.deleteId, {
                        method: 'DELETE'
                    });

                    document.querySelector(`[data-id="${this.deleteId}"]`).remove();

                    this.modalOpen = false;
                    this.notify('Tâche supprimée 🗑');
                },

                async addTodo() {
                    await fetch('/store', {
                        method: 'POST',
                        body: new FormData(document.querySelector('form'))
                    });

                    this.notify('Ajouté ✔');
                    location.reload();
                },

                notify(message) {
                    this.toast.message = message;
                    this.toast.show = true;

                    setTimeout(() => {
                        this.toast.show = false;
                    }, 2000);
                }
            }
        }
    </script>

</body>

</html>