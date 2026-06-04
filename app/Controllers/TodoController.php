<?php

namespace App\Controllers;

use App\Models\TodoModel;

class TodoController extends BaseController
{
    public function index()
    {
        $todoModel = new TodoModel();

        return view('todos/index', [
            'todos' => $todoModel->findAll()
        ]);
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

        $todoModel->update($id, [
            'completed' => !$todo['completed']
        ]);

        return redirect()->to('/');
    }

    public function delete($id)
    {
        $todoModel = new TodoModel();

        $todoModel->delete($id);

        return redirect()->to('/');
    }
}
