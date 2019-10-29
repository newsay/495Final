#!/bin/bash
echo "Is this a Cloud installation? Y for yes, anything else for no: "
read cloud;
if [ "$cloud" == 'y' ]
then
  $cloud='Y'
fi
if [ "$cloud" == 'Y' ]
then
rm -rf dist
mkdir dist
mkdir dist/images
cp images/*.* dist/images
mkdir dist/misc
cp misc/*.* dist/misc
mkdir dist/models
cp models/*.* dist/models
mkdir dist/services
cp services/*.* dist/services
mkdir dist/config
cp configplaceholder.php dist/config/config.php
cp *.php dist
cp *.css dist
echo "Read README.TXT to complete installation"
else
echo "Are you using Bitnami WAMPStack? (Y/N) ";
read bitnami;
if [ "$bitnami" == 'y'  ]
then
	bitnami='Y';
fi
if [ "$bitnami"  == 'n'  ]
then
	bitnami='N';
fi

if [ "$bitnami"  == 'Y'  ]
then
	
  echo "Input Bitnami LAMPStack directory (i.e. /home/username/lampstack-7.2.19-2) ";
  read bitnamidir;
  htdocs="$bitnamidir/apache2/htdocs";
  phploc="$bitnamidir/php/bin/php";
  echo Bitnami Directory: $bitnamidir;
 
elif [ "$bitnami" == 'N' ]
then
	
  echo "Input PHP executable file location php: ";
  read phploc;
  echo "Input htdocs folder location: ";
  read htdocs;

else
  echo "Invalid input";
  exit 1;
fi
echo "Input MySQL Admin User Name: ";
read sqluser;
echo "Input MySQL Admin Password: ";
read sqlpass;
echo "Input new MySQL Database Name:";
read sqldb;
echo "Input SQL url (i.e. localhost): ";
read sqlloc;
echo "Input Scehduling OnDemand Admin Email Address: ";
read adminuser;
echo "Input Scehduling OnDemand Admin Password: ";
read adminpass;
echo "Include Unit Tests? Y for yes, anything else for no: "
read unittests
echo Current location: $cd;
echo php.exe location: $phploc;
echo htdocs location: $htdocs;
echo The following actions will be performed:;
echo All files in htdocs will be removed and replaced with Scheduling OnDemand;
echo The database $sqldb will be created.;
echo A new MySQL user, "soduser", will be created with a random password.;
echo The MySQL user, "soduser", will be granted Read and Write access to $sqldb;
if [ "$unittests" == 'y'  ]
then
	unittests='Y';
fi
if [ "$unittests"  == 'Y'  ]
then
  echo The database testdb will be created.;
  echo The MySQL user, "soduser", will be granted Read and Write access to $sqldb;
fi
echo "Type 'confirm' to confirm: ";
read confirmation;
if [ "$confirmation" == "confirm" ]
then
	export sqluser;
	export sqlpass;
	export sqldb;
	export adminuser;
	export adminpass;
	export htdocs;
    echo Starting installation.;
    $phploc ./setup.php;

else
    echo Cancelled installation of Scheduling OnDemand;

fi
fi