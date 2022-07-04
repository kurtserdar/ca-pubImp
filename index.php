<!doctype html>
<html lang="tr">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="pragma" content="no-cache" />

    <!-- Style -->
    <link rel="stylesheet" href="fonts/icomoon/style.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Style -->
    <link rel="stylesheet" href="css/style.css">

    <title>Public Key Importer</title>
  </head>
  <body>

  <div class="content">
    <div class="container">
      <div class="row align-items-stretch justify-content-center no-gutters">
        <div class="col-md-7">
          <div class="form h-100 contact-wrap p-5">
            <h3 class="text-center">Public Key Importer</h3>
            <form class="mb-5" method="post" id="pubKeyForm" name="pubKeyForm">
              <div class="row">
                <div class="col-md-6 form-group mb-3">
                  <label for="" class="col-form-label">Username *</label>
                  <input type="text" class="form-control" name="username" id="username" placeholder="" required>
                </div>
                <div class="col-md-6 form-group mb-3">
                  <label for="" class="col-form-label">Password *</label>
                  <input type="password" class="form-control" name="password" id="password" placeholder="" required>
                </div>

                <div class="col-md-6 form-group mb-3">
                <?php
                // bir takım post işleri
                if ($_POST){
                    $username = $_POST['username'];
                    $password = $_POST['password'];
                    $CAlogonUrl = "https://PVWAURL/PasswordVault/API/auth/LDAP/Logon";
                    $CAlogoffUrl = "https://PVWAURL/PasswordVault/API/Auth/Logoff";
                    $data = array("username" => $username, "password" => $password);

                    $data_string = json_encode($data);

                    $ch = curl_init($CAlogonUrl);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data_string))
                    );

                    $result = curl_exec($ch);
                    curl_close($ch);

                    // kullanıcı kendisini doğrularsa
                    if (strpos($result, 'PASWS013E') == false) {

                        $CAlogonUrlLcl = "https://PVWAURL/PasswordVault/API/auth/Cyberark/Logon";

                        $data2 = array("username" => "user", "password" => "pass");
                        $data_string2 = json_encode($data2);

                        $ch2 = curl_init($CAlogonUrlLcl);
                        curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "POST");
                        curl_setopt($ch2, CURLOPT_POSTFIELDS, $data_string2);
                        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($data_string2)));


                        $tokenUnStrim = curl_exec($ch2);
                        $tokenUnStrim = ltrim($tokenUnStrim, '"');
                        $token = rtrim($tokenUnStrim, '"');


                        $pubSSHKey = $_POST['pubKey'];
                        $CAaddSSHKeyUrl = "https://PVWAURL/PasswordVault/WebServices/PIMServices.svc/Users/" . $username . "/AuthenticationMethods/SSHKeyAuthentication/AuthorizedKeys";

                        $jsonData1_get_r = array(
                            'PublicSSHKey'=> $pubSSHKey
                            );

                        $jsonDataEncoded_get  = json_encode($jsonData1_get_r );
                        $ch_get = curl_init($CAaddSSHKeyUrl);
                        curl_setopt($ch_get, CURLOPT_CUSTOMREQUEST, "POST");
                        curl_setopt($ch_get, CURLOPT_POSTFIELDS, $jsonDataEncoded_get);
                        curl_setopt($ch_get, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch_get, CURLOPT_HTTPHEADER, array(
                            'Content-Type: application/json',
                            'Authorization: ' . $token));

                        $result_get = curl_exec($ch_get);
                        $ARRAY = json_decode($result_get,true);
                        curl_close($ch_get);

                    }
                    // yanlış giriş
                    else {
                        // Code

                    }
                }
                else {
                    //Code
                }
                ?>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12 form-group mb-3">
                </div>
              </div>

              <div class="row mb-5">
                <div class="col-md-12 form-group mb-3">
                  <label for="message" class="col-form-label">Public Key *</label>
                  <textarea class="form-control" name="pubKey" id="pubKey" cols="30" rows="4"  placeholder="" required></textarea>
                </div>
              </div>
              <div class="row justify-content-center">
                <div class="col-md-5 form-group text-center">
                  <input type="submit" value="Submıt" class="btn btn-block btn-primary rounded-0 py-2 px-4">
                  <span class="submitting"></span>
                </div>
              </div>
            </form>
            <div></div>

            <div>
              <?php
              if ($_POST and $result_get){ ?>
               <div class="alert alert-success" role="alert">
                <?php echo ("Public SSH Key has been imported.<br>KEY ID: ");
                echo $ARRAY["AddUserAuthorizedKeyResult"]["KeyID"];
                ?>
              </div>
              <?php
            }   if ($_POST and strpos($result, 'PASWS013E') == true){ ?>
              <div class="alert alert-danger" role="alert">
                <?php echo ("Authentication failure!"); ?>
              </div>
              <?php
            }
              ?>
              <div class="row mb-5">
                <div class="col-md-12 form-group mb-3">
                <label for="message" class="col-form-label"></label>
                 <small><a href="/download/information.pdf" target="_blank">PDF</a></small>
                </div>
            </div>
          </div>
        
        </div>
        <p><center><small>infosec@acme.com</small></center></p>
      </div>
      
    </div>
    
  </div>


    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script src="js/main.js"></script>

  </body>
</html>