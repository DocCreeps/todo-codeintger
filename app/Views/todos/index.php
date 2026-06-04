<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Todo List</title>

    <!-- Tailwind CDN (simple et rapide pour CodeIgniter) -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-xl bg-white shadow-lg rounded-xl p-6">

        <!-- Header -->
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">
            Todo List
        </h1>

        <!-- Form -->
        <form action="/store" method="POST" class="flex gap-2 mb-6">
            <input
                type="text"
                name="title"
                placeholder="Ajouter une tâche..."
                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                required>

            <button
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                Ajouter
            </button>
        </form>

        <!-- List -->
        <div class="space-y-3">

            <?php foreach ($todos as $todo): ?>

                <div class="flex items-center justify-between bg-gray-50 border rounded-lg px-4 py-3">

                    <!-- Title -->
                    <span class="<?= $todo['completed'] ? 'line-through text-gray-400' : 'text-gray-800' ?>">
                        <?= esc($todo['title']) ?>
                    </span>

                    <!-- Actions -->
                    <div class="flex gap-2">

                        <!-- Complete -->
                        <a
                            href="/complete/<?= $todo['id'] ?>"
                            class="text-green-600 hover:text-green-800 font-bold"
                            title="Terminer">
                            ✓
                        </a>

                        <!-- Delete -->
                        <a
                            href="/delete/<?= $todo['id'] ?>"
                            class="text-red-600 hover:text-red-800 font-bold"
                            title="Supprimer">
                            ✕
                        </a>

                    </div>

                </div>

            <?php endforeach; ?>

            <?php if (empty($todos)): ?>
                <p class="text-center text-gray-400 mt-6">
                    Aucune tâche pour le moment
                </p>
            <?php endif; ?>

        </div>

    </div>

</body>

</html>