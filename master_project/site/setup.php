<?php
/**
 * Build script
 * @author Andrew Ritchie
 */
$htdocs = getenv("htdocs");
$cd = getcwd();
$sqluser = getenv("sqluser");
$sqlpass = getenv("sqlpass");
$sqldb = getenv("sqldb");
$sqlloc = getenv("sqlloc");
$dbready = false;
$_SERVER['DOCUMENT_ROOT'] = $cd;
function file_build_path($name) {
	return str_replace("/",DIRECTORY_SEPARATOR,str_replace("\\",DIRECTORY_SEPARATOR,$name));
	
}
include_once $cd . file_build_path("\config\config.php");
include_once $cd . file_build_path("\services\sql_connection_service.php");
include_once $cd . file_build_path("\services\user_service.php");
include_once $cd . file_build_path("\models\user.php");

class InstallationConfig implements IConfiguration {
    public function sql_server_name() {
        return getenv("sqlloc");
    }
    public function sql_server_user() {
        return getenv("sqluser");
    }
    public function sql_server_password() {
        return getenv("sqlpass");
    }
    public function sql_server_db_name() {
        return $GLOBALS['dbready'] ? getenv("sqldb") : null;
    }
    public function sql_server_db_port() {
        return 3306; //default
    }
}

$conn = SQLConnectionService::get_instance(new InstallationConfig());
$conn->connect();
//Note: Can't use prepared statements for creating databases. As we expect the user to already have admin rights, risk of SQL injection is minimal.
$conn->execute_query("DROP DATABASE IF EXISTS " . $sqldb . ";"); 
$conn->execute_query("CREATE DATABASE " . $sqldb . ";");
echo "Database created" . PHP_EOL;
//Generate a random password
$allowedchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890";
$password = "";
for ($i=0;$i<15;$i++) {
    $password = $password . $allowedchars[mt_rand(0,strlen($allowedchars))];
}
//Create the new User
$conn->execute_query("DROP USER IF EXISTS soduser");
$conn->execute_query("CREATE USER soduser IDENTIFIED BY '" . $password . "'");
$conn->execute_query("GRANT SELECT, INSERT, UPDATE, DELETE ON " . $sqldb . ".* TO soduser");
echo "User created" . PHP_EOL;
$conn->disconnect();
$dbready = true;
//Reconnect to the new Database
$conn->connect();
$query = file_get_contents($cd . '/database/create_tables.sql');
$conn->execute_multiple($query);
echo "Tables created" . PHP_EOL;
$user_service = new UserService($conn);
$user = new User(null, getenv("adminuser"), User::USER_TYPE_ADMINISTRATOR);
$user_service->create_user($user,getenv("adminpass"),null,null,null,null,null,null);
$conn->disconnect();

function delete_folder($directory) {
    $files = glob($directory . "/*");
    foreach ($files as $file) {
        if (is_dir($file)) {
            delete_folder($file);
        }
        else {
            echo "Deleting ". $file . PHP_EOL;
            unlink($file);
        }
    }
    
    if ($directory != getenv("htdocs")) {
        echo "Deleting " . $directory  . PHP_EOL;
        rmdir($directory);
    }
    
}
delete_folder($htdocs);

$dirs_to_copy = array("", "images", "misc", "models", "services");
foreach ($dirs_to_copy as $dir) {
    $newdir = $htdocs . '/' . $dir;
    if (strlen($dir) > 0) {
        echo "Creating: " . $newdir . PHP_EOL;
        mkdir($newdir);
    }
    $files = glob($cd . "/" . $dir . "/*.{php,png,PNG,html,css}", GLOB_BRACE);
    foreach ($files as $file) {
        if (!is_dir($file)) {
            echo "Copying " . basename($file) . " to " . $newdir . PHP_EOL;
            copy($file, $newdir . '/' . basename($file));
        }
    }
}

echo "Creating: " . $htdocs . "/config" . PHP_EOL;
mkdir($htdocs . "/config");

echo "Creating configuration file /config/config.php";
$config = file_get_contents($cd . DIRECTORY_SEPARATOR .  DIRECTORY_SEPARATOR . 'configplaceholder.php');
$config = str_replace("serveruser", "soduser", $config);
$config = str_replace("serverpassword", $password, $config);
$config = str_replace("serverdb", $sqldb, $config);
$config = str_replace("servername", $sqlloc, $config);
$configfile = fopen($htdocs . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config.php","w");
fwrite($configfile, $config);
fclose($configfile);

$init = file_get_contents($htdocs . DIRECTORY_SEPARATOR . 'misc' . DIRECTORY_SEPARATOR . 'init.php');
$init = preg_replace('/include_once.*localconfig.php.*/i', '', $init); //Remove reference to local config file.
$init = preg_replace('/\/\/UNCOMMENT.*/i', '', $init); //Remove reference to local config file.
$init = preg_replace('/\$localconfig = Local.*/i', '', $init);

$initfile = fopen ($htdocs . '/misc/init.php','w');
fwrite($initfile, $init);
fclose($initfile);
