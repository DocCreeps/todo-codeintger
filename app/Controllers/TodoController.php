<?php

namespace App\Controllers;

use App\Models\TodoModel;

class TodoController extends BaseController
{
    public function index()
    {
        $todoModel = new TodoModel();

        $todos = $todoModel
            ->orderBy('position', 'ASC')
            ->findAll();

        return view('todos/index', [
            'todos' => $todos,
            'count' => $todoModel->countAll(),
        ]);
    }

    public function store()
{
    $todoModel = new TodoModel();

    $title = $this->request->getPost('title')
        ?? $this->request->getJSON(true)['title']
        ?? null;

    if (!$title) {
        return $this->response->setStatusCode(400);
    }

    $todoModel->insert([
        'title' => $title,
        'status' => 'todo',
        'position' => 0
    ]);

    return $this->response->setJSON(['success' => true]);
}

    /**
     * MOVE BETWEEN COLUMNS (KANBAN CORE)
     */
    public function move($id)
    {
        $todoModel = new TodoModel();

        $status = $this->request->getJSON(true)['status'] ?? null;

        if (!$status) {
            return $this->response->setStatusCode(400);
        }

        $todoModel->update($id, [
            'status' => $status
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * REORDER INSIDE COLUMN
     */
    public function reorder()
    {
        $todoModel = new TodoModel();

        $items = $this->request->getJSON(true)['items'] ?? [];

        foreach ($items as $index => $id) {
            $todoModel->update($id, [
                'position' => $index
            ]);
        }

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * SOFT DELETE = ARCHIVE
     */
    public function archive($id)
    {
        $todoModel = new TodoModel();

        $todoModel->update($id, [
            'status' => 'archived'
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * RESTORE FROM ARCHIVE
     */
    public function restore($id)
    {
        $todoModel = new TodoModel();

        $todoModel->update($id, [
            'status' => 'todo'
        ]);

        return $this->response->setJSON(['success' => true]);
    }
}
