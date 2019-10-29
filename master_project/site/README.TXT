-----Production Installation of Scheduling OnDemand Software-----

***REQUIREMENTS***

Option A:
  1) A Windows 8 Server or Higher Environment, with Admin credentials
  2) A Bitnami WAMPStack Installation (https://bitnami.com/stack/wamp)
Option B:
  1) A Linux Distribution that supports Bitnami LAMPStack, with sudo-level credentials
  2) A Bitnami LAMPStack Installation (https://bitnami.com/stack/lamp)
Option C:
  1) A Cloud MySQL Service with a Database created
  2) A user created on your MySQL service with the ability to create table,  
  3) A Cloud Serverless Web Application (i.e. Azure Web App)
Option D (NOT RECOMMENDED):
  1) An Apache HTTP Server 2.4.x Installation configured to use PHP 7.x
  2) An MySQL 8.x Server with credentials available to Create/Drop databases and Add new users/Grant users permissions.
  3) Either a Windows 8 or Linux environment with admin credentials.

Installation instructions for the Cloud still in progress

***WARNING***

Installation of Scheduling OnDemand will delete ALL DOCUMENTS IN THE 
APACHE2 HTDOCS FOLDER. This will be the only application on the website.

The database you provide will also be DELETED.

***LOCAL INSTALLATION***

1a) If on Windows, open the command line in the folder of this file and enter the command "install-windows.bat"
1b) If on Linux, open the command line in the folder of this file and enter the command "sudo bash install-linux.sh"
2) Follow the prompts, providing:
  a) Whether the installation is a Bitnami installation
     - If so, providing the directory of Bitnami
     - Otherwise, providing the directory of php.exe or /bin/php, and the htdocs directory of Apache
  d) Admin User Name and Password for MySQL
  e) A Database Name to use. If this database already exists, it will be dropped.
  f) The URL of the SQL server (likely localhost if Bitnami)
  g) An Email Address and Password to use to log in as an administrator on in Scheduling OnDemand
  h) Whether to include Unit Tests or not, to verify the system works.
    - If unit tests are to be included, the following occurs:
      * An additional website is installed at /test/tests.html that test 
        individual pieces of functionality
      * An additional database, 'testdb', is created
3) Type "confirm" at the final prompt to confirm all details are correct.
4) Your application will now install. Review the output for errors, 
   and log into the website your Apache installation is installed at, 
   using the credentials provided to the script.

***CLOUD INSTALLTION***
1a) If on Windows, open the command line in the folder of this file and enter the command "install-windows.bat"
1b) If on Linux, open the command line in the folder of this file and enter the command "sudo bash install-linux.sh"
2) Type Y to state that the installation is a cloud installation
3) Open the file dist/config/config.php in the editor of your choice.
4) Change the text "servername" to the URL of your MySQL server
5) Change the text "serveruser" to the log in user of your MySQL server
6) Change the text "serverpassword" to the password of your MySQL server user
7) Change the text "serverdb" to the database name of your MySQL server
8) Change "3306" to the port of your MySQL server
9) Save the file
10) Open the file dist/misc/init.php
11) Delete line 25.
12) Upload the contents of the dist folder to the root level of your serverless web application as per your cloud service's instructions
13) Navigate to /firsttimesetup.php on your new application and follow the instructions provided.