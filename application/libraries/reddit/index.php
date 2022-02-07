<?php 

require_once("reddit.php");

$postReddit = new reddit();


$ceratePost = $postReddit->createStory("Fun","https://youtu.be/gFmHPFlB42c","fun");

