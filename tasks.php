<?php

class Tasks {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        if (empty($_SESSION)) {
            session_start();
        }
    }

    public function add_task() {
        $json     = null;
        $results  = "";
        $name     = $_POST['name'];
        $email    = $_POST['email'];
        $descr    = $_POST['descr'];
        $send     = true;
        if (empty($name)) {
            $send = false;
            $results .= "Имя пользователя не заполнено.";
        }
        if (empty($email)) {
            $send = false;
            $results .= " Email не заполнен.";
        } elseif (!preg_match("/^(?:[a-z0-9]+(?:[-_.]?[a-z0-9]+)?@[a-z0-9_.-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,5})$/i", $email)) {
            $send = false;
            $results .= " Email указан не валидно.";
        }
        if (empty($descr)) {
            $send = false;
            $results .= " Текст задачи не заполнен.";
        } else {
            $descr = strip_tags($descr);
            $descr = htmlentities($descr, ENT_QUOTES, "UTF-8");
            $descr = htmlspecialchars($descr, ENT_QUOTES);
        }
        if ($send) {
            $stmt = $this->pdo->prepare("INSERT INTO tasks SET  name=:name, email=:email, descr=:descr");
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':descr', $descr, PDO::PARAM_STR);
            $stmt->execute();
            $json['success'] = "<center><div class='success'>Задача добавлена!</div></center>";
        } else {
            $json['error'] = "<div class='error'>$results</div>";
        }

        return $json;
    }

    public function edit_task() {
        $json       = null;
        $tid        = (int)$_POST['tid'];
        $descr      = $_POST['descr'];
        $descr = strip_tags($descr);
        $descr = htmlentities($descr, ENT_QUOTES, "UTF-8");
        $descr = htmlspecialchars($descr, ENT_QUOTES);
        $old_descr  = $_POST['old_descr'];
        $status     = $_POST['status'];
        $check_auth = $this->check_authorization();
        if (!$check_auth['auth']) {
            $json['error'] = "Необходимо выполнить авторизацию!";
        } elseif ($tid > 0) {
            if ($status == 'yes') $status = 'выполнена';
            else $status = '';
            if ($descr != $old_descr) $status .= ' отредактировано администратором';
            $stmt = $this->pdo->prepare("UPDATE tasks SET descr=:descr, status=:status WHERE id=:tid");
            $stmt->bindParam(':descr', $descr, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':tid', $tid, PDO::PARAM_INT);
            $stmt->execute();
            $json['success'] = "<center><div class='success'>Задача №$tid отредактирована!</div></center>";
        } else {
            $json['error'] = "Системная ошибка!";
        }

        return $json;
    }

    public function delete_task() {
        $json     = null;
        $tid      = (int)$_POST['tid'];
        if ($tid > 0) {
            $stmt = $this->pdo->prepare("DELETE FROM tasks WHERE id=:tid");
            $stmt->bindParam(':tid', $tid, PDO::PARAM_INT);
            $stmt->execute();
            $json['success'] = "yes";
        } else {
            $json['error'] = "Системная ошибка!";
        }

        return $json;
    }

    public function show_tasks() {
        $json    = null;
        $results = '';
        $sort    = ' ORDER BY id DESC';
        if (isset($_POST['sort'])) {
            if ($_POST['sort'] == 'name_up') $sort = ' ORDER BY name DESC';
            elseif ($_POST['sort'] == 'name_down') $sort = ' ORDER BY name ASC';
            elseif ($_POST['sort'] == 'email_up') $sort = ' ORDER BY email DESC';
            elseif ($_POST['sort'] == 'email_down') $sort = ' ORDER BY email ASC';
            elseif ($_POST['sort'] == 'status_up') $sort = ' ORDER BY status DESC';
            elseif ($_POST['sort'] == 'status_down') $sort = ' ORDER BY status ASC';
        }
        $check_auth = $this->check_authorization();
        $stmtm = $this->pdo->query('SELECT COUNT(*) FROM tasks');
        $count_tasks = $stmtm->fetchColumn();
        $pages = '';
        $num = 3;
        if ($count_tasks > $num) {
            $page = $_POST['page'];
            $total = intval(($count_tasks - 1) / $num) + 1;
            if($page < 0) $page = 1;
            if($page > $total) $page = $total;
            $start = $page * $num - $num;
            $limit = " LIMIT $start, $num";
            $stmtm = $this->pdo->query('SELECT id, name, email, descr, status FROM tasks'. $sort . $limit);
            for($i = 1; $i <= $total; $i++) {
                if ($i == $page) $pages .= "$i&nbsp;&nbsp;&nbsp;";
                else $pages .= "<a href='javascript: void(0);' onclick=\"$('#page').val($i); Show_tasks();\">$i</a>&nbsp;&nbsp;&nbsp;";
            }
        } else $stmtm = $this->pdo->query('SELECT id, name, email, descr, status FROM tasks' . $sort);
        $stmtm->execute();
        while(list($tid, $name, $email, $descr, $status) = $stmtm->fetch(PDO::FETCH_LAZY)) {
            if ($check_auth['auth']) {
                $descr = "<textarea name='descr' id='descr_$tid'>$descr</textarea><input type='hidden' name='old_descr' value='$descr' id='old_descr_$tid'>";
                $checked = preg_match('/выполнена/', $status) ? 'checked' : '';
                $status = 'Выполнено:<br><input type="checkbox" name="status" value="1" id="status_'.$tid.'" '.$checked.'>';
                $admin_botton = '<button type="button" class="btn btn-primary" onclick="Edit_task('.$tid.');">Сохранить</button>'; //<br><button type="button" class="btn btn-danger" onclick="Remove_task('.$tid.');">Удалить</button>
            } else $admin_botton = '';
            $results .= '
            <div class="flex-block" id="task-'.$tid.'">
                <div class="flex-item">'.$name.'</div>
                <div class="flex-item">'.$email.'</div>
                <div class="flex-item">'.$descr.'</div>
                <div class="flex-item">'.$status.'</div>
                <div class="flex-item-delete">'.$admin_botton.'</div>
            </div>
            <hr>
            ';
        }
        $results .= $pages;
        if ($results == '') $results = '<center>Нет задач</center>';
        $json['success'] = $results;

        return $json;
    }

    public function authorization() {
        $json     = null;
        $login    = $_POST['login'];
        $password = $_POST['password'];
        $send     = true;
        if (empty($login)) {
            $send = false;
            $results .= "Имя пользователя не заполнено.";
        } elseif($login != 'admin') {
            $send = false;
            $results .= "Неправильный логин доступа.";
        }
        if (empty($password)) {
            $send = false;
            $results .= " Пароль не заполнен.";
        } elseif($password != '123') {
            $send = false;
            $results .= " Неправильный пароль доступа.";
        }
        if ($send) {
            $_SESSION['login']    = $login;
            $_SESSION['password'] = $password;
            $json['success'] = "<center><div class='success'>Авторизация прошла успешно!</div></center>";
        } else {
            $json['error'] = "<div class='error'>$results</div>".$_SESSION['login'];
        }

        return $json;
    }

    public function check_authorization() {
        $json = null;
        $send = true;
        if (empty($_SESSION['login']) || $_SESSION['login'] != 'admin') {
            $send = false;
        }
        if (empty($_SESSION['password']) || $_SESSION['password'] != '123') {
            $send = false;
        }
        if ($send) {
            $json['auth']    = true;
            $json['success'] = "<center><div class='success'>Вход выполнен пользователем admin.</div></center>";
        } else $json['auth'] = false;

        return $json;
    }

    public function logout_authorization() {
        $json = null;
        unset($_SESSION['login']);
        unset($_SESSION['password']);
        $json['success'] = "<center><div class='success'>Выход выполнен успешно!</div></center>";

        return $json;
    }

}
