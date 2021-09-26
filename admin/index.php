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
        width: 80%;
        margin: auto;
        margin-top: 5%;
      }
    </style>

    <title>Panel administracyjny</title>
  </head>
  <body>
    <?php

    if ( ! empty($_POST) ) {

        if ( ! empty($_GET['register']) ) {

            $hash = password_hash($_POST['pass'], PASSWORD_DEFAULT);
            $client = array ('uname' => $_POST['uname'], 'hash' => $hash);

            $clientJSON = json_encode($client);
            
            $fp = fopen('client.json', 'w');
            fwrite($fp, $clientJSON);
            fclose($fp);

            if ( fopen('client.json', 'r') ) {
                session_start();
                $_SESSION['username'] = $_POST['uname'];
                header("Location: index.php");
            } else {
                $clientFileReadError = 1;
            }

        } else if ( ! empty($_GET['settings']) ) {

          //var_dump($_FILES);

            if ( fopen('../settings.json', 'r') ) { 
              $fp = fopen('../settings.json', 'r');
              $oldSettingsJSON = fgets($fp);
              $oldSettings = json_decode($oldSettingsJSON);
            }

            if ( $_FILES['bgupload']['error'] === 0 ) {
                $uploadPath='resources/' . $_FILES['bgupload']['name'];
                move_uploaded_file($_FILES['bgupload']['tmp_name'], '../' . $uploadPath);
            } else if ( ($_FILES['bgupload']['error'] === 4) || empty($_FILES) ) {
              if ( ! empty($oldSettings->bgimage) ) {
                $uploadPath = $oldSettings->bgimage;
              } else {
                $uploadPath = 'resources/trash_logo.png';
              }
            }

            if ( empty($_POST['pathform']) ) {
              if ( ! empty($oldSettings->path) ) {
                $_POST['pathform'] = $oldSttings->path;
              } else {
                $_POST['pathform'] = $_SERVER['DOCUMENT_ROOT'];
              }
            }

            function writePasswordToSettings($uploadPath) {
              $passwordSecureHash = password_hash($_POST['sitePassword'], PASSWORD_DEFAULT);
              $intSettings = array("path" => $_POST['pathform'], "bgimage" => $uploadPath, "color" => $_POST['textcolor'], "sitepassword" => $passwordSecureHash);

              return $intSettings;
            }

            if ( ! empty($_POST['sitePassword']) ) {

              if ( ! empty($oldSettings->sitepassword) ) {
                  if ( password_verify($_POST['sitePassword'], $oldSettings->sitepassword) ) {
                    $settings = array("path" => $_POST['pathform'], "bgimage" => $uploadPath, "color" => $_POST['textcolor']);
                  } else {
                    $settings = writePasswordToSettings($uploadPath);
                  }
              } else {
                $settings = writePasswordToSettings($uploadPath);
              }
            } else {

              if ( ! empty($oldSettings->sitepassword) ) {
                $settings = array("path" => $_POST['pathform'], "bgimage" => $uploadPath, "color" => $_POST['textcolor'], "sitepassword" => $oldSettings->sitepassword);
              } else {
                $settings = array("path" => $_POST['pathform'], "bgimage" => $uploadPath, "color" => $_POST['textcolor']);
              }

            }

            $settingsJSON = json_encode($settings);

            $fp = fopen('../settings.json', 'w');
            fwrite($fp, $settingsJSON);
            fclose($fp);

            if ( ! fopen('../settings.json', 'r') ) {
                $settingsFileReadError = 1;
            }

        } else {

            if ( fopen('client.json', 'r') ) {

                $fp = fopen('client.json', 'r');
                $clientJSON = fgets($fp);

                $client = json_decode($clientJSON);

                if ( $client->uname === $_POST['uname']) {
                    if ( password_verify($_POST['pass'], $client->hash) ) {
                        session_start();
                        $_SESSION['username'] = $_POST['uname'];
                        header("Location: index.php");
                    } else {
                        echo "<div class=\"alert alert-warning alert-dismissible fade show\" role=\"alert\">
                        Nie poprawne login lub hasło.
                        <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
                          <span aria-hidden=\"true\">&times;</span>
                        </button>
                      </div>";
                    }
                } else {
                    echo "<div class=\"alert alert-warning alert-dismissible fade show\" role=\"alert\">
                    Nie poprawne login lub hasło.
                    <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
                      <span aria-hidden=\"true\">&times;</span>
                    </button>
                  </div>";
                }

            }

        }

    }

    session_start();

    if ( ! empty($_SESSION['username']) ) {

      if ( fopen('../settings.json','r') ) {

        $fp = fopen('../settings.json', 'r');
        $settingsJSON = fgets($fp);

        $settings = json_decode($settingsJSON);

        echo "<h1>Ustawienia</h1>
            <hr />
            <form action=\"index.php?settings=1\" method=\"post\" enctype=\"multipart/form-data\">
                <div class=\"form-group\">
                    <label for=\"pathForm\">Ścieżka:</label>
                    <input id=\"pathForm\" class=\"form-control\" type=\"text\" name=\"pathform\" aria-describedby=\"pathFormHelp\" value=\"" . $settings->path . "\" />
                    <small id=\"pathFormHelp\" class=\"form-text text-muted\">Podaj ścieżkę do katalogu udostępnianego za pomocą Trash.</small>
                </div>
                <div class=\"form-group\">
                    <label for=\"imageUploadForm\">Obraz tła:</label>
                    <img src=\"../" . $settings->bgimage . "\" style=\"width: 20%;\" />
                    <input id=\"imageUploadForm\" class=\"form-control\" type=\"file\" name=\"bgupload\" aria-describedby=\"imageUploadFormHelp\" />
                    <small id=\"imageUploadFormHelp\" class=\"form-text text-muted\">Wybierz obraz tła</small>
                </div>
                <div class=\"form-group\">
                    <label for=\"textColorForm\">Kolor tekstu: </label>
                    <input id=\"textColorForm\" class=\"form-control\" type=\"color\" name=\"textcolor\" aria-describedby=\"textcolorFormHelp\" value=\"" . $settings->color . "\" />
                    <small id=\"textColorFormHelp\" class=\"form-text text-muted\">Wybierz kolor tekstu pasujący do wybranego obrazu tła.</small>
                </div>";
          echo "<div class=\"form-group\">
                    <label for=\"sitePassword\">Zabezpieczenie strony hasłem: </label>
                    <input id=\"sitePassword\" class=\"form-control\" name=\"sitePassword\" type=\"password\" aria-describedby=\"sitePasswordHelp\" />
                    <small id=\"sitePasswordHelp\" class=\"form-text text-muted\">Hasło zabepieczające stronę. Aby je usunąć należy wpisać w powyższe pole obecne hasło.</small>
                <div>";
        
                echo "<button class=\"btn btn-warning\">Zapisz zmiany</button>
            </form>
                ";
      } else {

        echo "<h1>Ustawienia</h1>
            <hr />
            <form action=\"index.php?settings=1\" method=\"post\" enctype=\"form-data/multipart\">
                <div class=\"form-group\">
                    <label for=\"pathForm\">Ścieżka:</label>
                    <input id=\"pathForm\" class=\"form-control\" type=\"text\" name=\"pathform\" aria-describedby=\"pathFormHelp\" />
                    <small id=\"pathFormHelp\" class=\"form-text text-muted\">Podaj ścieżkę do katalogu udostępnianego za pomocą Trash.</small>
                </div>
                <div class=\"form-group\">
                    <label for=\"imageUploadForm\">Obraz tła:</label>
                    <input id=\"imageUploadForm\" class=\"form-control\" type=\"file\" name=\"bgupload\" aria-describedby=\"imageUploadFormHelp\" />
                    <small id=\"imageUploadFormHelp\" class=\"form-text text-muted\">Wybierz obraz tła</small>
                </div>
                <div class=\"form-group\">
                    <label for=\"textColorForm\">Kolor tekstu: </label>
                    <input id=\"textColorForm\" class=\"form-control\" type=\"color\" name=\"textcolor\" aria-describedby=\"textcolorFormHelp\" />
                    <small id=\"textColorFormHelp\" class=\"form-text text-muted\">Wybierz kolor tekstu pasujący do wybranego obrazu tła.</small>
                </div>
                <div class=\"form-group\">
                    <label for=\"sitePassword\">Zabezpieczenie strony hasłem: </label>
                    <input id=\"sitePassword\" class=\"form-control\" name=\"sitePassword\" type=\"password\" aria-describedby=\"sitePasswordHelp\" />
                    <small id=\"sitePasswordHelp\" class=\"form-text text-muted\">Hasło zabepieczające stronę</small>
                <div>
                <button class=\"btn btn-success\">Zapisz ustawienia</button>
            </form>
                ";

        if ( ! empty($settingsFileReadError) ) {
          echo "<div class=\"alert alert-warning alert-dismissible fade show\" role=\"alert\">
                Wystąpił problem z zapisaniem danych do pliku.
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
                  <span aria-hidden=\"true\">&times;</span>
                </button>
              </div>";
        }

      }

    } else {

        if ( ! fopen('client.json', 'r') ) {
            echo "<h1>Rejestracja</h1>";
            echo "<hr />
                    <form action=\"index.php?register=1\" method=\"post\">";
        } else {
            echo "<h1>Logowanie</h1>";
            echo "<hr />
                    <form action=\"index.php?login=1\" method=\"post\">"; 
        }

        echo "<div class=\"form-group\">
                <label for=\"username\">Nazwa użytkownika</label>
                <input id=\"username\" class=\"form-control\" type=\"text\" name=\"uname\" aria-describedby=\"usernameHelp\" />
                <small id=\"usernameHelp\" class=\"form-text text-muted\">Wpisz nazwę użytkownika.</small>
            </div>
            <div class=\"form-group\">
                <label for=\"password\">Hasło</label>
                <input id=\"password\" class=\"form-control\" type=\"password\" name=\"pass\" aria-describedby=\"passwordHelp\" />
                <small id=\"passwordHelp\" class=\"form-text text-muted\">Wpisz hasło.</small>
            </div>
            <button class=\"btn btn-success\">Zaloguj się</button>
            </form>";

        if ( ! empty($clientFileReadError) ) {
          echo "<div class=\"alert alert-warning alert-dismissible fade show\" role=\"alert\">
                Wystąpił problem z zapisaniem danych do pliku.
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
                  <span aria-hidden=\"true\">&times;</span>
                </button>
              </div>";
        }

        if ( ! empty($loginerror) ) {
           echo "
           <div class=\"alert alert-warning alert-dismissible fade show\" role=\"alert\">
           Nie poprawna nazwa użytkownika lub hasło.
           <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
             <span aria-hidden=\"true\">&times;</span>
           </button>
         </div>
           "; 
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