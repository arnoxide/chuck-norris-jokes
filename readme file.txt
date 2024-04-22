Welcome, and glad that you made it thus far

have Git, Docker Desktop, Text editor(preferable Vs Code) installed
open folder u want to run your project
run cmd on that folder, 
run this commands "git clone https://github.com/arnoxide/chuck-norris-jokes.git"
cd chuck-norris-jokes
code .

the project will open on vscode
install docker extension first
go to terminal >> new terminal
run "docker-compose up" (take few minutes)

check docker desktop(make sure you are signed in) if the project is creating
after its completion on docker desktop you'll find 3 task running

click php:apache
open in terminal and run
"docker-php-ext-install mysqli"
NB: if the above code not run you'll encounter the following error>>
"Fatal error: Uncaught Error: Class "mysqli" not found in /var/www/html/config.php:2 Stack trace: #0 /var/www/html/index.php(3): require_once() #1 {main} thrown in /var/www/html/config.php on line 2"
after that restart the project on docker

last steps
go to any web browser(reccomend Google Chrome)"
run localhost

it will direct you to login page, create an account
login

homepage
choose category
get joke
if you like the joke, addto favorite

additionally
if you want to view the database run "localhost:8001"
username:php_docker
password:password

other than that, have fun..........adios
for any enquieris or encoutering errors kindly send an email to masutha.a@arnoldmasutha.co.za


