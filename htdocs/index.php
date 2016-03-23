<?php

$wanted_file = "";
if (isset($_REQUEST["file"])) {
  $wanted_file = $_REQUEST["file"];
}

$path = "./data/";
$content = "";
$files = array();

$dir_iterator = new RecursiveDirectoryIterator($path);
$iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);

foreach ($iterator as $file) {
  if (is_file($file)) {
    $files[] = "" . $file;
  }
}

if ($wanted_file != "") {
  // lacy security check
  if (!in_array($wanted_file, $files)) {
    die('-ERR');
  }
}

if (isset($_REQUEST["save"])) {
  if ($_REQUEST["new"] != "") {
    if (substr($_REQUEST["new"], 0, 1) == "/") {
      // new folder ???!?
    } else {
      // new file
      $new_file = dirname($_REQUEST["file"]) . "/" . $_REQUEST["new"] . ".md";
      $fp = fopen($new_file, "w");
      $wanted_file = $new_file;
      fclose($fp);
      $files[] = $new_file;
    }
  } else {
    if ($_REQUEST["wanted_file"] == $_REQUEST["file"]) {
      if ($_REQUEST["content"] != "") {
        // save content to wanted_file
        $fp = fopen($wanted_file, "w");
        fwrite($fp, $_REQUEST["content"]);
        fclose($fp);
      } else {
        // delete
        unlink($wanted_file);
        $files = array_diff($files, array($wanted_file));
      }
    } else {
      // load new content
    }
  }
  // print_r($_REQUEST);
  // die();
}

sort($files);

if ($wanted_file == "") {
  $wanted_file = "" . $files[0];
}
if (is_file($wanted_file)) {
  $content = file_get_contents($wanted_file);
}

?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <script type="text/javascript" src="marked.js"></script>
    <script type="text/javascript" src="editor.js"></script>
    <link rel="stylesheet" href="editor.css" />
    <title>Editor</title>
    <style type="text/css">
      body {
        padding: 0;
        margin: 0;
        background-color: #f9f9f7;
      }
      .editor-wrapper {
        max-width: 680px;
        padding: 10px;
        margin: 60px auto;
      }
    </style>
  </head>
<body>
    <div class="editor-wrapper">
      <form action="" method="post">
        <input type="hidden" name="wanted_file" value="<?php echo $wanted_file; ?>">
        <input type="text" name="new">
        <select name="file" onchange="submit();">
          <option value="" disabled>---</option>
          <?php
            foreach($files as $file) {
              $selected = "";
              if ($file == $wanted_file) {
                $selected = "selected";
              }
              echo "<option " . $selected . " value=\"" . $file . "\">" . str_replace($path, "", $file) . "</option>\n";
            }
          ?>
        </select>
        <input type="submit" name="save" value="save">
        <br/>
        <textarea id="editor" name="content"><?php echo $content; ?></textarea>
      </form>
    </div>
    <script type="text/javascript">
      var editor = new Editor({
      });
      editor.render();
    </script>
  </body>
</html>
