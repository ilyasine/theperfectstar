<?php
// This example illustrates how to extract text content from a webpage
include_once '../HtmlWeb.php';
use LearnDash\Certificate_Builder\simplehtmldom\HtmlWeb;

$doc = new HtmlWeb();
echo $doc->load('https://www.google.com/')->plaintext;
