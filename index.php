<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Password Generator</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        function addNew() {
            var usercount = document.querySelectorAll('#inputarea .username').length;
            var container = document.getElementById("inputarea");
            var input = document.createElement("input");
                input.type = "text";
                input.classList.add("username");
                input.name = "data["+usercount +"][user]";
                input.placeholder = "Username";
                container.appendChild(input);
            var inputPw = document.createElement("input");
                inputPw.type = "text";
                inputPw.classList.add("password");
                inputPw.name = "data["+usercount+"][pw]" ;
                inputPw.placeholder = "Password";
                container.appendChild(inputPw);
                container.appendChild(document.createElement("br"));                
        }
    </script>
</head>
<body>    
<h1>Password Generator Tool</h1>
<p>Only modified passwords will be updated.</p>
<form name="pws" method="POST">
    <?php
    //location of htpasswd
    //define("HTPWD",     "");   
    $currentpath = getcwd();     
    define("HTPWD", $currentpath."/.htpasswd");
    echo(HTPWD);    
    /**
     * Read the htpasswd file
     */
    function readHtpasswd()
    {
        $userdata = array();
        if(!file_exists(HTPWD))
            return $userdata;
        $content =  file_get_contents (HTPWD);
        $clines = preg_split ('/$\R?^/m', $content);
        $cnt = 0;
    
        foreach ($clines as $line) {
            $ls = explode(":", $line);
            $userdata[$cnt]["user"]= $ls[0];
            $userdata[$cnt]["pw"]= $ls[1];
            $cnt++;
        }
        return $userdata;
    }
    //load em in
    $users = readHtpasswd();

    //path which is used when the data is saved
    if(!empty($_POST["save"]) && $_POST["save"] == 1){

        $postuser = array();
        if(is_array($_POST["data"])){
            $postuser = $_POST["data"];
        }

        $htpwdata = "";
        $ucnt = 0;
        $allcnt = count($postuser)-1;
        foreach ($postuser as $puser) {
            //update            
            if(empty($puser["pw"]) && ($users[$ucnt]["user"] == $puser["user"])){                   
                //use read hash
                $puser["pw"] = $users[$ucnt]["pw"];
            }else{           
                //new
                $puser["pw"] = crypt($puser["pw"]);
            }
            $htpwdata .= $puser["user"].":".$puser["pw"];
           
            $htpwdata .= ($ucnt < $allcnt) ? "\n":"";
            $ucnt++;
        }
        file_put_contents(HTPWD, $htpwdata);
        $users = readHtpasswd();
    }
    ?>

    <div id="inputarea">
        <?php $u = 0; foreach ($users as $user) { ?>
            <input class="username" type="text" name="data[<?=$u?>][user]" value="<?=$user['user']?>" placeholder="Username">
            <input type="password" name="data[<?=$u?>][pw]" value="" placeholder="New Password"><br>
        <?php $u++;}?>
    </div>

    <button id="newButton">New</button>
    <input type="hidden" name="save" value="1">
    <input type="submit" value="update" >

</form>

<script>
    document.getElementById("newButton").addEventListener("click", function(event){
        event.preventDefault();
        addNew();
    });
</script>
</body>
</html>