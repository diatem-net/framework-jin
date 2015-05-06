<?php

include '%JINROOT%launcher.php';
use jin\filesystem\PublicSecuredFile;
use jin\log\Debug;
use jin\context\HttpHeader;

if(!isset($_REQUEST['path']) || !isset($_REQUEST['k'])){
    echo '404 - Paramètres manquants';
    HttpHeader::return404('404 - Paramètres manquants');
    exit;
}

$psf = new PublicSecuredFile($_REQUEST['path'], $_REQUEST['k']);

if(!$psf->isValid()){
    echo '404 - '.$psf->getLastError();
    HttpHeader::return404('404 - '.$psf->getLastError());
    exit;
}

if(isset($_REQUEST['d'])){
    $psf->forceDownload();
}else{
    $psf->renderInOutput();
}
