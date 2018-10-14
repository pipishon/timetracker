<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credential: true');
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers");

$tasks = json_decode(file_get_contents('tasks.json'), true);
$tracks = json_decode(file_get_contents('tracks.json'), true);
echo json_encode(array('tasks' => $tasks, 'tracks' => $tracks));

$post = json_decode(file_get_contents('php://input'), true);

if (isset($post['tasks'])) {
  $tasks = $post['tasks'];
  file_put_contents('tasks.json', json_encode($tasks));
}
if (isset($post['tracks'])) {
  $tracks = $post['tracks'];
  file_put_contents('tracks.json', json_encode($tracks));
}
