<?php
$version = '3.2.8';

// We set versions for both slugs to avoid undefined index errors for free slug
$GLOBALS['aws_meta']['amazon-s3-and-cloudfront-pro']['version'] = $version;
$GLOBALS['aws_meta']['amazon-s3-and-cloudfront']['version']     = $version;