<?php
echo "hello world";
$nationid='3969900145701';
$json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/people?apiKey=6QxfLc4uRn3vWrlgzsWtzTXBW7CYVsQv&q={"nationid":"'.$nationid.'"}');
