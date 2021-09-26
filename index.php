<?php
  if ( fopen('settings.json', 'r') ) {
    $fp = fopen('settings.json', 'r');
    $settingsJSON = fgets($fp);
    $settings = json_decode($settingsJSON);
  } else {
    $settings = new StdClass();
    $settings->path = '.';
    $settings->bgimage = 'resources/trash_logo.png';
    $settings->color='black';
  }
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

    <style>
      body {
        <?php 
          if ( ! empty($settings) ) {
            echo "background: url('" . $settings->bgimage . "') no-repeat center center fixed;";
          }
        ?>
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
        <?php
        if ( ! empty($settings) ) {
          echo "color: " . $settings->color . ";";
        } else {
          echo "color: black;";
        }
        ?>
        width: 80%;
        margin: auto;
        margin-top: 5%;
      }
     a {
       text-decoration: underline;
       <?php 
        if ( ! empty($settings) ) {
          echo "color: " . $settings->color . ";";
        }
        ?>
      font-weight: bold;
     }

    </style>

    <title>Trash</title>
  </head>
  <body>

    <h1>Index of <?php 
        if ( ! empty($_GET['path']) ) {
          echo $_GET['path'];
        } else {
          echo $settings->path;
        } 
      ?></h1>
      <hr />
  <?php

  function listFiles($path, $pd, $basepath) {
    
    $dir = scandir($path);

 
    echo "<div>";

    if ( $path !== $basepath ) {
      //substr($pd, 0, (strlen($pd) - (count(basename($pd)) + 1)))
      echo "<a href=\"index.php?path=" . $pd . "&pd=" . substr($pd, 0, (strlen($pd) - (strlen(basename($pd)) + 1))) . "\">PARRENT DIRECTORY</a>";
    }

    echo "<ul style=\"list-style-type: none;\">";

    for ( $i=2; $i < count($dir); $i++ ) {
      if ( (filetype($path . '/' . $dir[$i]) === 'dir') && ( $dir[$i] !== ".." ) ) {
        echo "<li class=\"dir\"><img src=\"resources/folder.png\" />&nbsp;<a href=\"index.php?path=" . $path . "/" . $dir[$i] . "&pd=" . $path . "\">" . $dir[$i] . "</a></li>";
      } else {
        echo "<li class=\"file\"><img src=\"resources/document.png\" />&nbsp;
        <a href=\"index.php?path=". $path . "&pd=" . $pd . "&link=" . $path . "/" . $dir[$i] . "\">" . $dir[$i] . "</a></li>";
      }
    }

    echo "</ul></div>";

  } 

      if ( ! empty($settings->sitepassword) ) {

        if ( ! empty($_POST) ) {

          if ( ! empty($_GET['siteprotected']) ) {
            if ( password_verify($_POST['SPPasswd'], $settings->sitepassword) ) {
              session_start();
              $_SESSION['siteunlocked']=1;
              header("Location: index.php");
            } else {
              echo "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">
                Nie poprawne hasło.
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
                  <span aria-hidden=\"true\">&times;</span>
                </button>
              </div>";
            }
          }

        }

        session_start();
        if ( ! empty($_SESSION['siteunlocked']) ) {
          if ( ! empty($_GET['link']) ) {
            if ( symlink($_GET['link'], 'links/' . basename($_GET['link'])) ) {
                header("Location: links/" . basename($_GET['link']));
            } else {
              unlink('links/' . basename($_GET['link']));
              if ( symlink($_GET['link'], 'links/' . basename($_GET['link'])) ) {
                header("Location: links/" . basename($_GET['link']));
              } else {
              echo "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">
                Nie można stworzyć dowiązania symbolicznego do pliku.
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
                  <span aria-hidden=\"true\">&times;</span>
                </button>
              </div>";
              }
            }
          }

          if ( ! empty($_GET['path']) ) {
            listFiles($_GET['path'], $_GET['pd'], $settings->path);
          } else {
            listFiles($settings->path, $settings->path, $settings->path);
          }

          

        } else {

          echo "<form action=\"index.php?siteprotected=1\" method=\"post\">
            <div class=\"form-group\">
              <label for=\"siteProtecedPassword\">Hasło: </label>
              <input id=\"siteProtectedPassword\" class=\"form-control\" type=\"password\" name=\"SPPasswd\" aria-describedby=\"SPPasswdHelp\" />
              <small id=\"SSPasswdHelp\" class=\"form-text text-muted\">Wpisz hasło aby zobaczyć zawartość strony</small>
            </div>
            <button class=\"btn btn-success\">Wejdź</button>
            </form>";
        }

      } else {

        if ( ! empty($_GET['link']) ) {
          if ( symlink($_GET['link'], 'links/' . basename($_GET['link'])) ) {
              header("Location: links/" . basename($_GET['link']));
          } else {
            unlink('links/' . basename($_GET['link']));
              if ( symlink($_GET['link'], 'links/' . basename($_GET['link'])) ) {
                header("Location: links/" . basename($_GET['link']));
              } else {
              echo "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">
                Nie można stworzyć dowiązania symbolicznego do pliku.
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
                  <span aria-hidden=\"true\">&times;</span>
                </button>
              </div>";
            }
          }
        }

        if ( ! empty($_GET['path']) ) {
          listFiles($_GET['path'], $_GET['pd'], $settings->path);
        } else {
          listFiles($settings->path, $settings->path, $settings->path);
        }
        
      }

  ?>
    

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
  </body>
</html>