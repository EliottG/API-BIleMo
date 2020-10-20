<h1>How to install</h1>

<h3>What you'll need</h3>
<ul>
  <li>Symfony v5</li>
  <li>PHP >= 7.4.9</li>
  <li>Composer</li>
</ul>

<h3>Step 1</h3>
Git clone in your own directory
<h3>Step 2</h3>
Composer install - To download dependencies
<h3>Step 3</h3>
Create an env.local file to modify your database
<h3>Step 4</h3>
To install your database + fixtures
<ul>
  <li>php bin/console doctrine:database:create</li>
  <li>php bin/console doctrine:schema:update -f</li>
  <li>php bin/console doctrine:fixtures:load -n</li>
</ul>
<h3>Step 5</h3>
Go to "yourlocalhost/api" to watch the full documentation of the api
