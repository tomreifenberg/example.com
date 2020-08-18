<?php
require '../../config/keys.php';
require '../../core/db_connect.php';

// Get the post
$get = filter_input_array(INPUT_GET);
$id = $get['id'];

$stmt = $pdo->prepare("SELECT * FROM posts WHERE id=:id");
$stmt->execute(['id'=>$id]);
$row = $stmt->fetch();

//If the id cannot be found kill the request
if(empty($row)){
    http_response_code(404);
    die('Page Not Found <a href="/">Return Home</a>');
  }

//var_dump($row);

$meta=[];
$meta['title']= "Edit: {$row['title']}";

// Update the post
$message=null;

//strips HTML, 'body' is NULL FILTER
$args = [
    'title'=>FILTER_SANITIZE_STRING,
    'meta_description'=>FILTER_SANITIZE_STRING,
    'meta_keywords'=>FILTER_SANITIZE_STRING,
    'body'=>FILTER_UNSAFE_RAW
];

$input = filter_input_array(INPUT_POST, $args);

if(!empty($input)){

    //Strip white space, begining and end
    $input = array_map('trim', $input);

    //Allow only whitelisted HTML
    $input['body'] = cleanHTML($input['body']);

    //Create the slug
    $slug = slug($input['title']);

    //Sanitized insert
    $sql = 'UPDATE posts 
    SET title=:title,
        slug=:slug,
        body=:body,
        meta_description=:meta_description,
        meta_keywords=:meta_keywords
    WHERE id=:id';

    if($pdo->prepare($sql)->execute([
        $input['title'],
        $slug,
        $input['body'],
        $input['meta_description'],
        $input['meta_keywords'],
        $input['id']
    ])){
       header('LOCATION:view.php?slug='. $row['slug']);
    }else{
        $message = 'Something bad happened';
    }
}


//Code below is from add.php for all the SQL content (if-else statement), for reference

// if(!empty($input)){

//     $input = array_map('trim', $input);

//     $slug = preg_replace(
//         "/[^a-z0-9-]+/",
//         "-",
//         strtolower($input['title'])
//     );

//     $sql = 'INSERT INTO posts SET id=uuid(), title=?, slug=?, body=?';
//     if($pdo->prepare($sql)->execute([
//         $input['title'],
//         $slug,
//         $input['body']
//     ])){
//        header('LOCATION:/posts');
//     }else{
//         $message = 'Something bad happened';
//     }
// }

$content = <<<EOT
<h1>Edit Post {$row['title']}</h1>
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
    <input type="reset" value="Undo Changes" class="btn btn-secondary">
</div>
</form>
EOT;

include '../../core/layout.php';
