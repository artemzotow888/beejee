<?php

if (isset($_GET['type'])) {

    include('db.php'); // Подключение к базе данных
    include('tasks.php'); // Подключение модели задач

    $task = new Tasks($pdo); //Создаем класс задач

    switch ($_GET['type']) {
        case 'add': // Добавление задачи
            echo json_encode($task->add_task());
            break;
        case 'edit': // Редактирование задачи
            echo json_encode($task->edit_task());
            break;
        case 'delete': // Удаление задачи
            echo json_encode($task->delete_task());
            break;
        case 'list': // Вывод списка задачь
            echo json_encode($task->show_tasks());
            break;
        case 'authorization': // Авторизация
            echo json_encode($task->authorization());
            break;
        case 'logout_authorization': // Выход с авторизации
            echo json_encode($task->logout_authorization());
            break;
        case 'check_authorization': // Проверка авторизации
            echo json_encode($task->check_authorization());
        break;
    }
}
