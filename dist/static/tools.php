<?php

if (isset($_GET['data'])) {
  $tasks = json_decode(file_get_contents('tasks_json'), true);
  $tracks = json_decode(file_get_contents('tracks_json'), true);
  echo json_encode(array('tasks' => $tasks, 'tracks' => $tracks));
}

if (isset($_POST['data'])) {
  if (isset($_POST['data']['tasks'])) {
    $tasks = $_POST['data']['tasks'];
    file_put_contents('tasks.json', json_encode($tasks));
  }
  if (isset($_POST['data']['tracks'])) {
    $tracks = $_POST['data']['tracks'];
    file_put_contents('tracks.json', json_encode($tracks));
  }
}
