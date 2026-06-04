<?php

namespace App\Controllers;

use App\Models\TodoModel;

class TodoController extends BaseController
{
    public function index()
    {
        $todoModel = new TodoModel();

        $filter = $this->request->getGet('filter');

        $query = $todoModel;

        if ($filter === 'active') {
            $query = $query->where('completed', 0);
        }

        if ($filter === 'completed') {
            $query = $query->where('completed', 1);
        }

        $todos = $query->findAll();

        return view('todos/index', [
            'todos' => $todos,
            'filter' => $filter ?? 'all',
            'count'  => $todoModel->countAllResults(),
        ]);
    }

    public function clearCompleted()
    {
        $todoModel = new TodoModel();
        $todoModel->where('completed', 1)->delete();

        return redirect()->to('/');
    }

    public function store()
    {
        $todoModel = new TodoModel();

        $todoModel->insert([
            'title' => $this->request->getPost('title'),
        ]);

        return redirect()->to('/');
    }

    public function complete($id)
    {
        $todoModel = new TodoModel();

        $todo = $todoModel->find($id);

        if (!$todo) {
            return redirect()->to('/')->with('error', 'Tâche introuvable');
        }

        $todoModel->update($id, [
            'completed' => !$todo['completed']
        ]);

        return redirect()->to('/');
    }

    public function delete($id)
    {
        $todo = new TodoModel();
        $todo->delete($id);

        return $this->response->setJSON(['success' => true]);
    }
    
    public function toggle($id)
    {
        $todo = new TodoModel();

        $task = $todo->find($id);
        if (!$task) return $this->response->setStatusCode(404);

        $todo->update($id, [
            'completed' => !$task['completed']
        ]);

        return $this->response->setJSON(['success' => true]);
    }
    public function reorder()
    {
        $todo = new TodoModel();

        $items = $this->request->getJSON(true)['items'];

        foreach ($items as $index => $id) {
            $todo->update($id, ['position' => $index]);
        }

        return $this->response->setJSON(['success' => true]);
    }
}
