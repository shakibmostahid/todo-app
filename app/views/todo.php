<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/custom.css">

    <title>Todo App</title>
</head>

<body>
    <h1 class="center">todos</h1>
    <div class="container mw-720">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-white">
                    <div>
                        <input id="addTask" type="text" class="form-control add-task" placeholder="What needs to be done?">
                    </div>
                    <div id="listDiv">
                        <?php foreach ($todoList as $todoItem) : ?>
                            <?php
                            ($todoItem->status) ? $totalCheckedItem++ : $totalUncheckedItem++;
                            ?>
                            <div data-id="<?= $todoItem->id ?>" class="todo-list <?= $todoItem->status ? 'task-complete' : '' ?>">
                                <div class="todo-item">
                                    <div class="checker"><span><input class="checkbox" type="checkbox" <?= $todoItem->status ? 'checked' : '' ?>></span></div>
                                    <span class="todo-span"><?= $todoItem->todo ?></span>
                                    <a href="#" class="float-right remove-todo-item"><i class="fa fa-times"></i></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="flex-container">
                        <div class="flex-item">
                            <span id="itemsRemaining" class="hide"><span id="itemCount"><?= $totalUncheckedItem ?></span> items left<span>
                        </div>
                        <div class="flex-item">
                            <ul class="nav nav-pills todo-nav">
                                <li id="all" class="nav-item"><a href="#" class="option-selected nav-link nav-options">All</a></li>
                                <li id="active" class="nav-item"><a href="#" class="nav-link nav-options">Active</a></li>
                                <li id="completed" class="nav-item"><a href="#" class="nav-link nav-options">Completed</a></li>
                            </ul>
                        </div>
                        <div class="flex-item">
                            <a id="clearCompleteItems" href="#" class="hide nav-options clear-completed">Clear Completed</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="totalTodo" id="totalTodo" value="<?= $totalUncheckedItem ?>">
        <input type="hidden" name="completedItems" id="completedItems" value="<?= $totalCheckedItem ?>">
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="assets/custom.js"></script>
</body>

</html>