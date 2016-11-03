<?php
require_once 'build.class.php';
$resolvers = array(
    'type'
);
$builder = new siteBuilder('NoHtmlUrls', '1.0.0', 'beta', $resolvers);
$builder->build();
