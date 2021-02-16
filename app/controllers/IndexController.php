<?php

namespace Controllers;

use Core\Controller;

use Exception;

class IndexController extends Controller
{
    /**
     * view a list of TODO works
     */
    public function indexAction()
    {
        $todoList = $this->database->query('select * from todo_lists')->findAll();

        $this->render(
            'todo',
            [
                'todoList' => $todoList,
                'totalCheckedItem' => 0,
                'totalUncheckedItem' => 0
            ]
        );
    }

    /**
     * add todo work action
     * 
     * @throws Exception on invalid HTTP methods or invalid data
     */
    public function addTodoAction()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                throw new Exception('Post expected Get received', 405);
            }

            if (!isset($_POST['text']) || !$_POST['text']) {
                throw new Exception('Todo text can not be blank', 400);
            }
            if (!isset($_POST['status'])) {
                throw new Exception('Todo status can not be blank', 400);
            }

            $text = $_POST['text'];
            $status = filter_var($_POST['status'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

            $this->database->query('INSERT INTO todo_lists SET todo=:todo, status=:status')
                ->bindParams([':todo' => $text, ':status' => $status])
                ->execute();

            $id = $this->database->getLastInsertedId();

            $this->jsonRender(200, ['success' => true, 'id' => $id]);
        } catch (Exception $e) {
            $this->jsonRender($e->getCode(), ['error' => $e->getMessage()]);
        }
    }

    /**
     * update todo work for a given id
     * 
     * @param int $id is todo work id
     * 
     * @throws Exception on invalid HTTP methods or invalid data
     */
    public function updateTodoAction($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] != 'PUT') {
                throw new Exception('Post expected Get received', 405);
            }

            $data = $this->getRequestBody();

            if (!isset($data['text']) || !$data['text']) {
                throw new Exception('Todo text can not be blank', 400);
            }
            if (!isset($data['status'])) {
                throw new Exception('Todo status can not be blank', 400);
            }

            $todo = $this->database->query('SELECT * FROM todo_lists WHERE id=:id')
                ->bindParams([':id' => $id])
                ->find();

            if (!$todo) {
                throw new Exception("This item doesn't exist!", 404);
            }

            $text = $data['text'];
            $status = filter_var($data['status'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

            $this->database->query('UPDATE todo_lists SET todo=:todo, status=:status WHERE id=:id')
                ->bindParams([':todo' => $text, ':status' => $status, ':id' => $id])
                ->execute();

            $this->jsonRender(200, ['success' => true]);
        } catch (Exception $e) {
            $this->jsonRender($e->getCode(), ['error' => $e->getMessage()]);
        }
    }

    /**
     * removes multiple todo work
     * 
     * @throws Exception on invalid HTTP methods or invalid data
     */
    public function deleteTodoAction()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] != 'DELETE') {
                throw new Exception('Post expected Get received', 405);
            }

            $data = $this->getRequestBody();

            if (!isset($data['ids']) || !is_array($data['ids'])) {
                throw new Exception('Invalid data pattern received', 405);
            }
            $ids = implode(', ', $data['ids']);

            $this->database->query("DELETE FROM todo_lists WHERE id in ($ids)")
                ->execute();

            $this->jsonRender(200, ['success' => true]);
        } catch (Exception $e) {
            $this->jsonRender($e->getCode(), ['error' => $e->getMessage()]);
        }
    }
}
