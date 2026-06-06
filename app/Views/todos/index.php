<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Kanban Todo Pro</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
</head>

<body class="bg-gray-100 min-h-screen p-6">

    <div class="max-w-7xl mx-auto">

        <!-- ADD TASK -->
        <div class="bg-white p-4 rounded-xl shadow mb-6"
            x-data="{ title: '' }"
            @create-task.window="async (e) => {

            await fetch('/store', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ title: e.detail })
            });

            location.reload();
         }">

            <form class="flex gap-2"
                @submit.prevent="$dispatch('create-task', title); title=''">

                <input
                    x-model="title"
                    class="flex-1 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                    placeholder="Ajouter une tâche...">

                <button class="bg-blue-600 text-white px-4 rounded-lg hover:bg-blue-700">
                    + Ajouter
                </button>

            </form>
        </div>

        <!-- KANBAN -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4"
            x-data="kanban()"
            x-init="init()">

            <!-- TODO -->
            <div class="bg-white rounded-xl shadow p-3">
                <h2 class="font-bold mb-3 text-yellow-600">🟡 À faire</h2>

                <div id="todo" class="space-y-2 min-h-[200px]">

                    <?php foreach ($todos as $t): ?>
                        <?php if (($t['status'] ?? 'todo') === 'todo'): ?>

                            <?php
                            $color = 'bg-gray-100 hover:bg-gray-200';
                            ?>

                            <div
                                class="task flex items-center justify-between p-2 rounded shadow-sm cursor-grab active:cursor-grabbing transition group <?= $color ?>"
                                data-id="<?= $t['id'] ?>">

                                <div class="flex items-center gap-2">

                                    <!-- DRAG HANDLE -->
                                    <div class="text-gray-400 group-hover:text-gray-600 select-none cursor-grab active:cursor-grabbing">
                                        ☰
                                    </div>

                                    <span><?= esc($t['title']) ?></span>

                                </div>

                            </div>

                        <?php endif; ?>
                    <?php endforeach; ?>

                </div>
            </div>

            <!-- DOING -->
            <div class="bg-white rounded-xl shadow p-3">
                <h2 class="font-bold mb-3 text-blue-600">🔵 En cours</h2>

                <div id="doing" class="space-y-2 min-h-[200px]">

                    <?php foreach ($todos as $t): ?>
                        <?php if (($t['status'] ?? '') === 'doing'): ?>

                            <?php $color = 'bg-blue-100 hover:bg-blue-200'; ?>

                            <div
                                class="task flex items-center justify-between p-2 rounded shadow-sm cursor-grab active:cursor-grabbing transition group <?= $color ?>"
                                data-id="<?= $t['id'] ?>">

                                <div class="flex items-center gap-2">

                                    <div class="text-gray-400 group-hover:text-gray-600 select-none cursor-grab active:cursor-grabbing">
                                        ☰
                                    </div>

                                    <span><?= esc($t['title']) ?></span>

                                </div>

                            </div>

                        <?php endif; ?>
                    <?php endforeach; ?>

                </div>
            </div>

            <!-- DONE -->
            <div class="bg-white rounded-xl shadow p-3">
                <h2 class="font-bold mb-3 text-green-600">🟢 Terminé</h2>

                <div id="done" class="space-y-2 min-h-[200px]">

                    <?php foreach ($todos as $t): ?>
                        <?php if (($t['status'] ?? '') === 'done'): ?>

                            <?php $color = 'bg-green-100 hover:bg-green-200'; ?>

                            <div
                                class="task flex items-center justify-between p-2 rounded shadow-sm cursor-grab active:cursor-grabbing transition group <?= $color ?>"
                                data-id="<?= $t['id'] ?>">

                                <div class="flex items-center gap-2">

                                    <div class="text-gray-400 group-hover:text-gray-600 select-none cursor-grab active:cursor-grabbing">
                                        ☰
                                    </div>

                                    <span class="line-through text-gray-600">
                                        <?= esc($t['title']) ?>
                                    </span>

                                </div>

                            </div>

                        <?php endif; ?>
                    <?php endforeach; ?>

                </div>
            </div>

            <!-- ARCHIVED -->
            <div class="bg-white rounded-xl shadow p-3">
                <h2 class="font-bold mb-3 text-gray-600">⚫ Archivées</h2>

                <div id="archived" class="space-y-2 min-h-[200px]">

                    <?php foreach ($todos as $t): ?>
                        <?php if (($t['status'] ?? '') === 'archived'): ?>

                            <?php $color = 'bg-gray-200 opacity-70'; ?>

                            <div
                                class="task flex items-center justify-between p-2 rounded shadow-sm cursor-grab active:cursor-grabbing transition group <?= $color ?>"
                                data-id="<?= $t['id'] ?>">

                                <div class="flex items-center gap-2">

                                    <div class="text-gray-400 group-hover:text-gray-600 select-none cursor-grab active:cursor-grabbing">
                                        ☰
                                    </div>

                                    <span><?= esc($t['title']) ?></span>

                                </div>

                                <button
                                    class="text-xs text-blue-600"
                                    @click="restore(<?= $t['id'] ?>)">
                                    restore
                                </button>

                            </div>

                        <?php endif; ?>
                    <?php endforeach; ?>

                </div>
            </div>

        </div>

    </div>

    <!-- SCRIPT -->
    <script>
        function kanban() {
            return {
                init() {

                    ['todo', 'doing', 'done', 'archived'].forEach((col) => {

                        const el = document.getElementById(col);
                        if (!el) return;

                        new Sortable(el, {
                            group: 'kanban',
                            animation: 150,

                            onStart(evt) {
                                evt.item.classList.add('opacity-50', 'scale-105');
                            },

                            onEnd(evt) {
                                evt.item.classList.remove('opacity-50', 'scale-105');
                            },

                            onAdd: async (evt) => {

                                const id = evt.item.dataset.id;
                                const status = evt.to.id;

                                await fetch('/todo/move/' + id, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        status
                                    })
                                });
                            }
                        });

                    });
                },

                async restore(id) {
                    await fetch('/todo/restore/' + id, {
                        method: 'POST'
                    });

                    location.reload();
                }
            }
        }
    </script>

</body>

</html>