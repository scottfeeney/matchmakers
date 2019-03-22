<?php

function add_head(){
    $head = <<<HEAD
<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        
        <!-- Project Team members -->
        <meta name="author" content="Blair Fraser">
        <meta name="author" content="Danny Aad">
        <meta name="author" content="Nick Kennedy">
        <meta name="author" content="Scott Feeney">
        <meta name="author" content="Shane West">
        

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" 
              href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" 
              integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" 
              crossorigin="anonymous">
              
        <link rel="stylesheet" href="styles/style.css">

        <title>Job Matcher</title>
    </head>
HEAD;

    echo $head;
}


function display_header(){
    //TODO: Add header here if it's the same on all pages    
}


function display_footer(){
    //TODO: Add footer here if it's the same on all pages
}


function bootstrap_optional(){
    $bs_opt = <<<OPTIONAL
<!-- Bootstrap: jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" 
            integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" 
            crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" 
            integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" 
            crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" 
            integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" 
            crossorigin="anonymous">
    </script>
OPTIONAL;

    echo $bs_opt;
}


?>