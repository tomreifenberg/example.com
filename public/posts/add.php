<?php
require '../../config/keys.php';
require '../../core/db_connect.php';

$args = [
    'title'=>FILTER_SANITIZE_STRING,
    'meta_description'=>FILTER_SANITIZE_STRING,
    'meta_keywords'=>FILTER_SANITIZE_STRING,
    'body'=>FILTER_UNSAFE_RAW
];

$input = filter_input_array(INPUT_POST, $args);

if(!empty($input)){

    $input = array_map('trim', $input);

    $slug = preg_replace(
        "/[^a-z0-9-]+/",
        "-",
        strtolower($input['title'])
    );

    $sql = 'INSERT INTO posts SET id=uuid(), title=?, slug=?, body=?';
    if($pdo->prepare($sql)->execute([
        $input['title'],
        $slug,
        $input['body']
    ])){
       header('LOCATION:/posts');
    }else{
        $message = 'Something bad happened';
    }
}

$content = <<<EOT
<h1>Add a New Post</h1>
<form method="post">

<div class="form-group">
    <label for="title">Title</label>
    <input id="title" name="title" type="text" class="form-control">
</div>

<div class="form-group">
    <label for="body">Body</label>
    <textarea id="body" name="body" rows="8" class="form-control"></textarea>
</div>

<div class="row">
    <div class="form-group col-md-6">
        <label for="meta_description">Description</label>
        <textarea id="meta_description" name="meta_description" rows="2" class="form-control"></textarea>
    </div>

    <div class="form-group col-md-6">
        <label for="meta_keywords">Keywords</label>
        <textarea id="meta_keywords" name="meta_keywords" rows="2" class="form-control"></textarea>
    </div>
</div>

<div class="form-group">
    <input type="submit" value="Submit" class="btn btn-primary">
</div>
</form>
EOT;

include '../../core/layout.php';
